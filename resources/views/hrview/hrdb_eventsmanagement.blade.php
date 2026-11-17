<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | Strathmore University</title>
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

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-8 flex flex-wrap justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manage Events</h1>
                <p class="text-sm text-gray-500 mt-1">Create upcoming wellness events, log historical past event records, print venue QR posters, and configure WhatsApp channels.</p>
            </div>
            <a href="{{ route('attendees.index') }}" class="text-xs font-bold bg-white text-brand border border-brand/20 px-4 py-2 rounded-lg hover:bg-brand-light transition shadow-sm">
                Open Attendee & Past Data Log →
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-bold border border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 text-rose-600 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Create Upcoming OR Log Past Event -->
            <div class="lg:col-span-5">
                <div id="event-form-card" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-24 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span id="form-heading">Create / Log Event</span>
                        </h2>
                        <span id="mode-badge" class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-brand-light text-brand">
                            Upcoming or Past
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-6">Pick a future date to publish an upcoming event, or select a past date to enter historical wellness records.</p>

                    <div id="duplicate-notice" class="hidden mb-4 bg-brand-light border border-brand/20 text-brand px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-between gap-2">
                        <span>Cloned from past record. Pick a new date below.</span>
                        <button type="button" onclick="resetEventForm()" class="underline hover:text-blue-900 shrink-0">Clear</button>
                    </div>

                    <form id="create-event-form" method="POST" action="{{ route('events.store') }}" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Event Name</label>
                            <input type="text" id="field_name" name="name" value="{{ old('name') }}" required placeholder="e.g., Mental Health Awareness" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Date & Time</label>
                                <input type="text" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" required placeholder="Select past or future date..." class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Capacity</label>
                                <input type="number" id="field_capacity" name="capacity" value="{{ old('capacity', 50) }}" required min="1" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                            <input type="text" id="field_location" name="location" value="{{ old('location') }}" required placeholder="Room / Venue" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Wellness Category</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="category" value="{{ $cat }}" class="peer sr-only" @checked(old('category') === $cat) required>
                                        <div class="text-center py-2 px-3 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 bg-white peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand hover:bg-gray-50 transition-all capitalize">
                                            {{ $cat }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Check-in Pts</label>
                                <input type="number" id="field_points_checkin" name="points_checkin" value="{{ old('points_checkin', 20) }}" required min="0" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Feedback Pts</label>
                                <input type="number" id="field_points_feedback" name="points_feedback" value="{{ old('points_feedback', 10) }}" required min="0" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Event WhatsApp Channel URL</label>
                            <p class="text-[11px] text-gray-400 mb-2">Staff are directed to this link after completing registration and waiver signing.</p>
                            <input type="url" id="field_comlink" name="comlink" value="{{ old('comlink') }}" placeholder="https://whatsapp.com/channel/... or https://chat.whatsapp.com/..." class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                            <textarea id="field_description" name="description" rows="3" required placeholder="Event details..." class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" id="submit-event-btn" class="w-full bg-wl-gold text-brand font-bold py-3.5 rounded-xl shadow-sm hover:bg-yellow-600 transition">
                            Save & Publish Event
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: All Events + Printable Venue QR Generator -->
            <div class="lg:col-span-7 space-y-6">
                
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                        <h2 class="text-lg font-bold text-gray-900">All Wellness Events ({{ $events->count() }})</h2>
                        
                        <div class="flex items-center gap-1.5 bg-gray-100 p-1 rounded-lg">
                            <button type="button" onclick="filterEvents('all', this)" class="event-filter-btn px-2.5 py-1 text-[11px] font-bold rounded-md bg-white text-brand shadow-sm transition">
                                All
                            </button>
                            <button type="button" onclick="filterEvents('upcoming', this)" class="event-filter-btn px-2.5 py-1 text-[11px] font-bold rounded-md text-gray-500 hover:text-gray-800 transition">
                                Upcoming
                            </button>
                            <button type="button" onclick="filterEvents('past', this)" class="event-filter-btn px-2.5 py-1 text-[11px] font-bold rounded-md text-gray-500 hover:text-gray-800 transition">
                                Past Records
                            </button>
                        </div>
                    </div>

                    <div id="events-list-container" class="space-y-3 max-h-[440px] overflow-y-auto pr-2">
                        @forelse($events as $event)
                            @php
                                $isPast = $event->starts_at->isPast();
                                $duplicatePayload = json_encode([
                                    'name' => $event->name,
                                    'capacity' => $event->capacity,
                                    'location' => $event->location,
                                    'category' => $event->category,
                                    'points_checkin' => $event->points_checkin,
                                    'points_feedback' => $event->points_feedback,
                                    'comlink' => $event->comlink,
                                    'description' => $event->description,
                                ]);
                            @endphp
                            <div data-status="{{ $isPast ? 'past' : 'upcoming' }}" class="event-row flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-lg {{ $isPast ? 'bg-slate-100 text-slate-600' : 'bg-brand-light text-brand' }} flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                        {{ substr($event->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm font-bold text-gray-900 truncate">{{ $event->name }}</h3>
                                            @if($isPast)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 inline-flex items-center gap-1">
                                                    Past Record
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Upcoming</span>
                                            @endif
                                            @if($event->comlink)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700">WhatsApp Linked</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $event->starts_at->format('D, M d Y \a\t H:i') }} • {{ $event->location }} • {{ $event->registrations_count ?? 0 }}/{{ $event->capacity }} reg
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($isPast)
                                        <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                            Edit
                                        </a>

                                        <button type="button" data-event="{{ $duplicatePayload }}" onclick="duplicateEvent(this)" class="px-3 py-1.5 text-xs font-bold text-brand bg-brand-light hover:bg-blue-100 rounded-lg transition">
                                            Re-run
                                        </button>

                                        <a href="{{ route('attendees.index', ['event_id' => $event->id]) }}" class="px-3 py-1.5 text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 rounded-lg transition">
                                            Attendance
                                        </a>
                                    @else
                                        <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                            Edit
                                        </a>

                                        <a href="{{ route('events.qr.poster', $event) }}" target="_blank" class="px-3 py-1.5 text-xs font-bold text-amber-900 bg-wl-gold/25 hover:bg-wl-gold/40 rounded-lg transition">
                                            Print Poster
                                        </a>
                                        
                                        <button type="button" onclick="loadQRCode('{{ route('events.qr', $event) }}', '{{ route('events.qr.poster', $event) }}', '{{ addslashes($event->name) }}')" class="px-3 py-1.5 text-xs font-bold text-white bg-brand hover:bg-blue-800 rounded-lg transition">
                                            QR Code
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No events created yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Event QR Code & Print Poster Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Venue Check-In QR Code</h2>
                        <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-1 rounded-md uppercase tracking-wider">Auto-Register & Check-In</span>
                    </div>
                    
                    <div class="border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 flex flex-col items-center justify-center p-8 min-h-[280px] transition-all" id="qr-container">
                        <div id="qr-empty-state" class="text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <p class="text-sm font-bold text-gray-400">Select an upcoming event above to preview or print its venue QR poster.</p>
                        </div>

                        <div id="qr-active-state" class="hidden text-center w-full">
                            <p class="text-sm font-bold text-brand mb-4" id="qr-event-name"></p>
                            <img id="qr-image" src="" alt="QR Code" class="w-48 h-48 mx-auto bg-white p-2 rounded-xl shadow-sm mb-4">
                        </div>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a id="qr-download-btn" href="#" download="event-qr.svg" class="block text-center bg-gray-100 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed transition-all text-xs" style="pointer-events: none;">
                            Download QR SVG
                        </a>
                        <a id="qr-poster-btn" href="#" target="_blank" class="block text-center bg-gray-100 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed transition-all text-xs" style="pointer-events: none;">
                            Open Printable Poster
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let startsAtPicker;

        document.addEventListener('DOMContentLoaded', function() {
            const modeBadge = document.getElementById('mode-badge');
            const submitBtn = document.getElementById('submit-event-btn');

            startsAtPicker = flatpickr("#starts_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S", 
                altInput: true,
                altFormat: "F j, Y at h:i K", 
                time_24hr: false,
                onChange: function(selectedDates) {
                    if (!selectedDates.length) return;
                    if (selectedDates[0] < new Date()) {
                        modeBadge.innerText = 'Past Historical Record';
                        modeBadge.className = 'text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-amber-100 text-amber-800';
                        submitBtn.innerText = 'Log Historical Past Event';
                    } else {
                        modeBadge.innerText = 'Upcoming Event';
                        modeBadge.className = 'text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800';
                        submitBtn.innerText = 'Publish Upcoming Event';
                    }
                }
            });
        });

        function duplicateEvent(button) {
            const data = JSON.parse(button.getAttribute('data-event'));

            document.getElementById('field_name').value = data.name || '';
            document.getElementById('field_capacity').value = data.capacity || 50;
            document.getElementById('field_location').value = data.location || '';
            document.getElementById('field_points_checkin').value = data.points_checkin ?? 20;
            document.getElementById('field_points_feedback').value = data.points_feedback ?? 10;
            document.getElementById('field_comlink').value = data.comlink || '';
            document.getElementById('field_description').value = data.description || '';

            const categoryRadio = document.querySelector(`input[name="category"][value="${data.category}"]`);
            if (categoryRadio) {
                categoryRadio.checked = true;
            }

            if (startsAtPicker) {
                startsAtPicker.clear();
            }

            const modeBadge = document.getElementById('mode-badge');
            const submitBtn = document.getElementById('submit-event-btn');
            modeBadge.innerText = 'Re-running Event';
            modeBadge.className = 'text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-brand-light text-brand';
            submitBtn.innerText = 'Publish New Event Session';

            document.getElementById('duplicate-notice').classList.remove('hidden');

            const formCard = document.getElementById('event-form-card');
            formCard.classList.add('ring-2', 'ring-brand');
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

            setTimeout(() => {
                formCard.classList.remove('ring-2', 'ring-brand');
                if (startsAtPicker) {
                    startsAtPicker.open();
                }
            }, 450);
        }

        function resetEventForm() {
            document.getElementById('create-event-form').reset();
            if (startsAtPicker) {
                startsAtPicker.clear();
            }
            document.getElementById('duplicate-notice').classList.add('hidden');
            const modeBadge = document.getElementById('mode-badge');
            const submitBtn = document.getElementById('submit-event-btn');
            modeBadge.innerText = 'Upcoming or Past';
            modeBadge.className = 'text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-brand-light text-brand';
            submitBtn.innerText = 'Save & Publish Event';
        }

        function filterEvents(status, clickedBtn) {
            document.querySelectorAll('.event-filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-brand', 'shadow-sm');
                btn.classList.add('text-gray-500');
            });
            clickedBtn.classList.add('bg-white', 'text-brand', 'shadow-sm');
            clickedBtn.classList.remove('text-gray-500');

            document.querySelectorAll('.event-row').forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function loadQRCode(qrUrl, posterUrl, eventName) {
            document.getElementById('qr-empty-state').classList.add('hidden');
            document.getElementById('qr-active-state').classList.remove('hidden');
            document.getElementById('qr-event-name').innerText = eventName;
            
            document.getElementById('qr-image').src = qrUrl;

            const dlBtn = document.getElementById('qr-download-btn');
            dlBtn.href = qrUrl;
            dlBtn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
            dlBtn.classList.add('bg-brand', 'text-white', 'hover:bg-blue-800', 'shadow-sm');
            dlBtn.style.pointerEvents = 'auto';

            const posterBtn = document.getElementById('qr-poster-btn');
            posterBtn.href = posterUrl;
            posterBtn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
            posterBtn.classList.add('bg-wl-gold', 'text-brand', 'hover:bg-yellow-500', 'shadow-sm');
            posterBtn.style.pointerEvents = 'auto';
        }
    </script>
</body>
</html>