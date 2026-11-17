@extends('layouts.app')

@push('styles')
    <!-- Flatpickr CSS for the premium date/time picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Custom Strathmore Styling for Flatpickr */
        .flatpickr-calendar {
            font-family: 'Sansation', sans-serif !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #02338d !important;
            border-color: #02338d !important;
        }
        .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover, .flatpickr-time input:focus, .flatpickr-time .flatpickr-am-pm:focus {
            background: #f8fafc !important;
        }
        .flatpickr-time .flatpickr-am-pm {
            color: #02338d !important;
            font-weight: bold !important;
        }
    </style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('events.management') }}" class="text-sm font-bold text-slate-400 hover:text-brand transition mb-2 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Event Management
        </a>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Create New Event</h1>
        <p class="text-sm text-slate-500 mt-1">Publish a new wellness event to the staff portal.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('events.store') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Event Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Mental Health Awareness Talk"
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wellness Category</label>
                        <select name="category" required class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                            <option value="">Select a dimension</option>
                            @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date & Time (The Premium Picker) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date & Time</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <!-- Flatpickr attaches to this input -->
                            <input type="text" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" required placeholder="Select date and time"
                                   class="w-full border-slate-200 rounded-xl text-sm p-3 pl-10 focus:ring-brand focus:border-brand bg-slate-50 cursor-pointer">
                        </div>
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Capacity</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 50) }}" required min="1"
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Location</label>
                        <input type="text" name="location" value="{{ old('location') }}" required placeholder="e.g., Auditorium / Room 4"
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                    </div>

                    <!-- Check-in Points -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Check-in Points</label>
                        <input type="number" name="points_checkin" value="{{ old('points_checkin', 20) }}" required min="0"
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                    </div>

                    <!-- Feedback Points -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Feedback Points</label>
                        <input type="number" name="points_feedback" value="{{ old('points_feedback', 10) }}" required min="0"
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                    </div>

                    <!-- Communication Link -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Communication Link (Optional)</label>
                        <input type="url" name="comlink" value="{{ old('comlink') }}" placeholder="https://chat.whatsapp.com/..."
                               class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">
                        <p class="text-xs text-slate-400 mt-1">Staff will be redirected here after registration.</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                        <textarea name="description" rows="4" required placeholder="Describe the event..."
                                  class="w-full border-slate-200 rounded-xl text-sm p-3 focus:ring-brand focus:border-brand bg-slate-50">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-brand text-white font-bold py-3 px-8 rounded-xl shadow-sm hover:bg-blue-800 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Publish Event
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar / Tips -->
        <div class="lg:col-span-1">
            <div class="bg-brand-light rounded-3xl border border-brand/20 p-6 sticky top-24">
                <h3 class="font-bold text-brand mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Event Guidelines
                </h3>
                <ul class="text-sm text-slate-600 space-y-2 mt-4 list-disc pl-4">
                    <li>Ensure the event falls under one of the 8 SAMHSA dimensions.</li>
                    <li>The WhatsApp/Communication link is crucial for post-event engagement.</li>
                    <li>Check-in points are awarded when staff scan the QR code at the venue.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the premium date/time picker
            flatpickr("#starts_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S", // Format sent to the database
                altInput: true,
                altFormat: "F j, Y at h:i K", // Beautiful format the user sees
                minDate: "today",
                time_24hr: false,
            });
        });
    </script>
@endpush