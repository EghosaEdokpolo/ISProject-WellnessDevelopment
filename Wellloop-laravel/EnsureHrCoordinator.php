<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// POINTER: Middleware runs BEFORE a controller method executes. Think of it
// as a checkpoint. We attach this to any route group that only the HR
// Coordinator (Overview/Impact/Retention/Departmental/Attendees/Settings)
// should see — if a staff member tries to visit those URLs directly, they
// get bounced with a 403 instead of the controller ever running.
class EnsureHrCoordinator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'HR Coordinator access only.');

        return $next($request);
    }
}
