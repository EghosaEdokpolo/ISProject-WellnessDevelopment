<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Keep HR Coordinators strictly inside the HR Portal settings page
        if ($user->isHrCoordinator()) {
            return redirect()->route('settings.edit');
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isHrCoordinator()) {
            return redirect()->route('settings.edit');
        }

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        return back()->with('success', 'Your staff profile details have been updated.');
    }
}