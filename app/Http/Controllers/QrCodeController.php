<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __invoke(Request $request, Event $event)
    {
        // This generates a URL like: https://yourdomain.com/checkin/event/1
        // When staff scan this, it opens on their phone.
        $url = url("/checkin/event/{$event->id}");

        // Generate the SVG (high quality for printing)
        $svg = QrCode::format('svg')->size(500)->margin(2)->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}