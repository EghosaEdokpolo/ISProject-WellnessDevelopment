<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __invoke(Request $request, Event $event)
    {
        $token = EventQrToken::create([
            'event_id'   => $event->id,
            'token'      => Str::random(64),
            'expires_at' => $event->ends_at ?? $event->starts_at->copy()->addDay(),
        ]);

        $url = url("/api/v1/events/{$event->id}/check-in?token={$token->token}");

                $svg = QrCode::format('svg')->size(400)->generate($url);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }
}