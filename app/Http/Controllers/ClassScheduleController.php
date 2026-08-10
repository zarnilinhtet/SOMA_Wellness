<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Instructor;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ClassScheduleController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole("Instructor")) {
            $instructor = Instructor::where('instructor_id', auth()->user()->id)->first();
            $instructorId = (string) $instructor->id;
            $schedules = ClassSchedule::with('category')->get();

            $filteredSchedules = $schedules->filter(function ($schedule) use ($instructorId) {
                return isset($schedule->instructor_ids) && in_array($instructorId, $schedule->instructor_ids);
            });

            foreach ($filteredSchedules as $schedule) {
                $schedule->instructor = Instructor::with('user')->whereIn('id', $schedule->instructor_ids ?? [])->get()->toArray();
            }
            $instructors = Instructor::with('user')->get();
            $categories = Category::all();
        } else {
            $schedules = ClassSchedule::with('category')->latest()->get();

            foreach ($schedules as $schedule) {
                $schedule->instructor = Instructor::with('user')->whereIn('id', $schedule->instructor_ids ?? [])->get()->toArray();
            }
            $instructors = Instructor::with('user')->get();
            $categories = Category::all();
        }
        return view('backends.class_schedules.class_schedules_index', compact('schedules', 'instructors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'category_id' => 'required|exists:categories,id',
            'class_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'days' => 'required|array',
            'days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'start_time' => 'required',
            'end_time' => 'nullable|after:start_time',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_1')) {
            $data['image_1'] = $this->compressAndSaveImage($request->file('image_1'), 'uploads/schedules');
        }
        if ($request->hasFile('image_2')) {
            $data['image_2'] = $this->compressAndSaveImage($request->file('image_2'), 'uploads/schedules');
        }

        ClassSchedule::create($data);

        return redirect()->back()->with('success', 'Class schedule created successfully.');
    }

    public function update(Request $request, ClassSchedule $classSchedule)
    {
        $request->validate([
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'category_id' => 'required|exists:categories,id',
            'class_name' => 'required|string|max:255',
            'days' => 'required|array',
            'days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'nullable|after:start_time',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->all();

        if ($request->remove_image_1 == '1') {
            if ($classSchedule->image_1 && File::exists(public_path($classSchedule->image_1))) {
                File::delete(public_path($classSchedule->image_1));
            }
            $data['image_1'] = null;
        }

        if ($request->remove_image_2 == '1') {
            if ($classSchedule->image_2 && File::exists(public_path($classSchedule->image_2))) {
                File::delete(public_path($classSchedule->image_2));
            }
            $data['image_2'] = null;
        }

        if ($request->hasFile('image_1')) {
            if ($classSchedule->image_1 && File::exists(public_path($classSchedule->image_1))) {
                File::delete(public_path($classSchedule->image_1));
            }
            $data['image_1'] = $this->compressAndSaveImage($request->file('image_1'), 'uploads/schedules');
        }

        if ($request->hasFile('image_2')) {
            if ($classSchedule->image_2 && File::exists(public_path($classSchedule->image_2))) {
                File::delete(public_path($classSchedule->image_2));
            }
            $data['image_2'] = $this->compressAndSaveImage($request->file('image_2'), 'uploads/schedules');
        }

        $classSchedule->update($data);

        return redirect()->back()->with('success', 'Class schedule updated successfully.');
    }

    public function destroy(ClassSchedule $classSchedule)
    {
        if ($classSchedule->image_1 && File::exists(public_path($classSchedule->image_1)))
            File::delete(public_path($classSchedule->image_1));
        if ($classSchedule->image_2 && File::exists(public_path($classSchedule->image_2)))
            File::delete(public_path($classSchedule->image_2));

        $classSchedule->delete();
        return redirect()->back()->with('success', 'Class schedule deleted successfully.');
    }

    public function cancel(ClassSchedule $classSchedule)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to cancel a class.');
        }

        ClassSchedule::where('id', $classSchedule->id)->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Class schedule canceled successfully.');
    }

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

    public function updateInstructors(Request $request, ClassSchedule $classSchedule)
    {
        $request->validate([
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
        ]);

        $classSchedule->instructor_ids = $request->input('instructor_ids');
        $classSchedule->save();

        return redirect()->back()->with('success', 'Instructors updated successfully.');
    }

    public function class_schedules_list(Request $request)
    {
        $now = Carbon::now();
        $defaultStartDate = $now->copy()->startOfWeek()->format('Y-m-d');
        $defaultEndDate = $now->copy()->endOfWeek()->format('Y-m-d');

        $fromDate = $request->input('from_date', $defaultStartDate);
        $toDate = $request->input('to_date', $defaultEndDate);

        $schedules = ClassSchedule::with(['category', 'bookings.user'])
            ->whereDate('start_date', '>=', $fromDate)
            ->whereDate('start_date', '<=', $toDate)
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        // Optimized Attendance Fetching (No date restriction so we can group by date in frontend)
        $scheduleIds = $schedules->pluck('id')->toArray();
        $attendances = Attendance::with(['instructor.user'])
            ->whereIn('class_id', $scheduleIds)
            ->get();

        // Pre-fetch all clients to avoid N+1 queries when loading user names
        $clientIds = $attendances->whereNotNull('client_id')->pluck('client_id')->unique();
        $clients = User::whereIn('id', $clientIds)->get()->keyBy('id');

        foreach ($schedules as $schedule) {
            $schedule->instructors = Instructor::with('user')
                ->whereIn('id', $schedule->instructor_ids ?? [])
                ->get();

            // REMOVED STRICT DATE RESTRICTION HERE to show all attended students in modal
            $scheduleAttendances = $attendances->where('class_id', $schedule->id);

            foreach ($scheduleAttendances as $att) {
                if ($att->client_id) {
                    $att->client_user = $clients->get($att->client_id);
                }
            }
            $schedule->attendances = $scheduleAttendances;
        }

        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->start_date)->format('l, F j');
        });

        return view('backends.class_schedules.schedules_list', compact('groupedSchedules', 'fromDate', 'toDate'));
    }

    public function joinClassForUser($id)
    {
        $user = User::findOrFail($id);
        $bookings = Booking::where('registered_id', $id)->get();
        $bookingsAll = Booking::all();
        $classes = ClassSchedule::with('category')->latest()->get();
        foreach ($classes as $class) {
            $class->instructor = Instructor::with('user')->whereIn('id', $class->instructor_ids ?? [])->get()->toArray();
        }
        return view('backends.class_schedules.join_class', compact('user', 'classes', 'bookings', 'bookingsAll'));
    }

    public function joinClassForUserSubmit(Request $request)
    {
        $classId = $request->class_id;
        $userId = $request->input('user_id', auth()->id());

        if (!$classId) {
            return redirect()->back()->with('warning', 'Invalid class selected.');
        }

        $class = ClassSchedule::find($classId);
        if (!$class) {
            return redirect()->back()->with('warning', 'Class not found.');
        }

        $categoryMatchExists = Purchase::whereHas('package', function ($query) use ($class) {
            $query->where('type', $class->category_id);
        })
            ->where('registered_id', $userId)
            ->exists();

        if (!$categoryMatchExists) {
            return redirect()->back()
                ->with('warning', 'The user does not have a package matching this category.');
        }

        $activePurchase = Purchase::whereHas('package', function ($query) use ($class) {
            $query->where('type', $class->category_id);
        })
            ->where('registered_id', $userId)
            ->where('pay_status', 'confirmed')
            ->where('class_remaining', '>', 0)
            ->where('expires_at', '>=', now())
            ->where(function ($query) {
                $query->whereRaw('class_remaining = (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)')
                    ->where('fix_expires_at', '>=', now())
                    ->orWhereRaw('class_remaining < (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)');
            })
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$activePurchase) {
            return redirect()->back()
                ->with('warning', 'No active package found with remaining credits for this class category.');
        }

        $existingBooking = Booking::where('registered_id', $userId)
            ->where('selected_class_id', $class->id)
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if ($existingBooking) {
            $statusMsg = $existingBooking->status === 'confirmed' ? 'already joined' : 'already on the waitlist for';
            return redirect()->back()->with('warning', "User has {$statusMsg} this class.");
        }

        $bookedCount = $class->bookings()
            ->where('status', 'confirmed')
            ->count();

        $classCapacity = $class->capacity ?? 0;

        return DB::transaction(function () use ($class, $activePurchase, $userId, $bookedCount, $classCapacity) {
            $booking = new Booking();
            $booking->package_id = $activePurchase->selected_packages_id;
            $booking->registered_id = $userId;
            $booking->selected_class_id = $class->id;

            if ($bookedCount >= $classCapacity) {
                $booking->status = 'waitlisted';
                $booking->save();

                $user = User::find($userId);
                $admins = User::where('name', 'System Admin')->get();
                $data = [
                    'title' => 'New Waitlist Request',
                    'message' => ($user->name ?? 'User') . ' requested a waitlist slot for ' . ($class->class_name ?? $class->name),
                    'url' => 'waitlist.index'
                ];

                return redirect()->back()
                    ->with('warning', 'Class is full. The user was added to the waiting list for admin approval.');
            }

            $booking->status = 'confirmed';
            $booking->save();

            $activePurchase->decrement('class_remaining');

            return redirect()->back()
                ->with('success', 'User successfully joined the class!');
        });
    }

    public function checkClassEligibility(Request $request)
    {
        $class = ClassSchedule::find($request->class_id);
        $userId = $request->user_id;

        if (!$class) {
            return response()->json(['status' => false, 'message' => 'Class not found.'], 404);
        }

        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'User ID is required.'], 400);
        }

        $existingBooking = Booking::where('registered_id', $userId)
            ->where('selected_class_id', $class->id)
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if ($existingBooking) {
            $statusMsg = $existingBooking->status === 'confirmed' ? 'already joined' : 'already on the waitlist for';
            return redirect()->back()->with('warning', "User has {$statusMsg} this class.");
        }

        $activePurchase = Purchase::whereHas('package', function ($query) use ($class) {
            $query->where('type', $class->category_id);
        })
            ->where('registered_id', $userId)
            ->where('pay_status', 'confirmed')
            ->where('class_remaining', '>', 0)
            ->where('expires_at', '>=', now())
            ->where(function ($query) {
                $query->whereRaw('class_remaining = (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)')
                    ->where('fix_expires_at', '>=', now())
                    ->orWhereRaw('class_remaining < (SELECT class_count FROM packages WHERE id = purchases.selected_packages_id)');
            })
            ->first();

        if (!$activePurchase) {
            return response()->json([
                'status' => false,
                'message' => 'User does not have an active package matching this class category.'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User is eligible for this class.',
            'class' => [
                'id' => $class->id,
                'name' => $class->class_name ?? $class->name,
                'category' => $class->category->name ?? 'N/A',
                'capacity' => $class->capacity ?? 0
            ]
        ]);
    }
}