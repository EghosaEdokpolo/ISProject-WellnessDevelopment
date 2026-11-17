@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-6 py-12">

    <div class="mb-4">
        <a href="{{ route('checkin.index') }}" class="text-xs font-bold text-slate-500 hover:text-brand inline-flex items-center gap-1">
            ← Back to Check-In Hub
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="h-2 bg-brand"></div>
        <div class="p-8">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-brand-light text-brand">
                    Registration #{{ $registration->id }}
                </span>
                <span class="text-xs font-bold text-wl-gold">
                    +{{ $event->points_checkin ?? 20 }} Wellness Points
                </span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ $event->name }}</h1>
            <p class="text-xs text-slate-500 mb-6">{{ $event->starts_at->format('l, d M Y \a\t H:i') }} • {{ $event->location }}</p>

            @if($errors->any())
                <div class="mb-6 bg-rose-50 text-rose-600 px-4 py-3 rounded-xl text-sm font-bold border border-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-2.5 mb-6 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Staff Member</span>
                    <span class="font-bold text-slate-900">{{ $attendee->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Staff ID</span>
                    <span class="font-bold text-slate-900">{{ $attendee->staff_id ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waiver Status</span>
                    <span class="font-bold {{ $registration->waiver ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $registration->waiver ? 'Signed' : 'Pending Signature' }}
                    </span>
                </div>
            </div>

            @if($registration->checkIn)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center">
                    <p class="text-base font-bold text-emerald-800">Attendance Verified</p>
                    <p class="text-xs text-emerald-700 mt-1">
                        Checked in on {{ \Illuminate\Support\Carbon::parse($registration->checkIn->checked_in_at)->format('d M Y \a\t H:i') }}
                    </p>
                </div>
            @elseif(!$registration->waiver)
                <a href="{{ route('registrations.waiver', $registration) }}" class="block w-full text-center bg-amber-500 text-white font-bold py-3.5 rounded-xl hover:bg-amber-600 transition text-sm">
                    Sign Mandatory Waiver First
                </a>
            @else
                <form method="POST" action="{{ route('checkin.confirm', $registration) }}">
                    @csrf
                    <button type="submit" class="w-full bg-brand text-white font-bold py-4 rounded-xl hover:bg-blue-900 transition shadow-md text-base">
                        Confirm Attendance Now
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection