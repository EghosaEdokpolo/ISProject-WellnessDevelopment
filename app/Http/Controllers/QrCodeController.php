<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __invoke(Request $request, Event $event)
    {
        // Points directly to the public venue arrival Staff ID verification page
        $url = route('checkin.scan', $event);

        // Generate the SVG (high quality for printing)
        $svg = QrCode::format('svg')->size(500)->margin(2)->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
