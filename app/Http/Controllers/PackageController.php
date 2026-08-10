<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ClassSchedule;
use App\Models\LoyalPoint;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        // အသစ်ထည့်ထားသော Package များကို အပေါ်ဆုံးမှ ပြရန် latest() သုံးထားသည်
        $packages = Package::with('category')->get();
        $categories = Category::all();
        return view('backends.packages.package_index', compact('packages', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'type' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'fix_duration' => 'required|integer',
            'class_count' => 'required|integer',
            'status' => 'required|string',
            'loyal_point' => 'required|string',
            'loyal_duration' => 'required|integer',
        ]);

        $package = Package::create($request->all());

        return redirect()->back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, Package $Package)
    {
        $request->validate([
            // နာမည်တူရှိမရှိ စစ်ဆေးမည် (မိမိကိုယ်တိုင်၏ ID ကိုမူ ချန်လှပ်ထားမည်)
            'name' => 'required|string|max:255|unique:packages,name,' . $Package->id,
            'type' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'fix_duration' => 'required|integer',
            'class_count' => 'required|integer',
            'status' => 'required|string',
            'loyal_point' => 'required|string',
            'loyal_duration' => 'required|integer',
        ]);

        $Package->update($request->all());

        return redirect()->back()->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $Package)
    {
        $Package->delete();
        return redirect()->back()->with('success', 'Package deleted successfully.');
    }


    public function buyPackgeIndex($id)
    {
        $packages = Package::all();
        $payments = Payment::all();
        $user = User::where('id', $id)->first();
        return view('backends.packages.buyPackageIndex', compact('packages', 'payments', 'user'));
    }

    public function buyPackge(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'package_id' => 'required',
            'account_name' => 'required|string',
            'phone_no' => 'required',
            'price' => 'required',
            'transaction_no' => 'required',
            'payment_id' => 'required',
            'user_discount' => 'required',
        ]);
        Purchase::create([
            'registered_id' => $request->user_id,
            'selected_packages_id' => $request->package_id,
            'account_name' => $request->account_name,
            'amount' => $request->price,
            'phone' => $request->phone_no,
            'transaction_no' => $request->transaction_no,
            'payment_method' => $request->payment_id,
            'user_discount' => $request->user_discount,
            'coin_used' => $request->coin_used,
            'class_remaining' => Package::findOrFail($request->package_id)->class_count,
            'expires_at' => now()->addDays(Package::findOrFail($request->package_id)->duration),
            'fix_expires_at' => now()->addDays(Package::findOrFail($request->package_id)->fix_duration),
        ]);
        if ($request->coin_used > 0) {
            $userId = $request->user_id;
            $packageId = $request->package_id;
            $coinsRequested = (float) $request->coin_used;

            // Basic sanity check before touching the DB
            if ($coinsRequested <= 0) {
                return back()->withErrors(['coin_used' => 'Please enter a valid coin amount to redeem.']);
            }

            // Wrap in DB transaction to ensure atomic execution
            DB::transaction(function () use ($userId, $coinsRequested) {

                // 1. Lock the user row for update to prevent concurrent spending (race conditions)
                $user = User::where('id', $userId)->lockForUpdate()->firstOrFail();

                // 2. Validate sufficient total balance
                if ($user->coins < $coinsRequested) {
                    throw new \Exception("Insufficient coins available for redemption.");
                }

                // 3. Deduct from total user balance directly
                $user->decrement('coins', $coinsRequested);

                // 4. Fetch valid, unexpired point records ordered FIFO (nearest expiry first)
                $activePoints = LoyalPoint::where('user_id', $userId)
                    ->where('is_redeemed', false)
                    ->where('expired_at', '>', now())
                    ->orderBy('expired_at', 'asc')
                    ->lockForUpdate() // Lock point records as well
                    ->get();

                $pointsToDeduct = $coinsRequested;

                // 5. Loop through and deduct FIFO
                foreach ($activePoints as $pointRecord) {
                    if ($pointsToDeduct <= 0) {
                        break;
                    }

                    // Rounding prevents floating-point precision bugs
                    $availableInBatch = round((float) $pointRecord->points, 4);

                    if ($availableInBatch >= $pointsToDeduct) {
                        // Batch can cover the rest of the deduction
                        $remaining = round($availableInBatch - $pointsToDeduct, 4);

                        $pointRecord->points = $remaining;
                        $pointRecord->is_redeemed = ($remaining <= 0);
                        $pointRecord->save();

                        $pointsToDeduct = 0; // Fully satisfied
                    } else {
                        // Batch isn't enough; exhaust it and carry over remaining amount
                        $pointsToDeduct = round($pointsToDeduct - $availableInBatch, 4);

                        $pointRecord->points = 0;
                        $pointRecord->is_redeemed = true;
                        $pointRecord->save();
                    }
                }

                // 6. Safeguard: throw error if user's batch points fell short of total coins balance
                if ($pointsToDeduct > 0) {
                    throw new \Exception("Mismatch between total user balance and active points batches.");
                }
            });
        }
        return redirect()->route('purchases.manage.page')->with('success', 'You made purchase for user.');
    }


    public function giveDiscount($id)
    {
        $user = User::findOrFail($id);
        $packages = Package::all();
        return view('user.user_discount', compact('user', 'packages'));

    }
}
