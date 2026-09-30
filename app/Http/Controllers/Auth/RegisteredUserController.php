<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the incoming form inputs
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // CHANGED: Swapped email validation for a required, numeric, unique staff_id validation
            'staff_id' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Safely write the user account row into your MySQL table container
        $user = User::create([
            'name' => $request->name,
            // CHANGED: Map the form field 'staff_id' directly to your user model database column
            'staff_id' => $request->staff_id,
            // Bcrypt hashing driver automatically fires here to securely salt the string
            'password' => Hash::make($request->password),
        ]);

        // 3. Dispatch global system account creation event metrics
        event(new Registered($user));

        // 4. Authenticate the newly generated user session record
        Auth::login($user);

        // 5. Securely route them directly through the dashboard middleware gate
        return redirect(route('dashboard', absolute: false));
    }
}
