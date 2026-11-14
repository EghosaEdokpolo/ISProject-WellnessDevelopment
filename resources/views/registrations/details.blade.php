@extends('layouts.app')

@section('content')
    <div class="mb-8 text-center">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-wl-indigo hover:underline flex items-center justify-center gap-1 mb-6">&larr; Back to events</a>
        <!-- Stepper -->
        <div class="flex justify-center items-center gap-4 text-xs font-semibold text-gray-400 mb-8">
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1">1</span> Your Details</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">2</span> Baseline</span>
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
            <span class="bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded">{{ ucfirst($event->category) }}</span>
            <div>
                <h3 class="font-bold text-gray-900">{{ $event->name }}</h3>
                <p class="text-xs text-gray-600">{{ $event->starts_at->format('D, d M Y') }} · {{ $event->location }}</p>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-6">Step 1 — Your Details</h2>

        <form method="POST" action="{{ route('registrations.store', $event) }}">
            @csrf
            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" class="w-full border-gray-300 rounded-lg bg-gray-50 p-3 text-sm focus:ring-wl-indigo focus:border-wl-indigo" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Staff ID</label>
                    <input type="text" value="{{ auth()->user()->employee_id ?? 'EMP-XXX' }}" class="w-full border-gray-300 rounded-lg bg-gray-100 p-3 text-sm text-gray-500" disabled>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
                    <select name="department" class="w-full border-gray-300 rounded-lg bg-gray-50 p-3 text-sm focus:ring-wl-indigo focus:border-wl-indigo" required>
                        @foreach(['School of Computing & Engineering Sciences', 'Strathmore Business School', 'School of Law', 'Human Resources', 'Finance & Accounting'] as $dept)
                            <option value="{{ $dept }}" {{ auth()->user()->department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Dietary requirements</label>
                    <input type="text" name="dietary_requirements" placeholder="None" class="w-full border-gray-300 rounded-lg bg-gray-50 p-3 text-sm focus:ring-wl-indigo focus:border-wl-indigo">
                </div>
            </div>

            <div class="flex justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('events.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-wl-gold text-wl-navy font-bold text-sm rounded-lg hover:bg-yellow-600 transition">Continue &rarr;</button>
            </div>
        </form>
    </div>
@endsection