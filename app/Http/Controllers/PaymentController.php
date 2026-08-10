<?php

namespace App\Http\Controllers;

use App\Models\LoyalPoint;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::latest()->get();
        return view('backends.payments.payments_index', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method' => 'required|string|max:255',
            'account_info' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
        ]);

        $data = $request->all();

        // Image Compress လုပ်၍ သိမ်းခြင်း
        if ($request->hasFile('image')) {
            $data['image'] = $this->compressAndSaveImage($request->file('image'), 'uploads/payments');
        }

        Payment::create($data);

        return redirect()->back()->with('success', 'Payment account added successfully.');
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method' => 'required|string|max:255',
            'account_info' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->all();

        // ==========================================
        // Remove Image Logic (X button နှိပ်ခဲ့လျှင်)
        // ==========================================
        if ($request->remove_image == '1') {
            if ($payment->image && File::exists(public_path($payment->image))) {
                File::delete(public_path($payment->image));
            }
            $data['image'] = null;
        }

        // ==========================================
        // Upload New Image Logic (ပုံအသစ်တင်ခဲ့လျှင်)
        // ==========================================
        if ($request->hasFile('image')) {
            if ($payment->image && File::exists(public_path($payment->image))) {
                File::delete(public_path($payment->image));
            }
            $data['image'] = $this->compressAndSaveImage($request->file('image'), 'uploads/payments');
        }

        $payment->update($data);

        return redirect()->back()->with('success', 'Payment account updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->image && File::exists(public_path($payment->image))) {
            File::delete(public_path($payment->image));
        }

        $payment->delete();
        return redirect()->back()->with('success', 'Payment account deleted successfully.');
    }

    /**
     * Image Auto Compress Function
     */
    private function compressAndSaveImage($file, $path, $quality = 60)
    {
        if (!File::exists(public_path($path))) {
            File::makeDirectory(public_path($path), 0777, true);
        }

        $info = getimagesize($file);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($file);
                break;
            default:
                return false;
        }

        $filename = time() . '_' . uniqid() . '.jpg';
        $destination = public_path($path . '/' . $filename);

        imagejpeg($image, $destination, $quality);
        imagedestroy($image);

        return $path . '/' . $filename;
    }

    public function managePurchases(Request $request)
    {
        // Eager load relationships to optimize database queries
        $query = Purchase::with(['package', 'user'])->latest();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Default to current week if no query parameters exist
        if (empty($request->query())) {
            $startDate = now()->startOfWeek()->format('Y-m-d');
            $endDate = now()->endOfWeek()->format('Y-m-d');
        }

        // Apply Date Filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('pay_status', $request->status);
        }

        $transactions = $query->get();

        return view('backends.payments.purchases_index', compact('transactions', 'startDate', 'endDate'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'pay_status' => 'required|in:confirmed,rejected',
            'rejection_reason' => 'required_if:pay_status,rejected|nullable|string|max:500'
        ]);

        $transaction = Purchase::findOrFail($id);
        $transaction->pay_status = $request->pay_status;

        if ($request->pay_status === 'rejected') {
            $transaction->rejection_reason = $request->rejection_reason;
        } else {
            // Business Logic: Enroll user to class if confirmed
            // $transaction->user->enrolledClasses()->attach($transaction->course_id);
        }

        $transaction->save();
        if ($transaction->pay_status == 'confirmed') {
            User::where('id', $transaction->registered_id)->update([
                'coins' => User::where('id', $transaction->registered_id)->value('coins') + Package::findOrFail($transaction->selected_packages_id)->loyal_point
            ]);

            LoyalPoint::create([
                'user_id' => $transaction->registered_id,
                'package_id' => $transaction->selected_packages_id,
                'points' => Package::findOrFail($transaction->selected_packages_id)->loyal_point,
                'expired_at' => now()->addDays(Package::findOrFail($transaction->selected_packages_id)->loyal_duration),
            ]);
        }
        $data = [
            'title' => 'Package Purchase Status!',
            'message' => 'Updated status of your package purchase',
            'url' => 'history.page'
        ];

        $user = User::where('id', $transaction->registered_id)->first();
        $user->notify(new AdminNotification($data));

        return redirect()->back()->with('success', "Transaction has been explicitly verified as {$transaction->pay_status}.");
    }

    /**
     * Remove the specified purchase transaction from storage.
     */
    public function destroyPurchase($id)
    {
        $transaction = Purchase::findOrFail($id);

        // Delete associated screenshot file if it exists
        if ($transaction->screenshot && File::exists(public_path($transaction->screenshot))) {
            File::delete(public_path($transaction->screenshot));
        }

        // Secondary check if 'receipt_image' column is used independently 
        if ($transaction->receipt_image && File::exists(public_path($transaction->receipt_image))) {
            File::delete(public_path($transaction->receipt_image));
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction successfully deleted.');
    }
}
