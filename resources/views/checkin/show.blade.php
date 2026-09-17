@extends('layouts.app')

@section('content')
    <div class="bg-wl-lavender rounded-lg px-5 py-4 mb-6 max-w-xl">
        <span class="text-xs font-semibold text-wl-indigo uppercase">{{ $registration->event->category }}</span>
        <h2 class="font-bold text-gray-900">{{ $registration->event->name }}</h2>
        <p class="text-xs text-gray-500">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-xl text-center">
        <h3 class="font-bold text-lg text-gray-900">You are registered!</h3>
        <p class="text-sm text-gray-500 mt-1 mb-5">
            Present this QR code at the event entrance. It refreshes every 15 minutes to prevent gaming.
        </p>

        <div class="flex justify-center mb-4">
            {{--
                POINTER: $qrUrl is the temporary signed URL built in
                CheckInController@show. Here we hand that URL to the
                simplesoftwareio/simple-qrcode package (composer require
                simplesoftwareio/simple-qrcode) to turn it into an actual
                scannable image. When someone scans it, their phone just
                opens $qrUrl — hitting checkin.confirm — which Laravel then
                validates against its signature and expiry automatically.
            --}}
            {!! QrCode::size(200)->generate($qrUrl) !!}
        </div>

        <p class="font-semibold text-gray-900">{{ $registration->user->name }}</p>
        <p class="text-xs text-gray-500 mb-4">{{ $registration->event->name }}</p>

        <div class="bg-red-50 text-red-600 text-xs rounded-lg px-3 py-2 mb-4">
            ⏱ Code refreshes in 15:00 — screenshot won't work after expiry
        </div>

        <p class="text-xs text-gray-500 mb-4">
            No smartphone? Enter <strong>{{ $registration->user->employee_id }}</strong> manually at the check-in desk.
        </p>

        {{-- Manual fallback form — this is what the person AT THE DESK uses --}}
        <form method="POST" action="{{ route('checkin.manual') }}" class="flex gap-2 justify-center">
            @csrf
            <input name="employee_id" placeholder="EMP-104" class="border-gray-200 rounded-lg bg-gray-50 text-sm px-3 py-1.5">
            <button class="bg-wl-navy text-white text-sm font-medium px-4 py-1.5 rounded-lg">Check in</button>
        </form>
    </div>
@endsection
