@extends('layouts.app')

@section('content')
    <div class="mb-8 text-center">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-wl-indigo hover:underline flex items-center justify-center gap-1 mb-6">&larr; Back to events</a>
        <div class="flex justify-center items-center gap-4 text-xs font-semibold text-gray-400 mb-8">
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Details</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1">2</span> Baseline</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">3</span> Waiver</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">4</span> Consent</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">5</span> Ticket</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto">
        <div class="bg-wl-lavender/40 rounded-lg p-4 mb-6 flex items-center gap-3">
            <span class="bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded">{{ ucfirst($registration->event->category) }}</span>
            <div>
                <h3 class="font-bold text-gray-900">{{ $registration->event->name }}</h3>
                <p class="text-xs text-gray-600">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
            </div>
        </div>

        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-bold text-gray-900">Pre-event Wellbeing Baseline</h2>
            <span class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Leading</span>
        </div>
        <p class="text-sm text-gray-500 mb-6">Rate each statement for the past two weeks.</p>

        <form method="POST" action="{{ route('registrations.who5.store', $registration) }}">
            @csrf
            @php
                $questions = [
                    'item_cheerful' => 'I have felt cheerful and in good spirits',
                    'item_calm' => 'I have felt calm and relaxed',
                    'item_active' => 'I have felt active and vigorous',
                    'item_rested' => 'I woke up feeling fresh and rested',
                    'item_interested' => 'My daily life has been filled with things that interest me',
                ];
                $scale = ['At no time', 'Some of the time', 'Less than half', 'More than half', 'All of the time'];
            @endphp

            <div class="space-y-6 mb-8">
                @foreach($questions as $index => $label)
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-3">{{ $loop->iteration }}. {{ $label }}</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($scale as $value => $scaleLabel)
                                <label class="cursor-pointer">
                                    <input type="radio" name="{{ $index }}" value="{{ $value }}" class="peer sr-only" required>
                                    <div class="text-center border border-gray-200 rounded-lg py-2 hover:bg-gray-50 peer-checked:bg-wl-indigo peer-checked:border-wl-indigo peer-checked:text-white transition-colors">
                                        <div class="font-bold text-sm">{{ $value }}</div>
                                        <div class="text-[9px] leading-tight opacity-70">{{ $scaleLabel }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-wl-lavender/50 rounded-lg p-4 flex justify-between items-center mb-8 border border-wl-indigo/10">
                <span class="text-sm font-semibold text-wl-indigo">WHO-5 Score</span>
                <div class="text-2xl font-extrabold text-emerald-600"><span id="who5-total">0</span><span class="text-sm text-gray-400 font-normal">/20</span></div>
            </div>

            <div class="flex justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('events.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-50">Back</a>
                <button type="submit" class="px-6 py-2.5 bg-wl-gold text-wl-navy font-bold text-sm rounded-lg hover:bg-yellow-600 transition">Continue &rarr;</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                let total = 0;
                ['item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested'].forEach(item => {
                    const selected = document.querySelector(`input[name="${item}"]:checked`);
                    if (selected) total += parseInt(selected.value);
                });
                document.getElementById('who5-total').textContent = total;
            });
        });
    </script>
@endsection