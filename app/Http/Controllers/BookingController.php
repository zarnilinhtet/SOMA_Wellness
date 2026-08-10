<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Instructor;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function getBookings(Request $request)
    {
        $query = Booking::orderBy("created_at", "desc")->with(['bookingUser', 'class']);

        // Instructor vs Admin base filtering
        if (auth()->user()->hasRole("Instructor")) {
            $instructorId = Instructor::where('instructor_id', auth()->id())->value('id');
            $query->whereHas('class', function ($q) use ($instructorId) {
                $q->whereJsonContains('instructor_ids', (string) $instructorId);
            });
        }

        // Default behavior: hide waitlisted items unless specifically searched for
        if (!$request->filled('status')) {
            $query->whereNotIn('status', ['waitlisted']);
        } else {
            $query->where('status', $request->status);
        }

        // Apply Date Filters (Default to Today if no date is selected)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate);

        $bookings = $query->get();

        return view('backends.bookings.bookings_index', compact('bookings'));
    }

    public function updateStatus($id, Request $request)
    {
        $book = Booking::findOrFail($id);
        $book->update([
            'status' => $request->status,
        ]);

        $data = [
            'title' => 'Approved WaitListed cancel Status!',
            'message' => 'Updated status of your class.',
            'url' => 'history.page'
        ];

        $user = User::where('id', $book->registered_id)->first();
        if ($user) {
            $user->notify(new AdminNotification($data));
        }

        return redirect()->back()->with('success', "Booking has been explicitly verified as Approved.");
    }

    public function cancelBooking($id)
    {
        $book = Booking::findOrFail($id);
        $book->update([
            'status' => 'cancelled',
            'byWho' => 'Admin',
        ]);

        $data = [
            'title' => 'Class cancel Status!',
            'message' => 'Updated status of your class.',
            'url' => 'history.page'
        ];

        $user = User::where('id', $book->registered_id)->first();
        if ($user) {
            $user->notify(new AdminNotification($data));
        }

        return redirect()->back()->with('success', "Booking has been explicitly verified as cancelled.");
    }

    public function getWaitList(Request $request)
    {
        $query = Booking::orderBy("created_at", "desc")->with(['bookingUser', 'class']);

        // Default to showing only waitlisted if no status is selected
        if (!$request->filled('status')) {
            $query->where('status', 'waitlisted');
        } else {
            $query->where('status', $request->status);
        }

        // Apply Date Filters (Default to Today if no date is selected)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate);

        $bookings = $query->get();
        return view('backends.waitlist.waitlist', compact('bookings'));
    }
    
    public function adminBookClass(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'selected_class_id' => 'required|exists:class_schedules,id',
        ]);

        // Check existing booking
        $existingBooking = Booking::where('registered_id', $request->user_id)
            ->where('selected_class_id', $request->selected_class_id)
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'This user is already booked for the selected class!');
        }

        // Booking အသစ် ဖန်တီးခြင်း
        Booking::create([
            'registered_id' => $request->user_id,
            'selected_class_id' => $request->selected_class_id,
            'package_id' => null, 
            'status' => 'confirmed',
            'byWho' => 'Admin'
        ]);

        return redirect()->back()->with('success', 'Class has been successfully booked for the user.');
    }
}