<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.loginpage');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();$request->session()->regenerate();

        $user = Auth::user();

        if ($user->isHrCoordinator()) {
            return redirect()->route('dashboard.hr');
        }

        if ($user->isStaff()) {
            // If staff scanned a QR poster while logged out, resume the QR check-in URL immediately
            $intended = session()->pull('url.intended');
            if ($intended && str_contains($intended, '/qr-scan')) {
                return redirect()->to($intended);
            }

            return redirect()->route('events.index');
        }

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();$request->session()->regenerateToken();

        return redirect('/');
    }
}