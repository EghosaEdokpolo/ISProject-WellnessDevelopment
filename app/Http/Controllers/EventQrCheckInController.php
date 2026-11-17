<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EventQrCheckInController extends Controller
{
    /**
     * HR View: Printable A4 QR Poster for an event entrance.
     */
    public function poster(Event $event): View
    {
        // Cryptographically signed URL prevents staff from forging check-in URLs without scanning
        $scanUrl = URL::signedRoute('events.qr.scan', ['event' =>$event->id]);

        return view('events.qr_poster', compact('event', 'scanUrl'));
    }

    /**
     * Staff View: Triggered automatically when scanning the HR-printed QR poster.
     * Registers walk-in staff automatically and marks attendance as checked_in.
     */
    public function scan(Request $request, Event$event): View
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or tampered event QR code.');

        $user =$request->user();

        $result = DB::transaction(function () use ($user, $event): array {$registration = Registration::with(['waiver', 'wellbeingResponses'])
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

            $autoRegistered = false;
            $alreadyCheckedIn = false;

            if (! $registration) {
                // Walk-in staff member: automatically register and check them in
                $registration = Registration::create([
                    'user_id'       => $user->id,
                    'event_id'      => $event->id,
                    'status'        => 'checked_in',
                    'checked_in_at' => now(),
                    'consent_given' => true,
                ]);
                $autoRegistered = true;
            } elseif (in_array($registration->status, ['checked_in', 'completed'], true)) {$alreadyCheckedIn = true;
            } else {
                // Pre-registered staff member: mark as checked in
                $registration->update([
                    'status'        => 'checked_in',
                    'checked_in_at' => $registration->checked_in_at ?? now(),
                ]);
            }

            $pointsEarned = (int) ($event->points_checkin ?? 20);
            $pointsAwarded =$this->awardPointsOnce($registration, 'checkin_completed',$pointsEarned);

            return [
                'registration'     => $registration->fresh(['event', 'waiver', 'wellbeingResponses']),
                'autoRegistered'   => $autoRegistered,
                'alreadyCheckedIn' => $alreadyCheckedIn,
                'pointsEarned'     => $pointsAwarded ? $pointsEarned : 0,
            ];
        });

        Cache::forget('hr_overview_data_v21');

        $registration = $result['registration'];$hasPreWho5 = $registration->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isNotEmpty();$needsOnboarding = ! $registration->waiver \vert{}\vert{} !$hasPreWho5;

        return view('events.qr_scan_result', [
            'event'            => $event,
            'registration'     => $registration,
            'autoRegistered'   => $result['autoRegistered'],
            'alreadyCheckedIn' => $result['alreadyCheckedIn'],
            'pointsEarned'     => $result['pointsEarned'],
            'needsOnboarding'  => $needsOnboarding,
        ]);
    }

    private function awardPointsOnce(Registration $registration, string $reason, int$points): bool
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason',$reason)->exists()) {
            return false;
        }

        PointsTransaction::create([
            'user_id'         => $registration->user_id,
            'registration_id' => $registration->id,
            'reason'          => $reason,
            'points'          => $points,
        ]);

        $registration->user->increment('points_balance',$points);

        return true;
    }
}