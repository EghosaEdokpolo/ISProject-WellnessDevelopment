@auth
@if(auth()->user()->isStaff())
@php
    $tz                 = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $opensBeforeMinutes = (int) config('wellloop.checkin.opens_minutes_before', 60);
    $lateGraceMinutes   = (int) config('wellloop.checkin.late_grace_minutes', 10);
    $now                = now($tz);

    $userRegs = \App\Models\Registration::with(['event', 'checkIn', 'feedback'])
        ->where('user_id', auth()->id())
        ->get()
        ->filter(fn ($r) => $r->event !== null);

    $reminders = collect();

    foreach ($userRegs as $reg) {
        $ev         = $reg->event;
        $eventStart = $ev->starts_at->copy()->shiftTimezone($tz);
        $gateOpens  = $eventStart->copy()->subMinutes($opensBeforeMinutes);
        $gateCloses = $eventStart->copy()->addMinutes($lateGraceMinutes);

        if ($reg->checkIn === null && $now->lte($gateCloses)) {
            $isLateGrace = $now->gt($eventStart);
            $isGateOpen  = $now->between($gateOpens, $gateCloses);

            if ($isLateGrace) {
                $minsLeft = max(1, (int) $now->diffInMinutes($gateCloses));
                $reminders->push([
                    'priority'  => 1,
                    'type'      => 'late_grace',
                    'badge'     => 'Late Grace Active',
                    'text'      => "Late Grace Window: {$ev->name} started — {$minsLeft} min(s) left before gate closes at {$gateCloses->format('H:i')} EAT.",
                    'cta'       => 'Scan QR Now →',
                    'url'       => route('registrations.ticket', $reg),
                    'starts_at' => $eventStart->timestamp,
                ]);
            } elseif ($isGateOpen) {
                $minsToStart = max(0, (int) $now->diffInMinutes($eventStart));
                $reminders->push([
                    'priority'  => 2,
                    'type'      => 'gate_open',
                    'badge'     => 'Gate Open',
                    'text'      => "Check-In Open: {$ev->name} starts in {$minsToStart} min(s) ({$eventStart->format('D, d M \a\t H:i')} at {$ev->location}).",
                    'cta'       => 'View Check-In Details →',
                    'url'       => route('registrations.ticket', $reg),
                    'starts_at' => $eventStart->timestamp,
                ]);
            } else {
                $timeUntil = $now->diffForHumans($eventStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
                $reminders->push([
                    'priority'  => 3,
                    'type'      => 'upcoming',
                    'badge'     => 'Upcoming Session',
                    'text'      => "Upcoming Event Reminder: {$ev->name} starts in {$timeUntil} ({$eventStart->format('D, d M \a\t H:i')} at {$ev->location}).",
                    'cta'       => 'View Check-In Details →',
                    'url'       => route('registrations.ticket', $reg),
                    'starts_at' => $eventStart->timestamp,
                ]);
            }
        }

        if ($reg->checkIn !== null && $reg->status !== 'completed' && $reg->feedback === null && $now->gte($eventStart)) {
            $fbPts = (int) ($ev->points_feedback ?? 10);
            $reminders->push([
                'priority'  => 2,
                'type'      => 'post_who',
                'badge'     => 'Post-WHO Pending',
                'text'      => "Post-Event WHO-5 Reminder: Complete your reflection & WHO-5 check-in for {$ev->name} ({$eventStart->format('d M Y')}) to earn +{$fbPts} pts.",
                'cta'       => 'Complete Post-WHO Feedback →',
                'url'       => route('feedback.create', $reg),
                'starts_at' => $eventStart->timestamp,
            ]);
        }
    }

    $sortedReminders = $reminders
        ->sortBy([
            ['priority', 'asc'],
            ['starts_at', 'asc'],
        ])
        ->values();

    $primaryReminder = $sortedReminders->first();
    $extraReminders  = $sortedReminders->slice(1)->values();
@endphp

@if($primaryReminder)
    <div class="bg-amber-50/90 border-b border-amber-200 text-amber-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="font-semibold truncate">{{ $primaryReminder['text'] }}</span>
                    <a href="{{ $primaryReminder['url'] }}" class="font-extrabold text-[#02338d] underline hover:text-[#01225e] shrink-0">
                        {{ $primaryReminder['cta'] }}
                    </a>
                </div>

                @if($extraReminders->isNotEmpty())
                    <button type="button"
                            onclick="document.getElementById('extra-reminders-drawer').classList.toggle('hidden')"
                            class="text-[11px] font-extrabold bg-amber-200/80 hover:bg-amber-300 text-amber-950 px-3 py-1 rounded-full transition shrink-0">
                        +{{ $extraReminders->count() }} more {{ $extraReminders->count() === 1 ? 'reminder' : 'reminders' }} ▾
                    </button>
                @endif
            </div>

            @if($extraReminders->isNotEmpty())
                <div id="extra-reminders-drawer" class="hidden mt-2.5 pt-2.5 border-t border-amber-200/80 space-y-2">
                    @foreach($extraReminders as $item)
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs bg-white/70 rounded-xl px-3.5 py-2 border border-amber-200/60">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full {{ $item['type'] === 'post_who' ? 'bg-blue-100 text-[#02338d]' : 'bg-amber-100 text-amber-900' }}">
                                    {{ $item['badge'] }}
                                </span>
                                <span class="font-semibold text-slate-800">{{ $item['text'] }}</span>
                            </div>
                            <a href="{{ $item['url'] }}" class="font-extrabold text-[#02338d] hover:underline shrink-0">
                                {{ $item['cta'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
@endif
@endauth