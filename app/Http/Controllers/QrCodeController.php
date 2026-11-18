<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    /**
     * Keep QR code generation on the unified Event Management page instead of opening a separate webpage.
     */
    public function __invoke(Request $request, Event $event): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'Unauthorized access to event QR codes.');

        return redirect()->route('events.management', ['qr_event' => $event->id]);
    }
}