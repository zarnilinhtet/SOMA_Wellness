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
use App\Models\Attendance;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
        $today = Carbon::today('Asia/Yangon');
        $todayDay = $today->format('D');

        $completedClassIdsToday = Attendance::whereDate('attendance_date', $today->format('Y-m-d'))
            ->where('admin_approve', 1)
            ->pluck('class_id')
            ->toArray();

        $dbClasses = ClassSchedule::with(['category'])
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->whereDate('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->where('days', 'like', "%{$todayDay}%")
            ->whereNotIn('id', $completedClassIdsToday)
            ->get();

        $expandedClasses = collect();
        foreach ($dbClasses as $class) {
            $exactEndTime = Carbon::parse($today->format('Y-m-d') . ' ' . ($class->end_time ?? '23:59:59'), 'Asia/Yangon');
            if (Carbon::now('Asia/Yangon')->lessThanOrEqualTo($exactEndTime)) {
                $session = clone $class;
                $session->target_date = $today->format('Y-m-d');
                $expandedClasses->push($session);
            }
        }

        $expandedClasses = $expandedClasses->sortBy(function ($item) {
            return $item->target_date . ' ' . $item->start_time;
        })->values();

        $perPage = 10;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $classes = new LengthAwarePaginator(
            $expandedClasses->forPage($page, $perPage),
            $expandedClasses->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $classes->getCollection()->each(function ($class) {
            $ids = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : ($class->instructor_ids ?? []);
            if (!is_array($ids)) $ids = [$ids];

            $class->instructor = Instructor::with('user')
                ->whereIn('id', $ids)
                ->get()
                ->toArray();
        });

        $instructors = Instructor::get();
        $categories = Category::get();

        $bookings = auth()->check() ? Booking::where('registered_id', auth()->id())->get() : collect();
        $bookingsAll = Booking::all();
        $allImages = Gallery::all();

        return view("frontend.class", compact('classes', 'instructors', 'categories', 'bookings', 'bookingsAll', 'allImages'));
    }

    public function classDetails(Request $request)
    {
        $class = ClassSchedule::with('category')->findOrFail($request->id);

        $ids = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : ($class->instructor_ids ?? []);
        if (!is_array($ids)) $ids = [$ids];

        $class->instructor = Instructor::with('user')->whereIn('id', $ids)->get()->toArray();

        $bookingCount = $class->bookings()->where('status', 'confirmed')->count();
        $isFull = $bookingCount >= $class->capacity;

        $bookings = auth()->check() ? Booking::where('registered_id', auth()->id())->get() : collect();

        $waitingApproval = auth()->check() ? Comment::where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->where('admin_approval', false)
            ->get() : collect();

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
        $today = Carbon::today('Asia/Yangon');
        $isSearch = $request->has('category') || $request->has('search') || $request->has('instructor');

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : $today->copy();

        if ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date);
        } else {
            $toDate = $isSearch ? $fromDate->copy()->addDays(6) : $fromDate->copy();
        }

        $completedClassIdsToday = Attendance::whereDate('attendance_date', $today->format('Y-m-d'))
            ->where('admin_approve', 1)
            ->pluck('class_id')
            ->toArray();

        $query = ClassSchedule::with(['category'])
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->whereDate('start_date', '<=', $toDate)
            ->where(function ($q) use ($fromDate) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $fromDate);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($innerQ) use ($search) {
                    $innerQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('instructor')) {
            $instructorId = $request->instructor;
            $query->where(function ($q) use ($instructorId) {
                $q->whereJsonContains('instructor_ids', (int) $instructorId)
                    ->orWhereJsonContains('instructor_ids', (string) $instructorId);
            });
        }

        $dbClasses = $query->get();
        $expandedClasses = collect();
        $daysMap = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];

        foreach ($dbClasses as $class) {
            $classStart = Carbon::parse($class->start_date)->startOfDay();
            $classEnd = $class->end_date ? Carbon::parse($class->end_date)->endOfDay() : Carbon::parse('2099-12-31')->endOfDay();

            $classDaysRaw = is_string($class->days) ? json_decode($class->days, true) : ($class->days ?? []);
            if (!is_array($classDaysRaw)) $classDaysRaw = [];

            $classDayInts = [];
            foreach ($classDaysRaw as $d) {
                if (!is_string($d) && !is_numeric($d)) continue;
                $shortDay = substr(ucfirst(trim((string)$d)), 0, 3);
                if (isset($daysMap[$shortDay])) $classDayInts[] = $daysMap[$shortDay];
            }

            $currentDate = $fromDate->copy();

            while ($currentDate->lessThanOrEqualTo($toDate)) {
                if ($currentDate->between($classStart, $classEnd) && in_array($currentDate->dayOfWeek, $classDayInts)) {
                    if ($currentDate->isSameDay($today) && in_array($class->id, $completedClassIdsToday)) {
                        $currentDate->addDay();
                        continue;
                    }

                    $exactEndTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . ($class->end_time ?? '23:59:59'), 'Asia/Yangon');
                    if (Carbon::now('Asia/Yangon')->lessThanOrEqualTo($exactEndTime)) {
                        $session = clone $class;
                        $session->target_date = $currentDate->format('Y-m-d');
                        $expandedClasses->push($session);
                    }
                }
                $currentDate->addDay();
            }
        }

        $expandedClasses = $expandedClasses->sortBy(function ($item) {
            return $item->target_date . ' ' . $item->start_time;
        })->values();

        $perPage = 10;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $classes = new LengthAwarePaginator(
            $expandedClasses->forPage($page, $perPage),
            $expandedClasses->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $classes->getCollection()->each(function ($class) {
            $ids = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : ($class->instructor_ids ?? []);
            if (!is_array($ids)) $ids = [$ids];

            $class->instructor = Instructor::with('user')
                ->whereIn('id', $ids)
                ->get()
                ->toArray();
        });

        $instructors = Instructor::with('user')->get();
        $categories = Category::all();
        $bookings = auth()->check() ? Booking::where('registered_id', auth()->id())->get() : collect();
        $bookingsAll = Booking::all();
        $allImages = Gallery::all();

        return view('frontend.class', compact('classes', 'instructors', 'categories', 'bookings', 'bookingsAll', 'allImages'));
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

        $userDiscountPackages = auth()->check() ? UserPackageDiscount::with('package')->where('user_id', auth()->id())->get() : collect();
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

        $schedules = ClassSchedule::with(['category', 'bookings.user'])
            ->whereDate('start_date', '>=', $fromDate)
            ->whereDate('start_date', '<=', $toDate)
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        $schedules->each(function ($schedule) {
            if (!empty($schedule->instructor_ids)) {
                $ids = is_string($schedule->instructor_ids) ? json_decode($schedule->instructor_ids, true) : $schedule->instructor_ids;
                if (!is_array($ids)) $ids = [$ids];

                $schedule->instructors = Instructor::with('user')
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $schedule->instructors = collect();
            }
        });

        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->start_date)->format('l, F j');
        });

        return view('frontend.scheduleList', compact('groupedSchedules', 'fromDate', 'toDate'));
    }

    public function payment($id)
    {
        $package = Package::findOrFail($id);
        $payments = Payment::get();
        $redeem = false;

        $onboarding = auth()->check() ? Onboarding::where('user_id', auth()->id())->first() : null;
        $discountPackage = auth()->check() ? UserPackageDiscount::with('package')->where('user_id', auth()->id())->where('package_id', $id)->first() : null;

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
        $validated = $request->validate([
            'registered_id' => 'required|exists:users,id',
            'package' => 'required|exists:packages,id',
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'transaction_id' => 'nullable|string',
            'gateway_method' => 'required|string',
            'userDiscount' => 'nullable|numeric',
            'coin_used' => 'nullable|numeric|min:0',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $packageModel = Package::findOrFail($request->package);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $image = $request->file('screenshot');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/purchases'), $imageName);
            $screenshotPath = 'uploads/purchases/' . $imageName;
        }

        $userId = $request->registered_id;
        $latestPurchase = Purchase::where('registered_id', $userId)
            ->whereHas('package', function ($q) use ($packageModel) {
                $q->where('type', $packageModel->type);
            })
            ->orderBy('fix_expires_at', 'desc')
            ->first();

        $baseDate = now();
        if ($latestPurchase && $latestPurchase->fix_expires_at) {
            $latestFixDate = Carbon::parse($latestPurchase->fix_expires_at);
            if ($latestFixDate->isFuture() && $latestPurchase->class_remaining > 0) {
                $baseDate = $latestFixDate;
            }
        }

        DB::transaction(function () use ($request, $packageModel, $screenshotPath, $baseDate) {
            Purchase::create([
                'registered_id' => $request->registered_id,
                'selected_packages_id' => $request->package,
                'account_name' => $request->sender_name,
                'receiver_name' => $request->receiver_name ?? 'N/A',
                'amount' => $request->amount,
                'phone' => $request->sender_phone ?? '',
                'transaction_no' => $request->transaction_id ?? 'CASH-' . strtoupper(uniqid()),
                'payment_method' => $request->gateway_method,
                'user_discount' => $request->userDiscount ?? 0,
                'coin_used' => $request->coin_used ?? 0,
                'class_remaining' => $packageModel->class_count,
                'expires_at' => $baseDate->copy()->addDays($packageModel->duration),
                'fix_expires_at' => $baseDate->copy()->addDays($packageModel->fix_duration),
                'screenshot' => $screenshotPath,
            ]);

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
                    if ($pointsToDeduct <= 0) break;

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
            }
        });

        return redirect()->route('history.page')
            ->with('success', 'Purchase completed successfully.');
    }

    public function history(Request $request)
    {
        $userId = auth()->id();
        $tab = $request->input('tab', 'rates');

        // 1. Get All Purchases (DataTables handles Pagination on Frontend)
        $purchases = Purchase::with('package.category')
            ->where('registered_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check Queued / Active logic
        $realActiveIds = [];
        $allValid = Purchase::with('package')
            ->where('registered_id', $userId)
            ->where('class_remaining', '>', 0)
            ->where('pay_status', 'confirmed')
            ->where('fix_expires_at', '>=', now())
            ->get();

        $grouped = $allValid->groupBy(function ($p) {
            return $p->package ? $p->package->type : 'none';
        });

        foreach ($grouped as $type => $packs) {
            $active = $packs->sortBy('created_at')->first();
            if ($active) $realActiveIds[] = $active->id;
        }

        $purchases->each(function ($purchase) use ($realActiveIds) {
            $purchase->is_queued = false;
            $purchase->projected_start = null;
            $purchase->is_active_now = in_array($purchase->id, $realActiveIds);
            $purchase->is_finished = false;

            $packageModel = $purchase->package;
            $expiryDate = $purchase->expires_at ? Carbon::parse($purchase->expires_at) : null;
            $fixExpiryDate = $purchase->fix_expires_at ? Carbon::parse($purchase->fix_expires_at) : null;

            if (strtolower($purchase->status ?? '') === 'finished') {
                $purchase->is_finished = true;
            } elseif (strtolower($purchase->pay_status) === 'confirmed') {
                if ($purchase->class_remaining <= 0 && !$purchase->is_queued && !$purchase->is_active_now) {
                    $purchase->is_finished = true;
                }
                if ($expiryDate && $expiryDate->isPast()) $purchase->is_finished = true;
                if ($fixExpiryDate && $fixExpiryDate->isPast()) $purchase->is_finished = true;
            }

            if ($packageModel && $purchase->class_remaining > 0 && !$purchase->is_active_now && strtolower($purchase->pay_status) === 'confirmed' && !$purchase->is_finished) {
                $purchase->is_queued = true;
                if ($purchase->fix_expires_at) {
                    $purchase->projected_start = Carbon::parse($purchase->fix_expires_at)->subDays($packageModel->fix_duration);
                }
            }
        });

        // 2. Get All Classes (Bookings) (DataTables handles Pagination on Frontend)
        $classes = Booking::with(['classSchedule.category'])
            ->where('registered_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($classes as $booking) {
            $class = $booking->classSchedule;
            if ($class && !empty($class->instructor_ids)) {
                $ids = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : $class->instructor_ids;
                if (!is_array($ids)) $ids = [$ids];

                $class->instructorList = Instructor::with('user')
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                if ($class) {
                    $class->instructorList = collect();
                }
            }
            $booking->assigned_class = $class;
        }

        return view('frontend.history', compact('purchases', 'classes', 'tab'));
    }

    public function joinClass(Request $request, $id)
    {
        $closeDate = CloseDate::latest()->first();
        if ($closeDate) {
            return redirect()->back()
                ->with('warning', 'ပိတ်ရက်ဖြစ်သောကြောင့် Class ကို Join ၍မရနိုင်ပါ။ ' . strip_tags($closeDate->description));
        }

        $class = ClassSchedule::findOrFail($id);

        $dateParam = $request->query('date');
        if (is_array($dateParam)) $dateParam = $dateParam[0];
        $targetDateStr = $dateParam ?: Carbon::today('Asia/Yangon')->format('Y-m-d');
        $targetDate = Carbon::parse($targetDateStr);

        $existingBooking = Booking::where('registered_id', auth()->id())
            ->where('selected_class_id', $class->id)
            ->whereDate('booked_date', $targetDateStr)
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if ($existingBooking) {
            return redirect()->back()
                ->with('warning', "You have already joined this class for {$targetDate->format('d M Y')}.");
        }

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
            ->orderBy('created_at', 'asc') // Queued ဖြစ်နေသည်များကို အစဉ်လိုက်ရွေးချယ်ရန်
            ->first();

        if (!$activePurchase) {
            return redirect()->route('rates.page')
                ->with('warning', 'You do not have an active package. Please purchase one.');
        }

        $bookedCount = Booking::where('selected_class_id', $class->id)
            ->whereDate('booked_date', $targetDateStr)
            ->where('status', 'confirmed')
            ->count();

        $booking = new Booking();
        $booking->package_id = $activePurchase->selected_packages_id;
        $booking->registered_id = auth()->id();
        $booking->selected_class_id = $class->id;
        $booking->booked_date = $targetDateStr;

        $admins = User::where('name', 'System Admin')->get();
        $classCapacity = $class->capacity ?? 0;

        if ($bookedCount >= $classCapacity) {
            $booking->status = 'waitlisted';
            $booking->save();

            $data = [
                'title' => 'New Waitlist Booking',
                'message' => auth()->user()->name . ' is waiting to join ' . $class->class_name . ' on ' . $targetDate->format('d M Y'),
                'url' => 'waitlist.index'
            ];

            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification($data));
            }
            $activePurchase->decrement('class_remaining');

            return redirect()->back()
                ->with('warning', "Class is full for {$targetDate->format('d M Y')}. You have been added to the waiting list.");
        }

        $booking->status = 'confirmed';
        $booking->save();

        $packageModel = Package::find($activePurchase->selected_packages_id);
        if ($packageModel && $activePurchase->class_remaining == $packageModel->class_count) {
            $activePurchase->expires_at = now()->addDays($packageModel->duration);
            $activePurchase->fix_expires_at = now()->addDays($packageModel->fix_duration);
            $activePurchase->save();
        }

        $activePurchase->decrement('class_remaining');

        return redirect()->back()
            ->with('success', "Successfully joined the class for {$targetDate->format('d M Y')}. Check updates in your Library page.");
    }

    public function removeClass(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $targetDateStr = $request->query('date');
        if (is_array($targetDateStr)) $targetDateStr = $targetDateStr[0];

        $query = Booking::where('registered_id', auth()->id())
            ->where('selected_class_id', $id)
            ->whereIn('status', ['confirmed', 'waitlisted']);

        if ($targetDateStr) {
            $query->whereDate('booked_date', $targetDateStr);
        }

        $booking = $query->orderBy('booked_date', 'asc')->first();

        if (!$booking) {
            return redirect()->back()
                ->with('error', 'You are not booked for this class or it is already cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
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

    public function myClassHistory()
    {
        $userId = auth()->id();

        $bookings = Booking::with(['classSchedule.category'])
            ->where('registered_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookings as $booking) {
            $class = $booking->classSchedule;

            if ($class && !empty($class->instructor_ids)) {
                $ids = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : $class->instructor_ids;
                if (!is_array($ids)) $ids = [$ids];

                $class->instructorList = Instructor::with('user')
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                if ($class) {
                    $class->instructorList = collect();
                }
            }
            $booking->assigned_class = $class;
        }

        return view('frontend.class_history', compact('bookings'));
    }
    public function myPackageHistory(Request $request)
    {
        $userId = auth()->id();

        // Get All Purchases for the user
        $purchases = Purchase::with('package.category')
            ->where('registered_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check Queued / Active logic
        $realActiveIds = [];
        $allValid = Purchase::with('package')
            ->where('registered_id', $userId)
            ->where('class_remaining', '>', 0)
            ->where('pay_status', 'confirmed')
            ->where('fix_expires_at', '>=', now())
            ->get();

        $grouped = $allValid->groupBy(function ($p) {
            return $p->package ? $p->package->type : 'none';
        });

        foreach ($grouped as $type => $packs) {
            $active = $packs->sortBy('created_at')->first();
            if ($active) $realActiveIds[] = $active->id;
        }

        $purchases->each(function ($purchase) use ($realActiveIds) {
            $purchase->is_queued = false;
            $purchase->projected_start = null;
            $purchase->is_active_now = in_array($purchase->id, $realActiveIds);
            $purchase->is_finished = false;

            $packageModel = $purchase->package;
            $expiryDate = $purchase->expires_at ? Carbon::parse($purchase->expires_at) : null;
            $fixExpiryDate = $purchase->fix_expires_at ? Carbon::parse($purchase->fix_expires_at) : null;

            // Determine if Finished
            if (strtolower($purchase->status ?? '') === 'finished') {
                $purchase->is_finished = true;
            } elseif (strtolower($purchase->pay_status) === 'confirmed') {
                if ($purchase->class_remaining <= 0 && !$purchase->is_queued && !$purchase->is_active_now) {
                    $purchase->is_finished = true;
                }
                if ($expiryDate && $expiryDate->isPast()) $purchase->is_finished = true;
                if ($fixExpiryDate && $fixExpiryDate->isPast()) $purchase->is_finished = true;
            }

            // Determine if Queued
            if ($packageModel && $purchase->class_remaining > 0 && !$purchase->is_active_now && strtolower($purchase->pay_status) === 'confirmed' && !$purchase->is_finished) {
                $purchase->is_queued = true;
                if ($purchase->fix_expires_at) {
                    $purchase->projected_start = Carbon::parse($purchase->fix_expires_at)->subDays($packageModel->fix_duration);
                }
            }
        });

        // View အသစ်သို့ return ပြန်ပါမည်
        return view('frontend.package_history', compact('purchases'));
    }
}
