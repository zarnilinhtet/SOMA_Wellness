<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserPackageDiscount;
use App\Models\ClassSchedule;
use Carbon\Carbon; // Added for accurate date validations
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
                'plain_password' => $request->password, // Admin ကြည့်ရန်အတွက် အစစ်အတိုင်းသိမ်းခြင်း
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

        // Password အသစ်ရိုက်ထည့်ထားမှသာ အသစ်ပြောင်းပေးမည်
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->plain_password = $request->password; // Admin ကြည့်ရန်အတွက်ပါ အသစ်ပြောင်းပေးခြင်း
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
        // Payment confirmed ဖြစ်ထားသော User IDs များကို ယူခြင်း
        $purchasedUserIds = Purchase::where('pay_status', 'confirmed')
            ->pluck('registered_id')
            ->unique()
            ->toArray();

        // User များနှင့် သက်ဆိုင်ရာ Roles, Onboarding, Purchases (+ Package) များကို ခေါ်ယူခြင်း
        $users = User::whereIn('id', $purchasedUserIds)
            ->with(['roles', 'onboarding', 'purchases' => function ($query) {
                $query->where('pay_status', 'confirmed')->with('package');
            }])
            ->latest()
            ->get();

        // User တစ်ယောက်စီအတွက် လိုအပ်သော Data များကို တွက်ချက်ခြင်း
        $users->map(function ($user) {
            // Active Package ရှိ/မရှိ စစ်ဆေးခြင်း (ကျန်ရှိသော အတန်းအရေအတွက် 0 ထက်ကြီးရင် Active)
            $hasActivePackage = $user->purchases->contains(function ($purchase) {
                // Safeguard for inconsistent column naming
                $remaining = $purchase->remaining_classes ?? $purchase->class_remaining ?? 0;
                return $remaining > 0;
            });

            // Package Status သတ်မှတ်ခြင်း
            $user->package_status = $hasActivePackage ? 'Using Package' : 'Completed';

            // Modal တွင်ပြသရန် Class Counts များ တွက်ချက်ခြင်း (ဝယ်ထားသမျှ Package အားလုံးပေါင်း)
            $user->total_classes = $user->purchases->sum('total_classes');

            // Calculate remaining safely across both possible column names
            $user->remaining_classes = $user->purchases->reduce(function ($carry, $purchase) {
                return $carry + ($purchase->remaining_classes ?? $purchase->class_remaining ?? 0);
            }, 0);

            $user->used_classes = $user->total_classes - $user->remaining_classes;

            return $user;
        });

        $userTypes = Role::all();
        $allPackages = Package::all();

        // Class Schedule များကို ယူခြင်း
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
            // Handle inconsistent column names dynamically
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

    /**
     * Check if a user has an active package eligible for a specific class
     */
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

        // Fetch user's confirmed purchases with package details
        $purchases = Purchase::with('package')
            ->where('registered_id', $user->id)
            ->where('pay_status', 'confirmed')
            ->get();

        $hasEligiblePackage = false;

        foreach ($purchases as $purchase) {
            // 1. Check Remaining Classes (Checks BOTH column names to prevent typos)
            $remaining = $purchase->remaining_classes ?? $purchase->class_remaining ?? 0;
            $classCount = $purchase->package->class_count ?? 0;

            if ($remaining <= 0) {
                continue; // Skip if no classes left in this package
            }

            // 2. Check Expiration Dates
            $isExpired = false;

            if ($purchase->expires_at && Carbon::parse($purchase->expires_at) < $now) {
                $isExpired = true;
            }

            if ($remaining == $classCount && $purchase->fix_expires_at && Carbon::parse($purchase->fix_expires_at) < $now) {
                $isExpired = true;
            }

            if ($isExpired) {
                continue; // Skip if this package is expired
            }

            // 3. Check Category Match
            $packageCategoryId = $purchase->package->category_id ?? null;

            // If the package is tied to a specific category, it MUST match the class category.
            // If the package category is NULL or 0, we assume it's an "All Access/General" package.
            if ($packageCategoryId && $packageCategoryId != $classCategoryId) {
                continue; // Skip because categories do not match
            }

            // If it passes all checks, the user is eligible!
            $hasEligiblePackage = true;
            break; // Stop looping, we found a valid package
        }

        if ($hasEligiblePackage) {
            return response()->json(['status' => true, 'message' => 'User is eligible.']);
        }

        return response()->json([
            'status' => false,
            'message' => 'User does not have an active package matching this class category.'
        ]);
    }
}
