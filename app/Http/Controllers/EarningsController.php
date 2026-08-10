<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\earnings;
use App\Models\Instructor;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function getInstructorEarnings(Request $request)
    {
        if (auth()->user()->hasRole("Instructor")) {
            $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
            $instructorId = (string) $instructor->id;
            $earnings = Attendance::whereHas('class', function ($query) use ($instructorId) {
                $query->where(function ($subQuery) use ($instructorId) {
                    $subQuery->orWhereJsonContains('instructor_ids', $instructorId);
                });
            })->with('class', 'instructor')->where('instructor_id', $instructorId)->get();
        } else {
            $allInstructorIds = Instructor::get()->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $earnings = Attendance::whereHas('class', function ($query) use ($allInstructorIds) {
                $query->where(function ($subQuery) use ($allInstructorIds) {
                    foreach ($allInstructorIds as $id) {
                        $subQuery->orWhereJsonContains('instructor_ids', $id);
                    }
                });
            })->with('class', 'instructor')->get();
        }
        return view('backends.earnings.earnings_index', compact('earnings'));
    }

    public function updateInstructorEarnings(Request $request, $instructorId)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'status' => 'required|in:paid,unpaid',
        ]);

        $attendance = Attendance::findOrFail($request->input('attendance_id'));
        $attendance->is_paid = $request->input('status') === 'paid' ? 1 : 0;
        $attendance->save();

        return redirect()->back()->with('success', 'Instructor earnings status updated successfully.');
    }
}
