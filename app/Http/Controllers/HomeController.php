<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ClassSchedule;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Instructor;
use App\Models\LoyalPoint;
use App\Models\Onboarding;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\CloseDate;
use App\Models\User;
use App\Models\UserPackageDiscount;
use App\Models\Workshop;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Resend\Laravel\Facades\Resend;
use App\Mail\OrderShipped;

class HomeController extends Controller
{
    public function index()
    {
        $workshops = Workshop::latest()->paginate(5);
        $closeDate = CloseDate::latest()->first();
        return view('frontend.index', compact('workshops', 'closeDate'));
    }

    public function class(Request $request)
    {
        $today = Carbon::today(); // 2026-08-11
        $todayDay = $today->format('D'); // e.g., 'Tue' (use 'l' for full name like 'Tuesday')

        // 2. Fetch classes valid for today
        $classes = ClassSchedule::with('category')
            ->where('status', 'book')
            // // Check if today is within start_date and end_date
            // ->whereDate('start_date', '<=', $today)
            // ->whereDate('end_date', '>=', $today)
            // Check if today matches scheduled days (JSON array)
            // ->whereJsonContains('days', $todayDay)
            // Filter category if needed
            ->whereHas('category', function ($query) {
                $query->where('name', 'Yoga');
            })
            ->get();
        $allImages = Gallery::all();

        foreach ($classes as $class) {
            // Manually fetch instructors based on the array of IDs
            $class->instructor = Instructor::with('user')->whereIn('id', $class->instructor_ids ?? [])->get()->toArray();
        }
        $instructors = Instructor::get();
        $categories = Category::get();
        $bookings = Booking::where('registered_id', auth()->id())->get();
        $bookingsAll = Booking::all();
        return view("frontend.class", compact('classes', 'instructors', 'categories', 'bookings', 'bookingsAll', 'allImages'));
    }

    public function classDetails(Request $request)
    {
        $class = ClassSchedule::with('category')->findOrFail($request->id);
        $class->instructor = Instructor::with('user')->whereIn('id', $class->instructor_ids ?? [])->get()->toArray();

        $bookingCount = $class->bookings()->where('status', 'confirmed')->count();
        $isFull = $bookingCount >= $class->capacity;
        $bookings = Booking::where('registered_id', auth()->id())->get();
        $waitingApproval = Comment::where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->where('admin_approval', false)
            ->get();
        $comments = Comment::where('class_id', $class->id)
            ->whereNull('parent_id')
            ->where('admin_approval', true)
            ->with(['user', 'approvedReplies'])
            ->latest()
            ->get();
        return view("frontend.class_details", compact('class', 'bookingCount', 'isFull', 'bookings', 'comments', 'waitingApproval'));
    }

    public function search(Request $request)
    {
        // Initialize the query with eager loading relationships
        $query = ClassSchedule::with('category');


        // 1. Keyword Text Search Scope
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereDate('start_date', 'like', "%{$search}%")
                    ->orWhereDate('end_date', 'like', "%{$search}%")

                    // 1. Search category relation
                    ->orWhereHas('category', function ($innerQ) use ($search) {
                        $innerQ->where('name', 'like', "%{$search}%");
                    })

                    // 2. Search instructors matching the name, then check if instructor_ids contains their ID
                    ->orWhere(function ($instructorQuery) use ($search) {
                        $matchingInstructorIds = \App\Models\Instructor::whereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        })->pluck('id')->toArray();

                        foreach ($matchingInstructorIds as $id) {
                            $instructorQuery->orWhereJsonContains('instructor_ids', (string) $id)
                                ->orWhereJsonContains('instructor_ids', (int) $id);
                        }
                    });
            });
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->where('start_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->where('start_date', '<=', $request->to_date);
        }

        // 3. FIXED: Direct Foreign Key Assignment Matching
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Instructor Match Filter
        if ($request->filled('instructor')) {
            $instructorId = $request->instructor;

            $query->where(function ($q) use ($instructorId) {
                $q->whereJsonContains('instructor_ids', (int) $instructorId)
                    ->orWhereJsonContains('instructor_ids', (string) $instructorId);
            });
        }

        $classes = $query->paginate(8);
        $classes->getCollection()->each(function ($class) {
            if ($class->instructor_ids) {
                $ids = $class->instructor_ids;
                $class->instructor = Instructor::with('user')
                    ->whereIn('id', $ids)
                    ->get();
            }
        });

        $instructors = Instructor::with('user')->get();
        $categories = Category::all();
        $bookings = Booking::where('registered_id', auth()->id())->get();
        $bookingsAll = Booking::all();
        return view('frontend.searchResult', compact('classes', 'instructors', 'categories', 'bookings', 'bookingsAll'));
    }

    public function contact()
    {
        return view("frontend.contact");
    }

    public function rates(Request $request)
    {
        $query = Package::with('category');

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $packages = $query->get();
        $userDiscountPackages = UserPackageDiscount::with('package')->where('user_id', auth()->id())->get();
        $categories = Category::all();

        return view('frontend.rates', compact('packages', 'categories', 'userDiscountPackages'));
    }

    public function mailSent(Request $request): RedirectResponse
    {
        Resend::emails()->send([
            'from' => 'Acme <onboarding@resend.dev>',
            'to' => 'somawellness.mm@gmail.com',
            'reply_to' => $request->email,
            'subject' => $request->subject,
            'html' => "
        <h3>New Contact Message from {$request->name}</h3>
        <p><strong>Sender Email:</strong> {$request->email}</p>
        <p><strong>Sender Name:</strong> {$request->name}</p>
        <p><strong>Message:</strong>     
           <p>{$request->message}</p>
        </p>
    ",
        ]);
        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function schedule(Request $request)
    {
        $now = Carbon::now();
        $defaultStartDate = $now->copy()->startOfWeek()->format('Y-m-d');
        $defaultEndDate = $now->copy()->endOfWeek()->format('Y-m-d');

        $fromDate = $request->input('from_date', $defaultStartDate);
        $toDate = $request->input('to_date', $defaultEndDate);

        // 1. Fetch schedules within date range with required relationships
        $schedules = ClassSchedule::with(['category', 'bookings.user'])
            ->whereDate('start_date', '>=', $fromDate)
            ->whereDate('start_date', '<=', $toDate)
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        // 2. Attach instructors to each schedule object
        $schedules->each(function ($schedule) {
            if (!empty($schedule->instructor_ids)) {
                $ids = is_array($schedule->instructor_ids)
                    ? $schedule->instructor_ids
                    : json_decode($schedule->instructor_ids, true);

                // Plural 'instructors' matches your Blade view
                $schedule->instructors = Instructor::with('user')
                    ->whereIn('id', $ids ?? [])
                    ->get();
            } else {
                $schedule->instructors = collect();
            }
        });

        // 3. Group the ENRICHED $schedules collection directly by date
        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->start_date)->format('l, F j');
        });
        // Cleaned up for production view render
        return view('frontend.scheduleList', compact('groupedSchedules', 'fromDate', 'toDate'));
    }

    public function payment($id)
    {
        $package = Package::findOrFail($id);
        $payments = Payment::get();
        $redeem = false;
        $onboarding = Onboarding::where('user_id', auth()->id())->first();
        $discountPackage = UserPackageDiscount::with('package')->where('user_id', auth()->id())->where('package_id', $id)->first();
        return view('frontend.payment', compact('payments', 'package', 'redeem', 'onboarding', 'discountPackage'));
    }

    public function redeemCoin($id)
    {
        $package = Package::findOrFail($id);
        $user = auth()->user();

        if ($user->coins < $package->loyal_point) {
            return redirect()->back()->with('error', 'You do not have enough coins to redeem this package.');
        }

        $payments = Payment::get();
        $redeem = true;
        $onboarding = Onboarding::where('user_id', auth()->id())->first();
        $discountPackage = UserPackageDiscount::with('package')->where('user_id', auth()->id())->where('package_id', $id)->first();
        return view('frontend.payment', compact('payments', 'package', 'redeem', 'onboarding', 'discountPackage'));
    }

    public function paymentSubmit(Request $request)
    {
        // Remove dd($request->receiver_name); once you're done testing

        // 1. Validate ALL incoming fields, including receiver_name
        $validated = $request->validate([
            'registered_id' => 'required|exists:users,id',
            'package' => 'required|exists:packages,id',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string',
            'receiver_name' => 'nullable|string|max:255', // <-- ADD THIS RULE
            'amount' => 'required|numeric',
            'transaction_id' => 'nullable|string',
            'gateway_method' => 'required|string',
            'userDiscount' => 'nullable|numeric',
            'coin_used' => 'nullable|numeric|min:0',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Fetch package details
        $packageModel = Package::findOrFail($request->package);

        // 3. Create the purchase record
        DB::transaction(function () use ($request, $packageModel) {

            Purchase::create([
                'registered_id' => $request->registered_id,
                'selected_packages_id' => $request->package,
                'account_name' => $request->sender_name,
                'receiver_name' => $request->receiver_name ?? 'N/A', // <-- Fallback ensures MySQL never receives raw NULL
                'amount' => $request->amount,
                'phone' => $request->sender_phone,
                'transaction_no' => $request->transaction_id ?? 'CASH-' . strtoupper(uniqid()),
                'payment_method' => $request->gateway_method,
                'user_discount' => $request->userDiscount ?? 0,
                'coin_used' => $request->coin_used ?? 0,
                'class_remaining' => $packageModel->class_count,
                'expires_at' => now()->addDays($packageModel->duration),
                'fix_expires_at' => now()->addDays($packageModel->fix_duration),
            ]);

            // 4. Loyalty Coin Deduction Logic
            $coinsRequested = (float) $request->coin_used;

            if ($coinsRequested > 0) {
                $userId = $request->registered_id;

                $user = User::where('id', $userId)->lockForUpdate()->firstOrFail();

                if ($user->coins < $coinsRequested) {
                    throw new \Exception("Insufficient coins available for redemption.");
                }

                $user->decrement('coins', $coinsRequested);

                $activePoints = LoyalPoint::where('user_id', $userId)
                    ->where('is_redeemed', false)
                    ->where('expired_at', '>', now())
                    ->orderBy('expired_at', 'asc')
                    ->lockForUpdate()
                    ->get();

                $pointsToDeduct = $coinsRequested;

                foreach ($activePoints as $pointRecord) {
                    if ($pointsToDeduct <= 0)
                        break;

                    $availableInBatch = round((float) $pointRecord->points, 4);

                    if ($availableInBatch >= $pointsToDeduct) {
                        $remaining = round($availableInBatch - $pointsToDeduct, 4);
                        $pointRecord->points = $remaining;
                        $pointRecord->is_redeemed = ($remaining <= 0);
                        $pointRecord->save();

                        $pointsToDeduct = 0;
                    } else {
                        $pointsToDeduct = round($pointsToDeduct - $availableInBatch, 4);
                        $pointRecord->points = 0;
                        $pointRecord->is_redeemed = true;
                        $pointRecord->save();
                    }
                }

                if ($pointsToDeduct > 0) {
                    throw new \Exception("Mismatch between total user balance and active points batches.");
                }
            }
        });

        return redirect()->route('history.page')
            ->with('success', 'Purchase completed successfully.');
    }

    public function history(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'rates');

        $purchases = Purchase::with('package.category')
            ->where('registered_id', auth()->id())
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('transaction_no', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('account_name', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhereHas('package', function ($packageQuery) use ($search) {
                            $packageQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        $classes = Booking::with(['class', 'class.category'])
            ->where('registered_id', auth()->id())
            ->when($search, function ($q) use ($search) {
                $instructorIds = Instructor::whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->pluck('id')->map(fn($id) => (string) $id)->toArray();

                $q->where(function ($sub) use ($search, $instructorIds) {
                    $sub->where('status', 'like', "%{$search}%")
                        ->orWhereHas('class', function ($c) use ($search, $instructorIds) {
                            $c->where(function ($classQuery) use ($search, $instructorIds) {
                                $classQuery->where('class_name', 'like', "%{$search}%");
                                if (!empty($instructorIds)) {
                                    $classQuery->orWhere(function ($jsonQuery) use ($instructorIds) {
                                        foreach ($instructorIds as $id) {
                                            $jsonQuery->orWhereJsonContains('instructor_ids', $id);
                                        }
                                    });
                                }
                            });
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)->fragment('classResults');

        $classes->getCollection()->each(function ($booking) {
            if ($booking->class) {
                $ids = $booking->class->instructor_ids ?? [];
                $booking->class->instructors = Instructor::with('user')
                    ->whereIn('id', $ids)
                    ->get();
            }
        });

        if ($request->ajax()) {
            return view('frontend.history_list', compact('purchases', 'classes', 'tab'))->render();
        }

        return view('frontend.history', compact('purchases', 'classes', 'tab'));
    }

    public function joinClass($id)
    {
        // ---------------------------------------------------------
        // ADDED: Check if there is an active close date
        // ---------------------------------------------------------
        $closeDate = CloseDate::latest()->first();
        if ($closeDate) {
            return redirect()->back()
                ->with('warning', 'ပိတ်ရက်ဖြစ်သောကြောင့် Class ကို Join ၍မရနိုင်ပါ။ ' . strip_tags($closeDate->description));
        }

        $class = ClassSchedule::findOrFail($id);
        $packages = Package::latest()->paginate(4);

        $categoryMatchExists = Purchase::whereHas('package', function ($query) use ($class) {
            $query->where('type', $class->category_id);
        })
            ->where('registered_id', auth()->id())
            ->exists();

        if (!$categoryMatchExists) {
            return redirect()->route('rates.page')
                ->with('warning', 'You do not have a package matching this category. Please purchase one.');
        }

        $activePurchase = Purchase::whereHas('package', function ($query) use ($class) {
            $query->where('type', $class->category_id);
        })
            ->where('registered_id', auth()->id())
            ->where('pay_status', 'confirmed')
            ->where('class_remaining', '>', 0)
            ->where('expires_at', '>=', now())
            ->where(function ($query) {
                $query->whereRaw('class_remaining = (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)')
                    ->where('fix_expires_at', '>=', now())
                    ->orWhereRaw('class_remaining < (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)');
            })
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$activePurchase) {
            return redirect()->route('rates.page')
                ->with('warning', 'You do not have an active package. Please purchase one.');
        }

        $bookedCount = $class->bookings()
            ->where('status', 'confirmed')
            ->count();

        $booking = new Booking();
        $booking->package_id = $activePurchase->selected_packages_id;
        $booking->registered_id = auth()->id();
        $booking->selected_class_id = $class->id;

        $admins = User::where('name', 'System Admin')->get();
        $classCapacity = $class->capacity ?? 0;

        if ($bookedCount >= $classCapacity) {
            $booking->status = 'waitlisted';
            $booking->save();

            $data = [
                'title' => 'New Waitlist Booking',
                'message' => auth()->user()->name . ' is waiting to join ' . $class->class_name,
                'url' => 'waitlist.index'
            ];

            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification($data));
            }
            $activePurchase->decrement('class_remaining');

            return redirect()->back()
                ->with('warning', 'Class is full. You have been added to the waiting list. Check updates in your Library page.');
        }

        $booking->status = 'confirmed';
        $booking->save();
        $activePurchase->decrement('class_remaining');

        return redirect()->back()
            ->with('success', 'Successfully joined the class. Check updates in your Library page.');
    }

    public function removeClass(Request $request, $id)
    {
        // Validation စစ်ရန် (အကြောင်းပြချက် ထည့်သွင်းထားခြင်း ရှိ/မရှိ)
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        // Login ဝင်ထားတဲ့ user ရဲ့ booking ဟုတ်မဟုတ် စစ်ဆေးပါမယ်
        // $id နေရာမှာ UI က $class->selected_class_id ကို လှမ်းပို့တာဖြစ်လို့ selected_class_id ကို စစ်ပါမယ်
        $booking = Booking::where('registered_id', auth()->id())
            ->where('selected_class_id', $id)
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if (!$booking) {
            return redirect()->back()
                ->with('error', 'You are not booked for this class or it is already cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            // 'cancellation_reason' => $request->cancellation_reason,
            'byWho' => 'User'
        ]);

        if ($booking->package_id) {
            $activePurchase = Purchase::where('registered_id', auth()->id())
                ->where('selected_packages_id', $booking->package_id)
                ->where('pay_status', 'confirmed')
                ->where('expires_at', '>=', now())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($activePurchase) {
                $activePurchase->increment('class_remaining');
            }
        }

        $data = [
            'title' => 'Class Booking Cancelled',
            'message' => auth()->user()->name . ' has cancelled their booking for ' . ($booking->class->class_name ?? 'a class') . '. Reason: ' . $request->cancellation_reason,
            'url' => 'bookings.index'
        ];

        $admins = User::where('name', 'System Admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminNotification($data));
        }

        return redirect()->back()
            ->with('success', 'Successfully cancelled the class booking.');
    }
    /**
     * User ၏ Class Booking History များကို ဆွဲထုတ်ပြသမည့် Method
     */
    public function myClassHistory()
    {
        $userId = auth()->id();

        // 1. Booking model ထဲက classSchedule() relationship ကို သုံးပါ
        $bookings = \App\Models\Booking::with(['classSchedule.category'])
            ->where('registered_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookings as $booking) {
            // 2. Booking model ထဲက classSchedule ကို ခေါ်ပါ
            $class = $booking->classSchedule;

            if ($class && !empty($class->instructor_ids)) {
                $class->instructorList = \App\Models\Instructor::with('user')
                    ->whereIn('id', $class->instructor_ids)
                    ->get();
            } else {
                $class->instructorList = collect();
            }

            // 3. View ဖိုင်မှာ $booking->assigned_class သုံးနိုင်အောင် assign လုပ်ပေးခြင်း
            $booking->assigned_class = $class;
        }

        return view('frontend.class_history', compact('bookings'));
    }
}
