<?php

namespace App\Http\Controllers;

use App\Models\InstructorPayment;
use Illuminate\Http\Request;

class InstructorPaymentController extends Controller
{
    public function store(Request $request, $instructorId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        InstructorPayment::create([
            'instructor_id' => $instructorId,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'remark' => $request->remark,
        ]);

        return redirect()->back()->with('success', 'Payment record added successfully.');
    }

    public function destroy(InstructorPayment $payment)
    {
        $payment->delete();
        return redirect()->back()->with('success', 'Payment record deleted successfully.');
    }
}
