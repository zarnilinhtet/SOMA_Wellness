<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with('user')->get();
        $instRole = User::role('Instructor')->latest()->get();
        return view('backends.instructors.instructors_index', compact('instructors', 'instRole'));
    }

    public function store(Request $request)
    {
        // Email validation ကို ဖြုတ်ပြီး Name သာလျှင် required ဖြစ်အောင် ပြင်ဆင်ထားသည်
        $request->validate([
            'instructor_id' => 'required|integer|unique:instructors,instructor_id',
            'specialty' => 'nullable|string|max:255',
            'fee' => 'required|integer|min:0',
        ]);

        Instructor::create($request->all());

        return redirect()->back()->with('success', 'Instructor registered successfully.');
    }

    public function edit(Instructor $instructor)
    {
        return view('backends.instructors.edit', compact('instructor'));
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
            'fee' => 'required|integer|min:0',
        ]);

        $instructor->update($request->all());

        return redirect()->back()->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();
        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }
}
