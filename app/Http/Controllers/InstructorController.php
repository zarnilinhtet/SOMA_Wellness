<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Instructor;
use App\Models\InstructorCategoryFee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with(['user', 'categoryFees.category'])->get();
        $categories = Category::all();
        $instRole = User::role('Instructor')->latest()->get();

        return view('backends.instructors.instructors_index', compact('instructors', 'categories', 'instRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instructor_id' => [
                'required',
                'integer',
                // ပြဿနာမဖြစ်စေရန် deleted_at null ဖြစ်မှသာ unique စစ်မည်
                Rule::unique('instructors', 'instructor_id')->whereNull('deleted_at')
            ],
            'specialty' => 'nullable|string|max:255',
            'category_fees' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $instructor = Instructor::create([
                'instructor_id' => $request->instructor_id,
                'specialty' => $request->specialty,
            ]);

            if ($request->has('category_fees')) {
                foreach ($request->category_fees as $categoryId => $data) {
                    // Checkbox ရွေးထားပြီး Fee Value အလွတ်မဖြစ်မှသာ သိမ်းမည်
                    if (isset($data['selected']) && $data['selected'] == '1' && isset($data['fee_value']) && $data['fee_value'] !== null && $data['fee_value'] !== '') {

                        $bonuses = [];
                        if (isset($data['bonuses']) && is_array($data['bonuses'])) {
                            foreach ($data['bonuses'] as $bonus) {
                                // 0 ကို လက်ခံနိုင်ရန် !empty အစား isset ဖြင့် စစ်ထားပါသည်
                                if (isset($bonus['threshold']) && $bonus['threshold'] !== '' && isset($bonus['amount']) && $bonus['amount'] !== '') {
                                    $bonuses[] = [
                                        'threshold' => (int) $bonus['threshold'],
                                        'amount' => (float) $bonus['amount'],
                                    ];
                                }
                            }
                            // အကြီးဆုံး Threshold ကနေ စီရန်
                            if (count($bonuses) > 0) {
                                usort($bonuses, function ($a, $b) {
                                    return $b['threshold'] <=> $a['threshold'];
                                });
                            }
                        }

                        InstructorCategoryFee::create([
                            'instructor_id' => $instructor->id,
                            'category_id' => $categoryId,
                            'fee_type' => $data['fee_type'],
                            'fee_value' => $data['fee_value'],
                            'bonuses' => (count($bonuses) > 0) ? $bonuses : null,
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Instructor registered successfully.');
    }

    public function update(Request $request, Instructor $instructor)
    {
        $request->validate([
            'instructor_id' => [
                'required',
                'integer',
                Rule::unique('instructors', 'instructor_id')->ignore($instructor->id)->whereNull('deleted_at'),
            ],
            'specialty' => 'nullable|string|max:255',
            'category_fees' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $instructor) {
            $instructor->update([
                'instructor_id' => $request->instructor_id,
                'specialty' => $request->specialty,
            ]);

            // အဟောင်းဖျက် အသစ်ပြန်ထည့်မည်
            InstructorCategoryFee::where('instructor_id', $instructor->id)->delete();

            if ($request->has('category_fees')) {
                foreach ($request->category_fees as $categoryId => $data) {
                    // Checkbox ရွေးထားပြီး Fee Value အလွတ်မဖြစ်မှသာ သိမ်းမည်
                    if (isset($data['selected']) && $data['selected'] == '1' && isset($data['fee_value']) && $data['fee_value'] !== null && $data['fee_value'] !== '') {

                        $bonuses = [];
                        if (isset($data['bonuses']) && is_array($data['bonuses'])) {
                            foreach ($data['bonuses'] as $bonus) {
                                if (isset($bonus['threshold']) && $bonus['threshold'] !== '' && isset($bonus['amount']) && $bonus['amount'] !== '') {
                                    $bonuses[] = [
                                        'threshold' => (int) $bonus['threshold'],
                                        'amount' => (float) $bonus['amount'],
                                    ];
                                }
                            }
                            // အကြီးဆုံး Threshold ကနေ စီရန်
                            if (count($bonuses) > 0) {
                                usort($bonuses, function ($a, $b) {
                                    return $b['threshold'] <=> $a['threshold'];
                                });
                            }
                        }

                        InstructorCategoryFee::create([
                            'instructor_id' => $instructor->id,
                            'category_id' => $categoryId,
                            'fee_type' => $data['fee_type'],
                            'fee_value' => $data['fee_value'],
                            'bonuses' => (count($bonuses) > 0) ? $bonuses : null,
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Instructor configurations updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        DB::transaction(function () use ($instructor) {
            // Delete related Category Fees
            InstructorCategoryFee::where('instructor_id', $instructor->id)->delete();

            // Force Delete လုပ်ပေးမှသာ ထို User ကို Instructor အဖြစ်ပြန်လည် Register လုပ်နိုင်ပါမည်
            // Unique constraint error ကိုရှောင်ရှားရန်ဖြစ်သည်
            $instructor->forceDelete();
        });

        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }
}
