<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Onboarding;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'starting_level' => 'required|string',
            'included_practices' => 'required|array',
            'preferred_times' => 'required|array',
            'considerations' => 'required|array',
            'selected_plan' => 'nullable|string',
            'rules_accepted' => 'required',
            'know_where' => 'required|string', // ✨ Add validation for know_where
        ]);

        // ✨ Register လုပ်ပြီးသား User ရဲ့ ID ဖြင့် ကွက်တိသွားရောက် သိမ်းဆည်းမည်
        Onboarding::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'starting_level' => $request->starting_level,
                'included_practices' => $request->included_practices,
                'preferred_times' => $request->preferred_times,
                'considerations' => $request->considerations,
                'selected_plan' => $request->selected_plan,
                'know_where' => $request->know_where,
                'rules_accepted' => filter_var($request->rules_accepted, FILTER_VALIDATE_BOOLEAN),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Onboarding completed successfully.'
        ]);
    }


    public function paymentPolicySave(Request $request)
    {
        $request->validate([
            'payment_policy_accepted' => 'required|boolean',
        ]);

        // ✨ Register လုပ်ပြီးသား User ရဲ့ ID ဖြင့် ကွက်တိသွားရောက် သိမ်းဆည်းမည်
        Onboarding::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'payment_policy_accepted' => $request->payment_policy_accepted,
            ]
        );

        return redirect()->back();
    }
}
