<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Waiver;
use App\Models\WellbeingResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(Event $event): View|RedirectResponse
    {
        $existing = Registration::with(['waiver', 'wellbeingResponses'])
            ->where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            $hasPreWho5 = $existing->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isNotEmpty();

            if ($existing->waiver && $hasPreWho5) {
                return $this->renderNextSteps($existing, $event);
            }

            if (! $hasPreWho5) {
                return redirect()->route('registrations.who5', $existing)
                    ->with('warning', 'Warning: Please complete your Pre-Event WHO-5 wellbeing check-in before proceeding.');
            }

            return redirect()->route('registrations.waiver', $existing)
                ->with('warning', 'Warning: Please sign the mandatory participation waiver to complete your booking.');
        }

        $spotsLeft = $event->spots_remaining ?? max(0, (int) $event->capacity - $event->registrations()->count());
        if ($spotsLeft <= 0) {
            return redirect()->route('events.index')->with('error', 'Sorry, this event is fully booked.');
        }

        return view('events.register', compact('event'));
    }

    public function store(Request $request, Event $event): View|RedirectResponse
    {
        $user = $request->user();

        $existing = Registration::with(['waiver', 'wellbeingResponses'])
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing && $existing->waiver && $existing->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isNotEmpty()) {
            return $this->renderNextSteps($existing, $event);
        }

        $spotsLeft = $event->spots_remaining ?? max(0, (int) $event->capacity - $event->registrations()->count());
        if (! $existing && $spotsLeft <= 0) {
            return redirect()->route('events.index')->with('error', 'Sorry, this event is fully booked.');
        }

        $data = $request->validate(
            [
                'item_cheerful'   => ['required', 'integer', 'between:0,5'],
                'item_calm'       => ['required', 'integer', 'between:0,5'],
                'item_active'     => ['required', 'integer', 'between:0,5'],
                'item_rested'     => ['required', 'integer', 'between:0,5'],
                'item_interested' => ['required', 'integer', 'between:0,5'],
                'signature_name'  => ['required', 'string', 'max:255'],
                'agree'           => ['accepted'],
            ],
            [
                'item_cheerful.required'   => 'Warning: Please answer Pre-Event WHO-5 Question 1 (cheerful and in good spirits).',
                'item_calm.required'       => 'Warning: Please answer Pre-Event WHO-5 Question 2 (calm and relaxed).',
                'item_active.required'     => 'Warning: Please answer Pre-Event WHO-5 Question 3 (active and vigorous).',
                'item_rested.required'     => 'Warning: Please answer Pre-Event WHO-5 Question 4 (woke up feeling fresh and rested).',
                'item_interested.required' => 'Warning: Please answer Pre-Event WHO-5 Question 5 (daily life filled with interesting things).',
                'signature_name.required'  => 'Warning: Please enter your full name as your digital signature.',
                'agree.accepted'           => 'Warning: You must agree to the participation waiver before completing registration.',
            ]
        );

        $preScore = array_sum([
            (int) $data['item_cheerful'],
            (int) $data['item_calm'],
            (int) $data['item_active'],
            (int) $data['item_rested'],
            (int) $data['item_interested'],
        ]);

        $registration = DB::transaction(function () use ($user, $event, $existing, $data, $preScore): Registration {
            $reg = $existing ?? Registration::create([
                'user_id'       => $user->id,
                'event_id'      => $event->id,
                'status'        => 'registered',
                'consent_given' => true,
            ]);

            if ($existing) {
                $reg->update(['consent_given' => true]);
            }

            WellbeingResponse::updateOrCreate(
                ['registration_id' => $reg->id, 'stage' => 'pre'],
                [
                    'item_cheerful'   => (int) $data['item_cheerful'],
                    'item_calm'       => (int) $data['item_calm'],
                    'item_active'     => (int) $data['item_active'],
                    'item_rested'     => (int) $data['item_rested'],
                    'item_interested' => (int) $data['item_interested'],
                    'total_score'     => $preScore,
                ]
            );

            Waiver::updateOrCreate(
                ['registration_id' => $reg->id],
                [
                    'signature_name' => $data['signature_name'],
                    'signed_at'      => now(),
                ]
            );

            $this->awardPointsOnce($reg, 'waiver_signed', 5);

            return $reg;
        });

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        $impactPrefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($impactPrefix);
        if ($event->category) {
            Cache::forget($impactPrefix . $event->category);
        }

        return $this->renderNextSteps($registration->fresh(['event', 'waiver', 'wellbeingResponses']), $event);
    }

    public function who5Form(Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        $registration->load('event');

        return view('events.who5_baseline', compact('registration'));
    }

    public function who5Store(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $data = $request->validate(
            [
                'item_cheerful'   => ['required', 'integer', 'between:0,5'],
                'item_calm'       => ['required', 'integer', 'between:0,5'],
                'item_active'     => ['required', 'integer', 'between:0,5'],
                'item_rested'     => ['required', 'integer', 'between:0,5'],
                'item_interested' => ['required', 'integer', 'between:0,5'],
            ],
            [
                '*.required' => 'Warning: All 5 Pre-Event WHO-5 wellbeing items are required before continuing.',
            ]
        );

        $total = array_sum(array_map('intval', $data));

        WellbeingResponse::updateOrCreate(
            ['registration_id' => $registration->id, 'stage' => 'pre'],
            array_merge(array_map('intval', $data), ['total_score' => $total])
        );

        $impactPrefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($impactPrefix);
        if ($registration->event?->category) {
            Cache::forget($impactPrefix . $registration->event->category);
        }

        return redirect()->route('registrations.waiver', $registration);
    }

    public function waiverForm(Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $registration->load(['event', 'wellbeingResponses', 'waiver']);

        if ($registration->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isEmpty()) {
            return redirect()->route('registrations.who5', $registration)
                ->with('warning', 'Warning: Please complete your Pre-Event WHO-5 baseline first.');
        }

        if ($registration->waiver) {
            return $this->renderNextSteps($registration, $registration->event);
        }

        return view('events.waiver', compact('registration'));
    }

    public function waiverStore(Request $request, Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $registration->load(['event', 'waiver']);

        if ($registration->waiver) {
            return $this->renderNextSteps($registration, $registration->event);
        }

        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:255'],
            'agree'          => ['accepted'],
        ]);

        DB::transaction(function () use ($registration, $data): void {
            Waiver::updateOrCreate(
                ['registration_id' => $registration->id],
                ['signature_name' => $data['signature_name'], 'signed_at' => now()]
            );

            $registration->update(['consent_given' => true]);

            $this->awardPointsOnce($registration, 'waiver_signed', 5);
        });

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));

        $freshReg = $registration->fresh(['event', 'waiver']);

        return $this->renderNextSteps($freshReg, $freshReg->event);
    }

    public function consentForm(Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        return redirect()->route('registrations.waiver', $registration);
    }

    public function consentStore(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        $registration->update(['consent_given' => true]);

        return redirect()->route('registrations.waiver', $registration);
    }

    /**
     * Render the Registration Confirmation screen with working Google Calendar & WhatsApp URLs.
     */
    private function renderNextSteps(Registration $registration, Event $event): View
    {
        $tz         = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $eventStart = $event->starts_at ? $event->starts_at->copy()->shiftTimezone($tz) : now($tz);
        $eventEnd   = $eventStart->copy()->addHours(2);

        $gcalParams = http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => 'Strathmore WellLoop: ' . $event->name,
            'dates'    => $eventStart->format('Ymd\THis') . '/' . $eventEnd->format('Ymd\THis'),
            'details'  => ($event->description ?: 'Strathmore University Staff Wellness Session.') . "\nVenue: " . $event->location,
            'location' => $event->location . ', Strathmore University, Nairobi',
            'ctz'      => $tz,
        ], '', '&', PHP_QUERY_RFC3986);

        $googleCalendarUrl = 'https://calendar.google.com/calendar/render?' . $gcalParams;
        $whatsappUrl       = $event->whatsapp_channel_url;

        return view('events.registered_next_steps', [
            'registration'      => $registration,
            'event'             => $event,
            'googleCalendarUrl' => $googleCalendarUrl,
            'calendarUrl'       => $googleCalendarUrl,
            'whatsappUrl'       => $whatsappUrl,
        ]);
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) {
            return;
        }

        PointsTransaction::create([
            'user_id'         => $registration->user_id,
            'registration_id' => $registration->id,
            'reason'          => $reason,
            'points'          => $points,
        ]);

        $registration->user->increment('points_balance', $points);
    }
}