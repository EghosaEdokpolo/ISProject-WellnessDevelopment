<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event | Strathmore University</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } },
                    fontFamily: { sans: ['Sansation', 'sans-serif'] }
                } 
            } 
        }
    </script>
    <style> 
        body, h1, h2, h3, h4, h5, h6, p, div, span, a, button, input, select, textarea, table, th, td { 
            font-family: 'Sansation', sans-serif !important; 
        } 
        body { background-color: #f8fafc; }
        
        .flatpickr-calendar {
            font-family: 'Sansation', sans-serif !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0 !important;
        }
        .flatpickr-day.selected {
            background: #02338d !important;
            border-color: #02338d !important;
        }
    </style>
</head>
<body>
    <x-hr-nav />

    @php
        $isPast = $event->starts_at->isPast();
        $attendeeCount = $event->registrations_count ?? $event->registrations()->count();
        $formattedStartsAt = $event->starts_at->format('Y-m-d H:i:s');
    @endphp

    <main class="max-w-4xl mx-auto px-6 py-8">
        
        <div class="mb-8 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Wellness Event</h1>
                    @if($isPast)
                        <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-amber-100 text-amber-800 inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Audit-Locked Historical Record
                        </span>
                    @else
                        <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-emerald-100 text-emerald-800">
                            Upcoming Event
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $isPast ? 'Correct venue spelling, update summary notes, or modify WhatsApp links while preserving historical points and dates.' : 'Modify upcoming event schedule, venue, capacity, or point allocations.' }}
                </p>
            </div>
            <a href="{{ route('events.management') }}" class="text-xs font-bold bg-white text-brand border border-brand/20 px-4 py-2 rounded-lg hover:bg-brand-light transition shadow-sm">
                ← Back to Manage Events
            </a>
        </div>

        @if($isPast)
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-2xl text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Audit Integrity Protection Active</p>
                    <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                        This event took place on {{ $event->starts_at->format('M d, Y \a\t H:i') }}. To protect staff wellness point ledgers and pillar impact reports, <strong>Date &amp; Time</strong>, <strong>Wellness Category</strong>, and <strong>Point Values</strong> are locked. You may still update the event name, location, description, or WhatsApp channel URL.
                    </p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 text-rose-600 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Event Name</label>
                    <input type="text" name="name" value="{{ old('name', $event->name) }}" required class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</label>
                            @if($isPast)
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Locked</span>
                            @else
                                <span class="text-[10px] font-bold text-brand bg-brand-light px-2 py-0.5 rounded">Future Only</span>
                            @endif
                        </div>

                        @if($isPast)
                            <input type="text" value="{{ $event->starts_at->format('F j, Y \a\t h:i A') }}" disabled class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-100 text-gray-500 cursor-not-allowed">
                            <input type="hidden" name="starts_at" value="{{ $formattedStartsAt }}">
                        @else
                            <input type="text" name="starts_at" id="starts_at" value="{{ old('starts_at', $formattedStartsAt) }}" required class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand cursor-pointer">
                            <p class="text-[11px] text-gray-400 mt-1">Upcoming events can only be rescheduled to future dates and times.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Capacity</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" required min="{{ max(1, $attendeeCount) }}" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        @if($attendeeCount > 0)
                            <p class="text-[11px] text-gray-400 mt-1">Minimum capacity is {{ $attendeeCount }} (current registered staff count).</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                    <input type="text" name="location" value="{{ old('location', $event->location) }}" required class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Wellness Category</label>
                        @if($isPast)
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Locked</span>
                        @endif
                    </div>

                    @if($isPast)
                        <input type="hidden" name="category" value="{{ $event->category }}">
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                            <label class="{{ $isPast ? 'cursor-not-allowed opacity-75' : 'cursor-pointer' }}">
                                <input type="radio" name="category" value="{{ $cat }}" class="peer sr-only" @checked(old('category', $event->category) === $cat) @disabled($isPast) required>
                                <div class="text-center py-2.5 px-3 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 bg-white peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand {{ $isPast ? '' : 'hover:bg-gray-50' }} transition-all capitalize">
                                    {{ $cat }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Check-in Pts</label>
                            @if($isPast)
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Locked</span>
                            @endif
                        </div>
                        @if($isPast)
                            <input type="number" value="{{ $event->points_checkin }}" disabled class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-100 text-gray-500 cursor-not-allowed">
                            <input type="hidden" name="points_checkin" value="{{ $event->points_checkin }}">
                        @else
                            <input type="number" name="points_checkin" value="{{ old('points_checkin', $event->points_checkin) }}" required min="0" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Feedback Pts</label>
                            @if($isPast)
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Locked</span>
                            @endif
                        </div>
                        @if($isPast)
                            <input type="number" value="{{ $event->points_feedback }}" disabled class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-100 text-gray-500 cursor-not-allowed">
                            <input type="hidden" name="points_feedback" value="{{ $event->points_feedback }}">
                        @else
                            <input type="number" name="points_feedback" value="{{ old('points_feedback', $event->points_feedback) }}" required min="0" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Event WhatsApp Channel URL</label>
                    <p class="text-[11px] text-gray-400 mb-2">Staff are directed to this link after completing registration and waiver signing.</p>
                    <input type="url" name="comlink" value="{{ old('comlink', $event->comlink) }}" placeholder="https://whatsapp.com/channel/... or https://chat.whatsapp.com/..." class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        {{ $isPast ? 'Description & Post-Event Summary Notes' : 'Description' }}
                    </label>
                    <textarea name="description" rows="4" required class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">{{ old('description', $event->description) }}</textarea>
                </div>

                <div class="pt-2 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('events.management') }}" class="px-5 py-3 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl text-sm font-bold bg-wl-gold text-brand shadow-sm hover:bg-yellow-600 transition">
                        {{ $isPast ? 'Save Audit-Safe Updates' : 'Update Event' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit-Protected Deletion Card -->
        <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Archive / Remove Event Record</h3>
                @if($isPast && $attendeeCount > 0)
                    <p class="text-xs text-gray-500 mt-0.5">
                        Deletion is disabled because <strong>{{ $attendeeCount }} staff member(s)</strong> have attendance/points tied to this historical record.
                    </p>
                @else
                    <p class="text-xs text-gray-500 mt-0.5">
                        Permanently remove this event if it was created in error and has no active attendance history.
                    </p>
                @endif
            </div>

            @if($isPast && $attendeeCount > 0)
                <span class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 inline-flex items-center gap-1.5 cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Protected Record
                </span>
            @else
                <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to permanently delete this event?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition">
                        Delete Event
                    </button>
                </form>
            @endif
        </div>

    </main>

    @if(!$isPast)
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#starts_at", {
                    enableTime: true,
                    minDate: new Date(),
                    defaultDate: "{{ old('starts_at', $formattedStartsAt) }}",
                    dateFormat: "Y-m-d H:i:S", 
                    altInput: true,
                    altFormat: "F j, Y at h:i K", 
                    time_24hr: false
                });
            });
        </script>
    @endif
</body>
</html>