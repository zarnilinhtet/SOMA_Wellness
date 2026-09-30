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
        $instructors = Instructor::with(['user', 'categoryFees.category', 'payments'])->get();
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
            'payment_date' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'maintenance_fees' => 'nullable|numeric',
            'class_teaching_fees' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $instructor = Instructor::create([
                'instructor_id' => $request->instructor_id,
                'instructor_type' => $request->instructor_type,
                'specialty' => $request->has('specialty') ? json_encode($request->specialty) : null,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'maintenance_fees' => $request->maintenance_fees ?? 0,
                'class_teaching_fees' => $request->class_teaching_fees ?? 0,
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
            'payment_date' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'maintenance_fees' => 'nullable|numeric',
            'class_teaching_fees' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $instructor) {
            $instructor->update([
                'instructor_id' => $request->instructor_id,
                'instructor_type' => $request->instructor_type,
                'specialty' => $request->has('specialty') ? json_encode($request->specialty) : null,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'maintenance_fees' => $request->maintenance_fees ?? 0,
                'class_teaching_fees' => $request->class_teaching_fees ?? 0,
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
                $feeValue = $data['fee_value'] ?? 0;

                $bonuses = [];
                if (isset($data['bonuses']) && is_array($data['bonuses'])) {
                    foreach ($data['bonuses'] as $bonus) {
                        if (isset($bonus['min_students']) && $bonus['min_students'] !== '' && isset($bonus['bonus_amount']) && $bonus['bonus_amount'] !== '') {
                            $bonuses[] = [
                                'min_students' => (int) $bonus['min_students'],
                                'max_students' => isset($bonus['max_students']) && $bonus['max_students'] !== '' ? (int) $bonus['max_students'] : null,
                                'bonus_type' => $bonus['bonus_type'] ?? 'fixed',
                                'bonus_amount' => (float) $bonus['bonus_amount'],
                            ];
                        }
                    }
                    if (count($bonuses) > 0) {
                        // Sort by minimum students
                        usort($bonuses, function ($a, $b) {
                            return $a['min_students'] <=> $b['min_students'];
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
