<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /** Display the login view. */
    public function create(): View
    {
        return view('auth.loginpage');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Executes the email/staff_id password matching authentication logic
        $request->authenticate();

        // Regenerates the session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // CODE ADDITION: Fetch the logged-in user to check their database role string
        $user = Auth::user();

        // If an HR admin logs in, route them immediately to the HR Overview page route
        if ($user->role === 'hr_coordinator') {
            return redirect()->intended(route('dashboard.hr'));

        }

        // If standard Staff logs in, route them to their personal page layout
        if ($user->role === 'staff') {
            return redirect()->intended(route('events.index'));
        }

         // Fallback default catch route if no user role explicitly matches
        return redirect()->intended(route('dashboard', absolute: false));
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
