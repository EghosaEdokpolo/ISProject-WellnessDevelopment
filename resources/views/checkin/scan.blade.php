@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-6 py-10">

    <div class="mb-4">
        <a href="{{ route('checkin.index') }}" class="text-xs font-bold text-slate-500 hover:text-brand inline-flex items-center gap-1">
            ← Back to Check-In Hub
        </a>
    </div>

    @if(session('confirmed'))
        @php $confirmed = session('confirmed'); @endphp
        <div class="bg-white rounded-3xl border border-emerald-200 shadow-xl p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">
                {{ $confirmed['already_checked_in'] ? 'Already Checked In' : 'Check-In Successful!' }}
            </h1>
            <p class="text-slate-500 mb-6">Welcome, <strong class="text-slate-800">{{ $confirmed['name'] }}</strong></p>

            @if(!$confirmed['already_checked_in'])
                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 mb-6">
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Points Awarded</p>
                    <p class="text-3xl font-extrabold text-emerald-600">+{{ $confirmed['points'] }} Points</p>
                </div>
            @endif

            <p class="text-sm text-slate-500 mb-6">You are verified for <strong>{{ $confirmed['event'] }}</strong>.</p>

            <div class="space-y-3">
                @if(isset($myRegistration) && $myRegistration && $myRegistration->status !== 'completed')
                    <a href="{{ route('feedback.create', $myRegistration) }}" class="block w-full bg-wl-gold text-brand font-bold py-3.5 rounded-xl hover:bg-yellow-500 transition text-sm shadow-sm">
                        Complete Post-Event WHO-5 Feedback (+{{ $event->points_feedback ?? 10 }} pts)
                    </a>
                @endif

                <a href="{{ route('checkin.scan', $event) }}" class="block w-full bg-brand text-white font-bold py-3.5 rounded-xl hover:bg-blue-900 transition text-sm">
                    Check In Next Staff Member
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-brand"></div>

            <div class="w-16 h-16 bg-brand-light rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-1">Venue Check-In</h1>
            <p class="text-slate-600 text-sm mb-2 font-semibold">{{ $event->name }}</p>
            <p class="text-xs font-bold text-brand bg-brand-light inline-block px-3 py-1 rounded-full mb-6">
                {{ $checkedInCount }} / {{ $registeredCount }} Checked In
            </p>

            @if($errors->any())
                <div class="mb-6 bg-rose-50 text-rose-600 px-4 py-3 rounded-xl text-sm font-bold border border-rose-200 text-left">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- One-Tap Self Check-In for Logged-In Staff Scanning on Mobile -->
            @if(isset($myRegistration) && $myRegistration)
                <div class="mb-6 p-4 rounded-2xl bg-brand-light/70 border border-brand/20 text-left">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-brand mb-1">Logged in as {{ auth()->user()->name }}</p>
                    @if($myRegistration->checkIn)
                        <p class="text-xs text-emerald-700 font-bold mb-3">You are already checked in for this session.</p>
                        @if($myRegistration->status !== 'completed')
                            <a href="{{ route('feedback.create', $myRegistration) }}" class="block w-full text-center bg-emerald-600 text-white font-bold py-3 rounded-xl text-xs hover:bg-emerald-700 transition">
                                Submit Post-Event Feedback (+{{ $event->points_feedback ?? 10 }} pts)
                            </a>
                        @endif
                    @elseif(!$myRegistration->waiver)
                        <p class="text-xs text-amber-800 mb-3">Sign your event waiver to unlock instant check-in.</p>
                        <a href="{{ route('registrations.waiver', $myRegistration) }}" class="block w-full text-center bg-amber-500 text-white font-bold py-3 rounded-xl text-xs hover:bg-amber-600 transition">
                            Sign Mandatory Waiver First
                        </a>
                    @else
                        <p class="text-xs text-slate-600 mb-3">Tap below to confirm your arrival and earn <strong>+{{ $event->points_checkin ?? 20 }} pts</strong>.</p>
                        <form method="POST" action="{{ route('checkin.confirm', $myRegistration) }}">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-xl text-sm hover:bg-emerald-700 transition shadow-sm">
                                One-Tap Self Check-In
                            </button>
                        </form>
                    @endif
                </div>

                <div class="relative flex py-2 items-center mb-5">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-3 text-[11px] font-bold uppercase text-slate-400">Or Kiosk Staff ID</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>
            @elseif(auth()->check() && !auth()->user()->isHrCoordinator())
                <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left">
                    <p class="text-xs text-slate-600 mb-2">Not registered for this session yet?</p>
                    <a href="{{ route('registrations.create', $event) }}" class="inline-block text-xs font-bold text-brand hover:underline">
                        Register & Sign Waiver Now →
                    </a>
                </div>
            @endif

            <!-- Venue Kiosk / Manual Staff ID Entry -->
            <form method="POST" action="{{ route('checkin.manual') }}" class="text-left">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Enter Staff ID</label>
                    <input type="text" name="staff_id" required placeholder="e.g., SU1234" value="{{ old('staff_id') }}"
                           class="w-full border border-slate-300 rounded-xl text-lg p-3.5 bg-slate-50 focus:ring-brand focus:border-brand font-bold text-center tracking-widest">
                </div>
                <button type="submit" class="w-full bg-brand text-white font-bold py-3.5 rounded-xl shadow-md hover:bg-blue-900 transition text-base flex items-center justify-center gap-2">
                    Confirm Attendance
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    @endif

</div>
@endsection