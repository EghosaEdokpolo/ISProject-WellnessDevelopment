<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user'        => $request->user(),
            'departments' => $this->allowedDepartments(),
        ]);
    }

    /**
     * Name, Email, and Staff ID are immutable HR master fields and are strictly ignored if tampered with.
     * Staff may update their School/Department (dropdown), Residence, Phone, and Profile Photo.
     */
    public function update(Request $request): RedirectResponse
    {
        $user        = $request->user();
        $departments = $this->allowedDepartments();

        $validated = $request->validate([
            'department'    => ['required', 'string', Rule::in($departments)],
            'residence'     => ['nullable', 'string', 'max:180'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'department.in'       => 'Please select a valid Strathmore University school or department from the list.',
            'profile_photo.image' => 'Profile photo must be an image file (JPG, PNG, or WEBP).',
            'profile_photo.max'   => 'Profile photo must not exceed 2 MB.',
        ]);

        // Direct property assignment keeps name, email, and staff_id 100% locked
        $user->department = $validated['department'];

        if (Schema::hasColumn('users', 'residence')) {
            $user->residence = isset($validated['residence']) ? trim($validated['residence']) : null;
        }

        if (Schema::hasColumn('users', 'phone')) {
            $user->phone = isset($validated['phone']) ? trim($validated['phone']) : null;
        }

        if ($request->hasFile('profile_photo') && Schema::hasColumn('users', 'profile_photo_path')) {
            $dir = public_path('uploads/profiles');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Clean up old uploaded photo if present
            if (! empty($user->profile_photo_path)) {
                $oldFull = public_path($user->profile_photo_path);
                if (File::exists($oldFull)) {
                    File::delete($oldFull);
                }
            }

            $file     = $request->file('profile_photo');
            $filename = 'staff_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);

            $user->profile_photo_path = 'uploads/profiles/' . $filename;
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your profile details have been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Official list of Strathmore University Schools & Departments merged with existing DB values.
     *
     * @return array<int, string>
     */
    private function allowedDepartments(): array
    {
        $defaults = [
            'Cafeteria Services',
            'Finance and Accounting',
            'Housekeeping & Maintenance',
            'Human Resources',
            'School of Computing and Engineering Sciences',
            'School of Humanities and Social Sciences',
            'School of Law',
            'School of Tourism and Hospitality',
            'Strathmore Business School',
            'Strathmore Institute of Mathematical Sciences',
            'Strathmore University Library',
            'University Health Services',
        ];

        $fromDb = User::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department')
            ->all();

        $merged = array_values(array_unique(array_merge($defaults, $fromDb)));
        sort($merged);

        return $merged;
    }
}