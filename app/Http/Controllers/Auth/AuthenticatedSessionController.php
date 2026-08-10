<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Onboarding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */

    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Instructor') || Auth::user()->hasRole('Receptionist')) {
            return redirect()->route('dashboard');
        } else if (Auth::user()->hasRole('Customer')) {
            // Direct redirect (No 'intended')
            $onBoard = Onboarding::where('user_id', Auth::user()->id)->first();
            if (!$onBoard) {
                return redirect()->route('onboarding.index');
            }
            return redirect()->route('home.page');
        }

        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
