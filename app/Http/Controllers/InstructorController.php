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
        $categories = Category::all(); // Fetch all categories
        $instRole = User::role('Instructor')->latest()->get();

        return view('backends.instructors.instructors_index', compact('instructors', 'categories', 'instRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|integer|unique:instructors,instructor_id',
            'specialty' => 'nullable|string|max:255',
            'category_fees' => 'nullable|array',
            'category_fees.*.fee_type' => 'required|in:percentage,fixed',
            'category_fees.*.fee_value' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $instructor = Instructor::create([
                'instructor_id' => $request->instructor_id,
                'specialty' => $request->specialty,
            ]);

            if ($request->has('category_fees')) {
                foreach ($request->category_fees as $categoryId => $data) {
                    if (isset($data['selected']) && $data['selected'] == '1' && !empty($data['fee_value'])) {
                        InstructorCategoryFee::create([
                            'instructor_id' => $instructor->id,
                            'category_id' => $categoryId,
                            'fee_type' => $data['fee_type'],
                            'fee_value' => $data['fee_value'],
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Instructor registered with category fees successfully.');
    }

    public function update(Request $request, Instructor $instructor)
    {
        $request->validate([
            'instructor_id' => [
                'required',
                'integer',
                Rule::unique('instructors', 'instructor_id')->ignore($instructor->id),
            ],
            'specialty' => 'nullable|string|max:255',
            'category_fees' => 'nullable|array',
            'category_fees.*.fee_type' => 'required|in:percentage,fixed',
            'category_fees.*.fee_value' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $instructor) {
            $instructor->update([
                'instructor_id' => $request->instructor_id,
                'specialty' => $request->specialty,
            ]);

            // Sync category fees
            InstructorCategoryFee::where('instructor_id', $instructor->id)->delete();

            if ($request->has('category_fees')) {
                foreach ($request->category_fees as $categoryId => $data) {
                    if (isset($data['selected']) && $data['selected'] == '1' && !empty($data['fee_value'])) {
                        InstructorCategoryFee::create([
                            'instructor_id' => $instructor->id,
                            'category_id' => $categoryId,
                            'fee_type' => $data['fee_type'],
                            'fee_value' => $data['fee_value'],
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Instructor fee configurations updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();
        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }
}