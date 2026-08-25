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

            $schedules = ClassSchedule::all();

            $filteredSchedules = $schedules->filter(function ($schedule) use ($instructorId) {
                $ids = is_string($schedule->instructor_ids) ? json_decode($schedule->instructor_ids, true) : ($schedule->instructor_ids ?? []);
                return in_array($instructorId, (array)$ids);
            });

            $schedules = $filteredSchedules;
        } else {
            $schedules = ClassSchedule::latest()->get();
        }

        $instructors = Instructor::with(['user', 'categoryFees'])->get();
        $categories = Category::all();

        return view('backends.class_schedules.class_schedules_index', compact('schedules', 'instructors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
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

        $data = $request->except(['category_id']);
        $data['category_ids'] = json_encode($request->category_ids);

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
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
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

        $data = $request->except(['category_id']);
        $data['category_ids'] = json_encode($request->category_ids);

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

        $classSchedule->update(['status' => 'cancelled']);
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

    public function class_schedules_list(Request $request)
    {
        $now = Carbon::now();
        $defaultStartDate = $now->copy()->startOfWeek()->format('Y-m-d');
        $defaultEndDate = $now->copy()->endOfWeek()->format('Y-m-d');

        $fromDate = $request->input('from_date', $defaultStartDate);
        $toDate = $request->input('to_date', $defaultEndDate);

        $schedules = ClassSchedule::with(['bookings.user'])
            ->whereDate('start_date', '>=', $fromDate)
            ->whereDate('start_date', '<=', $toDate)
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        $scheduleIds = $schedules->pluck('id')->toArray();
        $attendances = Attendance::with(['instructor.user'])
            ->whereIn('class_id', $scheduleIds)
            ->get();

        $clientIds = $attendances->whereNotNull('client_id')->pluck('client_id')->unique();
        $clients = User::whereIn('id', $clientIds)->get()->keyBy('id');

        foreach ($schedules as $schedule) {
            $instIds = is_string($schedule->instructor_ids) ? json_decode($schedule->instructor_ids, true) : ($schedule->instructor_ids ?? []);
            $schedule->instructors = Instructor::with('user')->whereIn('id', (array)$instIds)->get();

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
        $classes = ClassSchedule::latest()->get();

        return view('backends.class_schedules.join_class', compact('user', 'classes', 'bookings', 'bookingsAll'));
    }

    public function joinClassForUserSubmit(Request $request)
    {
        $classId = $request->class_id;
        $userId = $request->input('user_id', auth()->id());
        $bookingDatesStr = $request->input('booking_dates');

        if (!$classId) {
            return redirect()->back()->with('warning', 'Invalid class selected.');
        }

        if (!$bookingDatesStr) {
            return redirect()->back()->with('warning', 'Please select at least one booking date.');
        }

        $class = ClassSchedule::find($classId);
        if (!$class) {
            return redirect()->back()->with('warning', 'Class not found.');
        }

        $bookingDates = explode(', ', $bookingDatesStr);
        $bookingDates = array_map('trim', $bookingDates);
        $totalRequestedClasses = count($bookingDates);

        $categoryIds = is_string($class->category_ids) ? json_decode($class->category_ids, true) : ($class->category_ids ?? []);
        $categoryIds = is_array($categoryIds) ? $categoryIds : [];

        $categoryMatchExists = Purchase::whereHas('package', function ($query) use ($categoryIds) {
            $query->whereIn('type', $categoryIds);
        })->where('registered_id', $userId)->exists();

        if (!$categoryMatchExists) {
            return redirect()->back()->with('warning', 'The user does not have a package matching this class categories.');
        }

        $activePurchase = Purchase::whereHas('package', function ($query) use ($categoryIds) {
            $query->whereIn('type', $categoryIds);
        })
            ->where('registered_id', $userId)
            ->where('pay_status', 'confirmed')
            ->where('class_remaining', '>=', $totalRequestedClasses)
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
                ->with('warning', "No active package found, or not enough credits for {$totalRequestedClasses} selected classes.");
        }

        $classCapacity = $class->capacity ?? 0;
        $waitlistedCount = 0;
        $confirmedCount = 0;

        return DB::transaction(function () use ($class, $activePurchase, $userId, $bookingDates, $classCapacity, &$waitlistedCount, &$confirmedCount) {

            foreach ($bookingDates as $date) {
                $existingBooking = Booking::where('registered_id', $userId)
                    ->where('selected_class_id', $class->id)
                    ->where('booked_date', $date)
                    ->whereIn('status', ['confirmed', 'waitlisted'])
                    ->first();

                if ($existingBooking) {
                    continue;
                }

                $bookedCount = Booking::where('selected_class_id', $class->id)
                    ->where('booked_date', $date)
                    ->where('status', 'confirmed')
                    ->count();

                $booking = new Booking();
                $booking->package_id = $activePurchase->selected_packages_id;
                $booking->registered_id = $userId;
                $booking->selected_class_id = $class->id;
                $booking->booked_date = $date;

                if ($bookedCount >= $classCapacity) {
                    $booking->status = 'waitlisted';
                    $booking->save();
                    $waitlistedCount++;
                } else {
                    $booking->status = 'confirmed';
                    $booking->save();
                    $activePurchase->decrement('class_remaining');
                    $confirmedCount++;
                }
            }

            if ($waitlistedCount > 0 && $confirmedCount == 0) {
                return redirect()->back()->with('warning', 'Classes were full on selected dates. Added to waitlist.');
            }

            return redirect()->back()
                ->with('success', "Successfully joined {$confirmedCount} class(es). " . ($waitlistedCount > 0 ? "Waitlisted for {$waitlistedCount}." : ""));
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

        $categoryIds = is_string($class->category_ids) ? json_decode($class->category_ids, true) : ($class->category_ids ?? []);
        $categoryIds = is_array($categoryIds) ? $categoryIds : [];

        $activePurchase = Purchase::whereHas('package', function ($query) use ($categoryIds) {
            $query->whereIn('type', $categoryIds);
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
                'name' => $class->class_name,
                'capacity' => $class->capacity ?? 0
            ]
        ]);
    }

    public function frontendClasses(Request $request)
    {
        $query = ClassSchedule::with(['instructor.user'])
            ->where(function ($q) {
                $q->whereDate('end_date', '>=', Carbon::today('Asia/Yangon'))
                    ->orWhere(function ($subQ) {
                        $subQ->whereNull('end_date')
                            ->whereDate('start_date', '>=', Carbon::today('Asia/Yangon'));
                    });
        })
            ->where('status', '!=', 'cancelled');

        if ($request->filled('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_ids', 'like', '%"' . $request->category . '"%');
        }

        if ($request->filled('instructor')) {
            $query->where('instructor_ids', 'like', '%"' . $request->instructor . '"%');
        }

        if ($request->filled('from_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('end_date', '>=', $request->from_date)
                    ->orWhere(function ($subQ) use ($request) {
                        $subQ->whereNull('end_date')
                            ->whereDate('start_date', '>=', $request->from_date);
                    });
            });
        }

        if ($request->filled('to_date')) {
            $query->whereDate('start_date', '<=', $request->to_date);
        }

        $classes = $query->latest()->paginate(10);

        foreach ($classes as $class) {
            $instIds = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : ($class->instructor_ids ?? []);
            $class->instructor = Instructor::with('user')->whereIn('id', (array)$instIds)->get()->toArray();
        }

        $categories = Category::all();
        $instructors = Instructor::with('user')->get();
        $bookingsAll = Booking::all();

        $bookings = collect();
        if (Auth::check()) {
            $bookings = Booking::where('registered_id', auth()->id())->get();
        }

        $allImages = [];

        return view('frontend.classes.index', compact(
            'classes',
            'categories',
            'instructors',
            'bookingsAll',
            'bookings',
            'allImages'
        ));
    }
}
