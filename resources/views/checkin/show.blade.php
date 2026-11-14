@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Event Check-in</h1>
        <p class="text-sm text-gray-500 mt-1">UC-06 Check In via QR · UC-05 Sign Consent & Indemnity Waiver</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-lg mx-auto">
        
        @if($registration->checkIn)
            <div class="border-2 border-dashed border-emerald-400 bg-emerald-50 rounded-xl p-10 flex flex-col items-center justify-center mb-8">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-emerald-800 font-semibold text-sm">Gate list matched — welcome!</p>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-wl-indigo" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm text-gray-700">Indemnity waiver signed</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-wl-indigo" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm text-gray-700">Consent form signed</span>
                </div>
            </div>

            <a href="{{ route('feedback.create', $registration) }}" class="block w-full bg-wl-indigo text-white text-center font-semibold py-3 rounded-lg hover:bg-indigo-600 transition-colors shadow-sm">
                Continue to Feedback &rarr;
            </a>

        @else
            <div id="scan-box" class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl p-10 flex flex-col items-center justify-center mb-8 cursor-pointer hover:bg-gray-100 transition-colors">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <p class="text-gray-600 font-semibold text-sm">Tap to simulate QR scan</p>
            </div>

            <div class="space-y-3 mb-8">
                <label class="flex items-center gap-3">
                    <input type="checkbox" id="waiver-check" disabled class="w-4 h-4 text-wl-indigo rounded border-gray-300">
                    <span class="text-sm text-gray-600">Indemnity waiver signed</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" id="consent-check" disabled class="w-4 h-4 text-wl-indigo rounded border-gray-300">
                    <span class="text-sm text-gray-600">Consent form signed</span>
                </label>
            </div>

            <form method="POST" action="{{ route('checkin.confirm', $registration) }}">
                @csrf
                <button type="submit" id="confirm-btn" disabled class="w-full bg-wl-indigo/50 text-white font-semibold py-3 rounded-lg transition-colors cursor-not-allowed">
                    Confirm check-in (+20 pts)
                </button>
            </form>

            <!-- MANUAL FALLBACK UPDATED TO STAFF ID -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500 mb-2">No smartphone? Enter Staff ID manually:</p>
                <form method="POST" action="{{ route('checkin.manual') }}" class="flex gap-2 justify-center">
                    @csrf
                    <input name="staff_id" placeholder="Staff ID (e.g. 000100)" class="border-gray-200 rounded-lg bg-gray-50 text-sm px-3 py-1.5 focus:ring-brand focus:border-brand" required>
                    <button class="bg-brand text-white text-sm font-medium px-4 py-1.5 rounded-lg hover:bg-blue-800">Check in</button>
                </form>
            </div>
        @endif
    </div>

    @if(!$registration->checkIn)
    <script>
        const hasWaiver = {{ $registration->waiver ? 'true' : 'false' }};
        const scanBox = document.getElementById('scan-box');
        const waiverCheck = document.getElementById('waiver-check');
        const consentCheck = document.getElementById('consent-check');
        const confirmBtn = document.getElementById('confirm-btn');

        scanBox.addEventListener('click', () => {
            if (!hasWaiver) {
                alert('You must sign the waiver before checking in.');
                window.location.href = "{{ route('registrations.waiver', $registration) }}";
                return;
            }

            scanBox.classList.remove('border-gray-300', 'bg-gray-50');
            scanBox.classList.add('border-emerald-400', 'bg-emerald-50');
            scanBox.innerHTML = `
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-emerald-800 font-semibold text-sm">Gate list matched — welcome!</p>
            `;

            waiverCheck.checked = true;
            consentCheck.checked = true;
            
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('bg-wl-indigo/50', 'cursor-not-allowed');
            confirmBtn.classList.add('bg-wl-indigo', 'hover:bg-indigo-600', 'shadow-md');
        });
    </script>
    @endif
@endsection