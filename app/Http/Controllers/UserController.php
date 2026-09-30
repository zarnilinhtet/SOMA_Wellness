<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserPackageDiscount;
use App\Models\ClassSchedule;
use App\Models\Attendance; // 👈 ဤနေရာတွင် Attendance Model ကို ခေါ်ထားပါသည်
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['userType', 'onboarding'])->latest()->get();
        $packages = Package::all();
        $userTypes = Role::all();
        $userPackageDiscounts = UserPackageDiscount::with(['user', 'package'])->get();

        return view('user.user_index', compact('users', 'userTypes', 'packages', 'userPackageDiscounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'required',
            'phone' => 'required|string|max:20',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'plain_password' => $request->password,
                'age' => $request->age,
                'phone' => $request->phone,
            ]);

            $user->assignRole('Customer');

            return redirect()->back()->with('success', 'User account registered!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'An unexpected error occurred while creating your account. Please try again later.']);
        }
    }

    public function discount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'discount' => 'required|integer|min:0|max:100',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->discount = $request->discount;
        $user->discount_expire_at = $request->discount_expire_at;
        $user->packages = $request->packages;
        $user->save();

        return redirect()->back()->with('success', 'Discount updated successfully for ' . $user->name . '.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $userTypes = Role::all();
        return view('user.user_edit', compact('user', 'userTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'age' => 'required',
            'phone' => 'required|string|max:20',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->age = $request->age;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->plain_password = $request->password;
        }
        $user->save();

        return redirect()->route('user_register.index')->with('success', 'User updated successfully!');
    }

    public function role_update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|in:Admin,Instructor,Customer,Receptionist',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        return back()->with('success', $user->name . ' ၏ ရာထူးကို ' . $request->role . ' သို့ အောင်မြင်စွာ ပြောင်းလဲပြီးပါပြီ။');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Cannot delete System Administrator!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function usersWithPackages()
    {
        $purchasedUserIds = Purchase::where('pay_status', 'confirmed')
            ->pluck('registered_id')
            ->unique()
            ->toArray();

        $users = User::whereIn('id', $purchasedUserIds)
            ->with(['roles', 'onboarding', 'purchases' => function ($query) {
                $query->where('pay_status', 'confirmed')->with('package');
            }])
            ->latest()
            ->get();

        $users->map(function ($user) {
            $hasActivePackage = $user->purchases->contains(function ($purchase) {
                $remaining = $purchase->remaining_classes ?? $purchase->class_remaining ?? 0;
                return $remaining > 0;
            });

            $user->package_status = $hasActivePackage ? 'Using Package' : 'Completed';
            $user->total_classes = $user->purchases->sum('total_classes');

            $user->remaining_classes = $user->purchases->reduce(function ($carry, $purchase) {
                return $carry + ($purchase->remaining_classes ?? $purchase->class_remaining ?? 0);
            }, 0);

            $user->used_classes = $user->total_classes - $user->remaining_classes;

            return $user;
        });

        $userTypes = Role::all();
        $allPackages = Package::all();
        $allClasses = ClassSchedule::orderBy('start_date', 'desc')->get();

        return view('user.user_with_packages', compact('users', 'userTypes', 'allPackages', 'allClasses'));
    }

    public function userPackageDetails($id)
    {
        $user = User::findOrFail($id);

        $purchases = Purchase::with('package')
            ->where('registered_id', $id)
            ->where('pay_status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPackages = $purchases->count();
        $totalClassesAllowed = 0;
        $totalClassesRemaining = 0;

        foreach ($purchases as $purchase) {
            $classCount = $purchase->package->class_count ?? 0;
            $remaining = $purchase->remaining_classes ?? $purchase->class_remaining ?? 0;

            $totalClassesAllowed += $classCount;
            $totalClassesRemaining += $remaining;
            $purchase->used_classes = $classCount - $remaining;

            $isExpired = false;
            $now = Carbon::now();

            if ($purchase->expires_at && $purchase->expires_at < $now) {
                $isExpired = true;
            }

            if ($remaining == $classCount && $purchase->fix_expires_at && $purchase->fix_expires_at < $now) {
                $isExpired = true;
            }

            if ($isExpired) {
                $purchase->current_status = 'Expired';
            } elseif ($remaining <= 0) {
                $purchase->current_status = 'Completed';
            } else {
                $purchase->current_status = 'Active';
            }
        }

        $totalClassesUsed = $totalClassesAllowed - $totalClassesRemaining;

        return view('user.package_details', compact(
            'user',
            'purchases',
            'totalPackages',
            'totalClassesAllowed',
            'totalClassesRemaining',
            'totalClassesUsed'
        ));
    }

    public function checkClassEligibility(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_schedules,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $class = ClassSchedule::find($request->class_id);
        $user = User::find($request->user_id);

        $now = Carbon::now();
        $classCategoryId = $class->category_id;

        $purchases = Purchase::with('package')
            ->where('registered_id', $user->id)
            ->where('pay_status', 'confirmed')
            ->get();

        $hasEligiblePackage = false;

        foreach ($purchases as $purchase) {
            $remaining = $purchase->remaining_classes ?? $purchase->class_remaining ?? 0;
            $classCount = $purchase->package->class_count ?? 0;

            if ($remaining <= 0) {
                continue;
            }

            $isExpired = false;

            if ($purchase->expires_at && Carbon::parse($purchase->expires_at) < $now) {
                $isExpired = true;
            }

            if ($remaining == $classCount && $purchase->fix_expires_at && Carbon::parse($purchase->fix_expires_at) < $now) {
                $isExpired = true;
            }

            if ($isExpired) {
                continue;
            }

            $packageCategoryId = $purchase->package->category_id ?? null;

            if ($packageCategoryId && $packageCategoryId != $classCategoryId) {
                continue;
            }

            $hasEligiblePackage = true;
            break;
        }

        if ($hasEligiblePackage) {
            return response()->json(['status' => true, 'message' => 'User is eligible.']);
        }

        return response()->json([
            'status' => false,
            'message' => 'User does not have an active package matching this class category.'
        ]);
    }
    /**
     * Get users with packages that are about to expire.
     */
    /**
     * Get users with packages that are about to expire.
     */
    public function expiringPackages(Request $request)
    {
        // Default အနေဖြင့် ယနေ့မှစ၍ နောက် ၇ ရက် (တစ်ပတ်) အတွင်း Expire ဖြစ်မည့် စာရင်းကို ပြပေးမည်
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->addDays(7)->toDateString()); // 👈 ဤနေရာတွင် 30 အစား 7 သို့ ပြောင်းလဲထားပါသည်

        // Confirmed ဖြစ်ပြီး Remaining Class ကျန်သေးသော Purchase များကို ဆွဲထုတ်ခြင်း
        $purchases = Purchase::with(['user', 'package'])
            ->where('pay_status', 'confirmed')
            ->where('class_remaining', '>', 0)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expires_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->orWhereBetween('fix_expires_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->get();

        // Data များကို View အတွက် ပြင်ဆင်ခြင်း
        $expiringPackages = $purchases->map(function ($purchase) {
            $expiryDate = $purchase->expires_at ? Carbon::parse($purchase->expires_at) : null;
            $fixExpiryDate = $purchase->fix_expires_at ? Carbon::parse($purchase->fix_expires_at) : null;

            // Expiry Date အမှန်ကို ရွေးချယ်ခြင်း
            $actualExpiry = $expiryDate ?? $fixExpiryDate;

            $purchase->actual_expiry_date = $actualExpiry;

            if ($actualExpiry) {
                // ယနေ့နှင့် နှိုင်းယှဉ်၍ ကျန်ရက် (Days Left) ကို တွက်ချက်ခြင်း (Negative ဆိုလျှင် Expired)
                $purchase->days_left = Carbon::now()->startOfDay()->diffInDays($actualExpiry->startOfDay(), false);
            } else {
                $purchase->days_left = null;
            }

            $purchase->remaining = $purchase->class_remaining ?? 0;
            return $purchase;
        })->filter(function ($purchase) {
            return $purchase->actual_expiry_date !== null;
        })->sortBy('days_left'); // ရက်အနီးဆုံးမှ စတင်ပြသရန် Sort လုပ်ခြင်း

        return view('user.expiring_packages', compact('expiringPackages', 'startDate', 'endDate'));
    }
    public function getAttendanceHistory($id)
    {
        try {
            $user = User::findOrFail($id);

            // 👈 တကယ့် Attendance table ကနေ client_id ဖြင့် ဆွဲထုတ်ခြင်း
            $history = Attendance::with('class')
                ->where('client_id', $id)
                ->orderBy('attendance_date', 'desc')
                ->get()
                ->map(function ($record) {
                    $schedule = $record->class;

                    return [
                        'date' => $record->attendance_date ? Carbon::parse($record->attendance_date)->format('d M Y') : 'N/A',
                        'time' => $schedule && $schedule->start_time ? Carbon::parse($schedule->start_time)->format('h:i A') : 'N/A',
                        'class_name' => $schedule ? ($schedule->class_name ?? $schedule->name) : 'Unknown Class',
                        'status' => $record->attended ? 'Attended' : 'No Show'
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get users who haven't attended classes for more than 3 days.
     */
    /**
     * Get users who haven't attended classes based on date range.
     * Default is users absent for more than 3 days.
     */
    public function absentUsers(Request $request)
    {
        // Default အနေဖြင့် နောက်ဆုံးတက်ခဲ့သောရက်သည် လွန်ခဲ့သော ၃ ရက်မှ ၃၀ ရက်အတွင်း (အနည်းဆုံး ၃ ရက်ပျက်နေသူများ) ကို ပြပေးမည်
        $defaultEndDate = Carbon::today()->subDays(3)->toDateString();
        $defaultStartDate = Carbon::today()->subDays(30)->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);

        // Active Package (အတန်းကျန်သေးသောသူများ) ကိုသာ ဆွဲထုတ်မည်
        $activeUserIds = Purchase::where('pay_status', 'confirmed')
            ->where('class_remaining', '>', 0)
            ->pluck('registered_id')
            ->unique();

        $absentUsers = User::whereIn('id', $activeUserIds)
            ->with(['purchases' => function ($q) {
                $q->where('pay_status', 'confirmed')->where('class_remaining', '>', 0)->with('package');
            }])
            ->get()
            ->map(function ($user) {
                // နောက်ဆုံး တက်ရောက်ခဲ့သောရက်
                $lastAttendance = Attendance::where('client_id', $user->id)
                    ->where('attended', 1)
                    ->orderBy('attendance_date', 'desc')
                    ->first();

                if ($lastAttendance && $lastAttendance->attendance_date) {
                    $user->last_attendance_date = Carbon::parse($lastAttendance->attendance_date);
                    $user->last_attendance_display = $user->last_attendance_date->format('d M, Y');
                } else {
                    // တစ်ခါမှ အတန်းမတက်ရသေးပါက Package စဝယ်သည့်ရက်မှ စတွက်မည်
                    $firstPurchase = $user->purchases->min('created_at');
                    $user->last_attendance_date = $firstPurchase ? Carbon::parse($firstPurchase) : Carbon::now();
                    $user->last_attendance_display = 'Never Attended';
                }

                $user->absent_days = Carbon::now()->startOfDay()->diffInDays($user->last_attendance_date->startOfDay());

                return $user;
            })
            ->filter(function ($user) use ($startDate, $endDate) {
                // Last Attended Date သည် ရွေးချယ်ထားသော Date Range (From - To) ကြားတွင် ရှိမရှိ စစ်ထုတ်ခြင်း
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();

                return $user->last_attendance_date->between($start, $end);
            })
            ->sortByDesc('absent_days'); // ပျက်ရက်အများဆုံးသူကို ထိပ်ဆုံးမှာ ပြမည်

        return view('user.absent_users', compact('absentUsers', 'startDate', 'endDate'));
    }
}
