<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Instructor;
use App\Models\Purchase;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendanceData(Request $request)
    {
        if (auth()->user()->hasRole("Instructor")) {
            $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
            $instructorId = $instructor->id;

            $query = Attendance::where('instructor_id', $instructorId)->whereNull('client_id');

        } else {
            $query = Attendance::where('instructor_id', '!=', NULL)->whereNull('client_id');
            logger($query->toSql());
        }

        // Filter by class_id if provided
        if (!empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by instructor_id if provided
        if (!empty($request->instructor_id)) {
            $query->where('instructor_id', $request->instructor_id);
        }

        // Fetch the filtered attendance records
        $attendances = $query->with(['instructor.user', 'class'])->get();

        $events = [];
        foreach ($attendances as $att) {
            // Since attendance_date is a date-only field (Y-m-d), we use it directly as the start date
            if ($att->attendance_date) {
                $events[] = [
                    'title' => ($att->instructor->user->name ?? 'Unknown') . ' - Attended',
                    'start' => $att->created_at ? $att->created_at->format('Y-m-d H:i:s') : $att->attendance_date,
                    'color' => '#28a745',// Green for recorded attendance
                    'recorded_at' => $att->created_at ? $att->created_at->format('Y-m-d H:i:s') : null,
                    'is_paid' => $att->is_paid,
                ];
            }
        }

        logger($events);
        return response()->json($events);
    }

    public function attendanceDayDetails(Request $request)
    {
        if (!$request->has('date')) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }

        $date = $request->date; // Layout string format: YYYY-MM-DD

        // Eager load instructor and user details + class details
        if (auth()->user()->hasRole("Instructor")) {
            $query = Attendance::with(['instructor.user', 'class'])
                ->whereDate('attendance_date', $date)
                ->whereNull('client_id')
                ->when(auth()->user()->hasRole("Instructor"), function ($q) {
                    $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
                    return $q->where('instructor_id', $instructor->id);
                });
        } else {
            $query = Attendance::with(['instructor.user', 'class'])->where('instructor_id', '!=', NULL)->whereNull('client_id')
                ->whereDate('attendance_date', $date);
        }

        // Apply dynamic conditional filter for class_id
        if (!empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        // Apply dynamic conditional filter for instructor_id
        if (!empty($request->instructor_id)) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $attendances = $query->get();
        $formattedData = [];

        foreach ($attendances as $att) {
            $formattedData[] = [
                'instructor_name' => $att->instructor->user->name ?? 'Unknown Instructor',
                'class_name' => $att->class->class_name ?? 'Unknown Class',
                'attendance_date' => $att->attendance_date,
                'recorded_at' => $att->created_at ? $att->created_at->format('Y-m-d H:i:s') : null,
                'is_paid' => $att->is_paid ? 'Paid' : 'Unpaid',
                'attendance_id' => $att->id,
            ];
        }
        return response()->json($formattedData);
    }

    public function record()
    {
        if (auth()->user()->hasRole("Instructor")) {
            $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
            $instructorId = (string) $instructor->id;
            $classes = ClassSchedule::with('category')->get();

            // Filter the collection
            $filteredSchedules = $classes->filter(function ($schedule) use ($instructorId) {
                return isset($schedule->instructor_ids) && in_array($instructorId, $schedule->instructor_ids);
            });

            foreach ($filteredSchedules as $schedule) {
                $schedule->instructor = Instructor::with('user')->whereIn('id', $schedule->instructor_ids ?? [])->get()->toArray();
            }
        } else {
            $classes = ClassSchedule::get();
        }

        $instructors = Instructor::with('user')->get();
        return view('backends.attendance.record', compact('classes', 'instructors'));
    }


    public function index()
    {
        if (auth()->user()->hasRole("Instructor")) {
            $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
            $instructorId = (string) $instructor->id;
            $att = ClassSchedule::with('category')->get();

            // Filter the collection
            $filteredSchedules = $att->filter(function ($schedule) use ($instructorId) {
                return isset($schedule->instructor_ids) && in_array($instructorId, $schedule->instructor_ids);
            });

            // 1. Collect all necessary IDs from the filtered schedules to avoid querying inside the loop
            $allInstructorIds = $filteredSchedules->pluck('instructor_ids')->flatten()->unique()->toArray();
            $allScheduleIds = $filteredSchedules->pluck('id')->toArray();

            // 2. Fetch all relevant attendance records in ONE query
            $allAttendances = Attendance::whereIn('instructor_id', $allInstructorIds)
                ->whereIn('class_id', $allScheduleIds)
                ->whereDate('attendance_date', now()->format('Y-m-d'))
                ->get()
                ->groupBy(function ($item) {
                    return $item->instructor_id . '_' . $item->class_id;
                });

            // 3. Map the data
            foreach ($filteredSchedules as $schedule) {
                $schedule->instructor = Instructor::with('user')
                    ->whereIn('id', $schedule->instructor_ids ?? [])
                    ->get();

                foreach ($schedule->instructor as $instructor) {
                    $key = $instructor->id . '_' . $schedule->id;

                    // Get the model
                    $attendanceModel = $allAttendances->get($key)?->first();

                    // Convert to array if it exists, otherwise set to null
                    $instructor->attendance = $attendanceModel ? $attendanceModel->toArray() : null;
                }
                $schedule->setRelation('instructor', $schedule->instructor);
            }

            // Reassign $att to the filtered results so the view iteration works properly
            $att = $filteredSchedules;
        } else {
            $att = ClassSchedule::with('category')->latest()->get();

            // 1. Collect all necessary IDs to avoid querying inside the loop
            $allInstructorIds = $att->pluck('instructor_ids')->flatten()->unique()->toArray();
            $allScheduleIds = $att->pluck('id')->toArray();

            // 2. Fetch all relevant attendance records in ONE query
            $allAttendances = Attendance::whereIn('instructor_id', $allInstructorIds)
                ->whereIn('class_id', $allScheduleIds)
                ->whereDate('attendance_date', now()->format('Y-m-d'))
                ->get()
                ->groupBy(function ($item) {
                    return $item->instructor_id . '_' . $item->class_id;
                });

            // 3. Map the data
            foreach ($att as $schedule) {
                $schedule->instructor = Instructor::with('user')
                    ->whereIn('id', $schedule->instructor_ids ?? [])
                    ->get();

                foreach ($schedule->instructor as $instructor) {
                    $key = $instructor->id . '_' . $schedule->id;

                    // Get the model
                    $attendanceModel = $allAttendances->get($key)?->first();

                    // Convert to array if it exists, otherwise set to null
                    $instructor->attendance = $attendanceModel ? $attendanceModel->toArray() : null;
                }
                $schedule->setRelation('instructor', $schedule->instructor);
            }
        }
        $record = Attendance::get();
        return view('backends.attendance.attendance', compact('att', 'record'));
    }

    // ၂။ အသစ်ထည့်မယ့် အခန်း
    public function inTime(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'instructor_id' => 'required',
            'attendance_date' => 'required'
        ]);

        $classId = $request->class_id;
        $instructorId = $request->instructor_id;
        $attendanceDate = $request->attendance_date;

        // 1. Record or update the instructor's own attendance marker
        Attendance::updateOrCreate(
            [
                'class_id' => $classId,
                'attendance_date' => $attendanceDate,
                'instructor_id' => $instructorId,
                'client_id' => null,
            ],
            [
                'attended' => true,
            ]
        );

        if (!auth()->user()->hasRole("Instructor")) {
            // 2. Find all students who ALREADY checked into this class today
            $existingStudentAttendances = Attendance::where('class_id', $classId)
                ->where('attendance_date', $attendanceDate)
                ->whereNotNull('client_id')
                ->select('client_id')
                ->distinct()
                ->get();

            // 3. Get this newly checking-in teacher's percentage commission
            $classInstructor = Instructor::where('id', $instructorId)->first();
            $feePercentage = $classInstructor->fee ?? 0;

            // 4. Retroactively apply fees for this teacher for every student already present
            foreach ($existingStudentAttendances as $studentAtt) {
                $booking = Booking::with('package')
                    ->where('selected_class_id', $classId)
                    ->where('status', 'confirmed')
                    ->first();
                $calculatedFee = ($booking->package->price * $feePercentage) / 100;

                Attendance::updateOrCreate(
                    [
                        'class_id' => $classId,
                        'attendance_date' => $attendanceDate,
                        'client_id' => $studentAtt->client_id,
                        'instructor_id' => $instructorId,
                    ],
                    [
                        'attended' => true,
                        'fee_amount' => $calculatedFee,
                        'admin_approve' => true,
                        'is_paid' => false,
                    ]
                );
            }
        }

        return back()->with('success', 'Instructor attendance recorded and retroactive fees applied!');
    }

    /**
     * Record Student Attendance (Checks if at least one instructor has checked in)
     */
    public function ClientinTime(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'client_ids' => 'required|array',
            'attendance_date' => 'required|date',
        ]);

        $classId = $request->class_id;
        $attendanceDate = $request->attendance_date;

        // 1. Instructor Role Check
        if (auth()->user()->hasRole("Instructor")) {
            $instructorModel = Instructor::where('instructor_id', auth()->user()->id)->first();

            $inst = Attendance::where('class_id', $classId)
                ->where('attendance_date', $attendanceDate)
                ->where('admin_approve', true)
                ->where('instructor_id', $instructorModel?->id)
                ->whereNull('client_id')
                ->exists();

            if (!$inst) {
                $msg = 'You have not checked in for this class yet or are waiting for Admin Approval!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }
        }

        // 2. CRITICAL CHECK: Verify if AT LEAST ONE teacher has checked in
        $checkedInTeachers = Attendance::where('class_id', $classId)
            ->where('attendance_date', $attendanceDate)
            ->where('admin_approve', true)
            ->whereNotNull('instructor_id')
            ->whereNull('client_id')
            ->get();

        if ($checkedInTeachers->isEmpty()) {
            $msg = 'No instructors have checked in for this class yet or waiting for Admin Approval!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 3. Fetch Class details to identify its Category ID
        // Note: Ensure your ClassModel (e.g., ClassSchedule or FitnessClass) has category_id or category relationship
        $classObj = \App\Models\ClassSchedule::with('category')->find($classId); // Adjust model name if needed
        $categoryId = $classObj?->category_id;

        // Load Instructors along with their category fee configurations
        $instructorIds = $checkedInTeachers->pluck('instructor_id')->unique();
        $instructors = Instructor::with('categoryFees')
            ->whereIn('id', $instructorIds)
            ->get()
            ->keyBy('id');

        // 4. Loop through clients and calculate category-based fee
        foreach ($request->client_ids as $clientId) {
            $booking = Booking::with('package')
                ->where('registered_id', $clientId)
                ->where('selected_class_id', $classId)
                ->where('status', 'confirmed')
                ->first();

            $packagePrice = $booking?->package?->price ?? 0;

            foreach ($checkedInTeachers as $teacherAtt) {
                $teacherId = $teacherAtt->instructor_id;
                $classInstructor = $instructors->get($teacherId);

                $calculatedFee = 0;

                if ($classInstructor && $categoryId) {
                    // Find category fee setting for this instructor & category
                    $categoryFeeSetting = $classInstructor->categoryFees
                        ->firstWhere('category_id', $categoryId);

                    if ($categoryFeeSetting) {
                        if ($categoryFeeSetting->fee_type === 'percentage') {
                            // Formula: (Package Price * Percentage) / 100
                            $calculatedFee = ($packagePrice * $categoryFeeSetting->fee_value) / 100;
                        } elseif ($categoryFeeSetting->fee_type === 'fixed') {
                            // Fixed Amount per attended client / class unit
                            $calculatedFee = $categoryFeeSetting->fee_value;
                        }
                    }
                }

                Attendance::updateOrCreate(
                    [
                        'class_id' => $classId,
                        'attendance_date' => $attendanceDate,
                        'client_id' => $clientId,
                        'instructor_id' => $teacherId,
                    ],
                    [
                        'attended' => true,
                        'fee_amount' => $calculatedFee,
                        'is_paid' => false,
                    ]
                );
            }
        }

        $successMsg = 'Client Attendance recorded and category fee rates applied!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMsg
            ], 200);
        }

        return back()->with('success', $successMsg);
    }

    public function attendanceStats()
    {
        $totalSessions = Attendance::where('client_id', auth()->user()->id)
            ->where('attended', true)
            ->count();
        $monthlyHistory = [];
        $maxCount = 1; // Prevents division by zero for relative progress bars

        for ($i = 0; $i < 12; $i++) {
            $monthDate = Carbon::now()->subMonths($i);

            $count = Attendance::where('client_id', auth()->user()->id)
                ->where('attended', true)
                ->whereYear('date', $monthDate->year)
                ->whereMonth('date', $monthDate->month)
                ->count();

            if ($count > $maxCount) {
                $maxCount = $count;
            }

            $monthlyHistory[] = [
                'month' => $monthDate->format('M'),
                'count' => $count,
            ];
        }

        foreach ($monthlyHistory as &$item) {
            $item['percentage'] = $maxCount > 0 ? round(($item['count'] / $maxCount) * 100) : 0;
        }

        $recentSessions = Attendance::where('attended', true)->where('client_id', auth()->user()->id)
            ->where('date', '>=', Carbon::now()->subDays(30))->count();

        $stats = [
            'recentSessions' => $recentSessions,
            'monthlyHistory' => $monthlyHistory,
            'totalSessions' => $totalSessions,
        ];

        return response()->json($stats);
    }

    public function adminApprove(Request $request, $instructorId)
    {
        // Validate the request
        $request->validate([
            'class_id' => 'required',
        ]);

        // Find the attendance record for the instructor for the given class and date
        $attendance = Attendance::where('instructor_id', $instructorId)
            ->where('class_id', $request->class_id)
            ->whereDate('attendance_date', now()->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Attendance record not found for this instructor.');
        }

        // Update the admin_approve field to true
        $attendance->admin_approve = true;
        $attendance->save();

        ClassSchedule::where('id', $request->class_id)->update(['status' => 'completed']);

        return back()->with('success', 'Instructor attendance approved by admin.');
    }

    public function outTime(Request $request)
    {
        $request->validate(['employee_id' => 'required', 'attendance_date' => 'required']);

        // Find if a record exists for this employee for the date selected
        $selectedDate = \Carbon\Carbon::parse($request->attendance_date)->format('Y-m-d');

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('in_time', $selectedDate) // Using whereDate on the datetime column
            ->first();

        if ($attendance) {
            $attendance->update(['out_time' => $request->attendance_date]);
        } else {
            Attendance::create([
                'employee_id' => $request->employee_id,
                'out_time' => $request->attendance_date
            ]);
        }

        return back()->with('success', 'Check-out recorded!');
    }
}
