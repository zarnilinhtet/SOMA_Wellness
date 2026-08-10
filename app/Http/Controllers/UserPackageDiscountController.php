<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use App\Models\userPackageDiscount;
use Illuminate\Http\Request;

class UserPackageDiscountController extends Controller
{

    public function create($id)
    {
        // Fetch active users and packages for select inputs
        $user = User::select('id', 'name', 'phone')->orderBy('name')->where('id', $id)->first();
        $packages = Package::select('id', 'name', 'price')->orderBy('name')->get();

        return view('user.user_discount', compact('user', 'packages'));
    }

    /**
     * Store the user package discount(s).
     */
    public function store(Request $request)
    {

        // 1. Validate Form Input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_data' => 'required|array',
            'package_data.*.selected' => 'nullable|boolean',
            'package_data.*.discount_amount' => 'required_with:package_data.*.selected|nullable|numeric|min:0|max:100',
            'package_data.*.expiration_date' => 'required_with:package_data.*.selected|nullable|date|after_or_equal:today',
            'package_data.*.expiration_time' => 'required_with:package_data.*.selected|nullable|date_format:H:i',
        ], [
            'package_data.required' => 'Please select at least one package.',
            'package_data.*.discount_amount.required_with' => 'Please specify a discount amount for selected packages.',
            'package_data.*.expiration_date.required_with' => 'Please set an expiration date for selected packages.',
            'package_data.*.expiration_time.required_with' => 'Please set an expiration time for selected packages.',
        ]);

        $userId = $request->input('user_id');
        $packageData = $request->input('package_data', []);

        $assignedCount = 0;

        // 2. Loop Through Packages & Save Only Checked Packages
        foreach ($packageData as $packageId => $data) {
            if (!empty($data['selected'])) {
                UserPackageDiscount::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'package_id' => $packageId,
                    ],
                    [
                        'discount_amount' => $data['discount_amount'],
                        'expiration_date' => $data['expiration_date'],
                        'expiration_time' => $data['expiration_time'],
                    ]
                );
                $assignedCount++;
            }
        }

        if ($assignedCount === 0) {
            return redirect()->back()->withErrors(['packages' => 'Please select at least one package to assign discounts.'])->withInput();
        }

        return redirect()->route('user_register.index')->with('success', "Discounts successfully assigned for {$assignedCount} package(s)!");
    }

    /**
     * Remove the specified user package discount.
     */
    public function destroy($userId, $packageId)
    {
        $discount = UserPackageDiscount::where('user_id', $userId)
            ->where('package_id', $packageId)
            ->first();
        $discount->delete();
        return redirect()->back()->with('success', 'Package discount removed successfully.');
    }

    public function discountIndex($userId)
    {

        $user = User::where('id', $userId)->firstOrFail();
        if (!$user) {
            return redirect()->back()->withErrors(['user' => 'User not found.']);
        }

        // Fetch all packages for this user to populate the modal table
        $userPackages = UserPackageDiscount::with('package')
            ->where('user_id', $userId)
            ->get();

        return view('user.user_discount_index', compact('user', 'userPackages'));
    }
    public function update(Request $request, $userId, $packageId)
    {
        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:100',
            'expiration_date' => 'required|date',
            // Accepts both H:i (14:30) and H:i:s (14:30:00)
            'expiration_time' => 'nullable|date_format:H:i,H:i:s',
        ]);

        // Use first() so it returns null if missing
        $discount = UserPackageDiscount::where('user_id', $userId)
            ->where('package_id', $packageId)
            ->first();

        if (!$discount) {
            return redirect()->back()
                ->withErrors(['discount' => 'Discount Not found!'])
                ->withInput();
        }

        $discount->update([
            'discount_amount' => $request->discount_amount,
            'expiration_date' => $request->expiration_date,
            'expiration_time' => $request->expiration_time,
        ]);

        return redirect()->back()->with('success', 'Package discount updated successfully.');
    }
}
