<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Onboarding;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class PageController extends Controller
{
    public function onboarding()
    {
        $packages = Package::paginate(8);
        $categories = Category::all();
        return view("frontend.onboarding", compact('packages', 'categories'));
    }

    public function terms()
    {
        return view("frontend.terms");
    }

    public function editProfile()
    {
        $user = Onboarding::with('user')->where('user_id', auth()->id())->first();
        return view("frontend.editProfile", compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users,phone,' . $user->id,
        ], [
            'phone.unique' => 'This phone is already registered.',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()
            ->route('edit.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success-avatar', 'Your profile picture has been updated beautifully.');
    }

    public function changePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('edit.profile')
                ->withErrors($validator, 'password') // Only affects the 'password' bag
                ->withInput();
        }

        if ($request->current_password === $request->new_password) {
            return redirect()->route('edit.profile')
                ->withErrors(['new_password' => 'New password cannot be the same as the current password.'], 'password')
                ->withInput();
        }

        if (!\Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->route('edit.profile')
                ->withErrors(['current_password' => 'Current password is incorrect.'], 'password')
                ->withInput();
        }

        auth()->user()->update(['password' => \Hash::make($request->new_password)]);

        return redirect()->route('edit.profile')->with('success-password', 'Password changed successfully.');
    }

    public function markAllRead()
    {
        // This is a built-in Laravel method that updates the 'read_at' column
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function markAsRead($id)
    {
        // Find the notification directly via the user's relationship
        $notification = auth()->user()->unreadNotifications()->findOrFail($id);

        // Mark as read
        $notification->markAsRead();

        // Get the route/url from the notification data
        // Assuming your notification data contains an 'url' key
        $targetUrl = $notification->data['url'] ?? 'home';
        // Redirect the user to the relevant page (e.g., your library page)
        return redirect()->route($targetUrl)->with('success', 'Notification marked as read.');
    }
}
