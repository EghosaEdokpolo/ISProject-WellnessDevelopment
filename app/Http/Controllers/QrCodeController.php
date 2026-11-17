<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Generate an SVG QR code for a specific event's venue check-in.
     */
    public function __invoke(Event $event): Response
    {
        // Points directly to the public Staff ID verification page
        $url = route('checkin.scan', $event);

        // Generate high-res SVG for printing on posters
        $svg = QrCode::format('svg')
            ->size(600)
            ->margin(2)
            ->errorCorrection('H') // High error correction for printed posters
            ->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}