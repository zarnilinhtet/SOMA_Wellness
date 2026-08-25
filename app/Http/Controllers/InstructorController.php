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
                Rule::unique('instructors', 'instructor_id')->whereNull('deleted_at')
            ],
            'instructor_type' => 'required|in:full_time,part_time',
            'specialty' => 'nullable|array',
            'category_fees' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $instructor = Instructor::create([
                'instructor_id' => $request->instructor_id,
                'instructor_type' => $request->instructor_type,
                'specialty' => $request->has('specialty') ? json_encode($request->specialty) : null,
            ]);

            $this->saveCategoryFees($instructor->id, $request->category_fees);
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
            'instructor_type' => 'required|in:full_time,part_time',
            'specialty' => 'nullable|array',
            'category_fees' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $instructor) {
            $instructor->update([
                'instructor_id' => $request->instructor_id,
                'instructor_type' => $request->instructor_type,
                'specialty' => $request->has('specialty') ? json_encode($request->specialty) : null,
            ]);

            InstructorCategoryFee::where('instructor_id', $instructor->id)->delete();
            $this->saveCategoryFees($instructor->id, $request->category_fees);
        });

        return redirect()->back()->with('success', 'Instructor configurations updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        DB::transaction(function () use ($instructor) {
            InstructorCategoryFee::where('instructor_id', $instructor->id)->delete();
            $instructor->forceDelete();
        });

        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }

    private function saveCategoryFees($instructorId, $categoryFees)
    {
        if (!$categoryFees) return;

        foreach ($categoryFees as $categoryId => $data) {
            if (isset($data['selected']) && $data['selected'] == '1') {
                $feeType = $data['fee_type'] ?? 'full_time_fixed';

                // Tiered type ဆိုလျှင် base value မလိုပါ (0 ထားမည်)
                $feeValue = ($feeType === 'part_time_tiered') ? 0 : ($data['fee_value'] ?? 0);

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
                    if (count($bonuses) > 0) {
                        // ကျောင်းသားအရေအတွက်အများဆုံး (Threshold အကြီးဆုံး) ကနေ စီထားရန်
                        usort($bonuses, function ($a, $b) {
                            return $b['threshold'] <=> $a['threshold'];
                        });
                    }
                }

                InstructorCategoryFee::create([
                    'instructor_id' => $instructorId,
                    'category_id' => $categoryId,
                    'fee_type' => $feeType,
                    'fee_value' => $feeValue,
                    'bonuses' => (count($bonuses) > 0) ? $bonuses : null,
                ]);
            }
        }
    }
}
