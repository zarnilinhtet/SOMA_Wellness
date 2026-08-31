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
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('category')->latest()->get();
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

        Package::create($request->all());

        return redirect()->back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, Package $Package)
    {
        $request->validate([
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
            'price' => 'required',
            'payment_id' => 'required',
            'user_discount' => 'required',
            'receiver_name' => 'required|string',
        ]);

        $packageModel = Package::findOrFail($request->package_id);

        // ==============================================
        // QUEUE LOGIC FOR ADMIN
        // ==============================================
        $latestPurchase = Purchase::where('registered_id', $request->user_id)
            ->whereHas('package', function ($q) use ($packageModel) {
                $q->where('type', $packageModel->type); // Same Category Match
            })
            ->orderBy('fix_expires_at', 'desc')
            ->first();

        $baseDate = now();
        if ($latestPurchase && $latestPurchase->fix_expires_at) {
            $latestFixDate = Carbon::parse($latestPurchase->fix_expires_at);
            if ($latestFixDate->isFuture() && $latestPurchase->class_remaining > 0) {
                $baseDate = $latestFixDate; // ရှိပြီးသား Package နောက်သို့ Queue လုပ်ရန်
            }
        }

        DB::transaction(function () use ($request, $packageModel, $baseDate) {
            Purchase::create([
                'registered_id' => $request->user_id,
                'selected_packages_id' => $request->package_id,
                'account_name' => $request->account_name,
                'amount' => $request->price,
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone ?? null,
                'phone' => $request->phone_no ?? null,
                'transaction_no' => $request->transaction_no ?? 'CASH-' . strtoupper(uniqid()),
                'payment_method' => $request->payment_id,
                'user_discount' => $request->user_discount,
                'coin_used' => $request->coin_used ?? 0,
                'class_remaining' => $packageModel->class_count,
                'expires_at' => $baseDate->copy()->addDays($packageModel->duration),
                'fix_expires_at' => $baseDate->copy()->addDays($packageModel->fix_duration),
            ]);

            if ($request->coin_used > 0) {
                $userId = $request->user_id;
                $coinsRequested = (float) $request->coin_used;

                if ($coinsRequested <= 0) {
                    throw new \Exception("Please enter a valid coin amount to redeem.");
                }

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

                if ($pointsToDeduct > 0) {
                    throw new \Exception("Mismatch between total user balance and active points batches.");
                }
            }
        });

        return redirect()->route('purchases.manage.page')->with('success', 'You made purchase for user.');
    }

    public function giveDiscount($id)
    {
        $user = User::findOrFail($id);
        $packages = Package::all();
        return view('user.user_discount', compact('user', 'packages'));
    }
}
