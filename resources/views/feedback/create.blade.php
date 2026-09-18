@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6 max-w-xl mx-auto">
        <h3 class="font-bold text-gray-900 mb-1">Post-event Wellbeing Check</h3>
        <p class="text-xs text-gray-500 mb-5">Rate each statement for the past two weeks.</p>

        <form method="POST" action="{{ route('feedback.store', $registration) }}" class="space-y-6">
            @csrf

            @php
                // POINTER: keeping the WHO-5 questions in one PHP array (rather
                // than writing the same block of HTML 5 times) means if the
                // wording ever needs a tweak, you change it in exactly one place.
                $questions = [
                    'item_cheerful' => 'I have felt cheerful and in good spirits',
                    'item_calm' => 'I have felt calm and relaxed',
                    'item_active' => 'I have felt active and vigorous',
                    'item_rested' => 'I woke up feeling fresh and rested',
                    'item_interested' => 'My daily life has been filled with things that interest me',
                ];
                $scale = ['At no time', 'Some of the time', 'Less than half', 'More than half', 'All of the time'];
            @endphp

            @foreach($questions as $field => $label)
                <div>
                    <label class="text-sm font-medium text-gray-800">{{ $loop->iteration }}. {{ $label }}</label>
                    <div class="grid grid-cols-5 gap-2 mt-2">
                        @foreach($scale as $value => $scaleLabel)
                            <label class="cursor-pointer">
                                <input type="radio" name="{{ $field }}" value="{{ $value }}" class="peer sr-only" required>
                                <span class="block text-center border border-gray-200 rounded-lg py-2 text-xs peer-checked:bg-wl-indigo peer-checked:text-white peer-checked:border-wl-indigo">
                                    <span class="block font-bold">{{ $value }}</span>
                                    <span class="text-[10px] opacity-80">{{ $scaleLabel }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error($field) <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <hr class="border-gray-100">

            <div>
                <label class="text-sm font-medium text-gray-800 block mb-2">Overall satisfaction</label>
                <div class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="satisfaction_stars" value="{{ $i }}" class="peer sr-only" required>
                            <span class="text-2xl text-gray-300 peer-checked:text-wl-gold">★</span>
                        </label>
                    @endfor
                </div>
                @error('satisfaction_stars') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-800">Social interaction quality</label>
                <input type="range" name="social_quality" min="1" max="10" value="5" class="w-full accent-wl-indigo">
                @error('social_quality') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <textarea name="comments" rows="2" placeholder="Any additional comments..."
                          class="w-full border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo"></textarea>
            </div>

            <button class="w-full bg-wl-gold text-wl-navy font-semibold py-2.5 rounded-lg">
                Submit feedback
            </button>
        </form>
    </div>
@endsection
