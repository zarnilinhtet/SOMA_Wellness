<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserPackageDiscount;
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
        $purchasedUserIds = \App\Models\Purchase::where('pay_status', 'confirmed')
            ->pluck('registered_id')
            ->unique()
            ->toArray();

        $users = \App\Models\User::whereIn('id', $purchasedUserIds)
            ->with(['roles', 'onboarding'])
            ->latest()
            ->get();

        $userTypes = \Spatie\Permission\Models\Role::all();
        $allPackages = \App\Models\Package::all();

        return view('user.user_with_packages', compact('users', 'userTypes', 'allPackages'));
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
            $remaining = $purchase->class_remaining ?? 0;

            $totalClassesAllowed += $classCount;
            $totalClassesRemaining += $remaining;

            $purchase->used_classes = $classCount - $remaining;

            $isExpired = false;
            $now = now();

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
}
