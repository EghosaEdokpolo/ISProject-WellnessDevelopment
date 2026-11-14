<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __invoke(Request $request, Event $event)
    {
        // This URL is what gets embedded in the printed QR Code.
        // When scanned, the staff's phone will open this exact page.
        $url = url("/checkin/event/{$event->id}");

        // Generate high-res SVG for printing
        $svg = QrCode::format('svg')->size(500)->margin(2)->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}