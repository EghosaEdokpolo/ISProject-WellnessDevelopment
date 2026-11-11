<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Event Management</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
        @vite(['resources/css/hr_view_style.css', 'resources/js/app.js', 'resources/css/hr_eventmanage.css', 'resources/js/hr_eventmanage.js'])
    </head>
    <body>
        <div class="header-title">
            <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" alt="Strathmore University Logo">
            Strathmore University - Wellness Programme Management & Impact System (WPMIS)
        </div>
        <header class="nav-head">
            <nav class="nav-menu">
                <div class="pillgroup1">
                    <span class="category-label">Dashboards</span>
                    <a href="{{ route('dashboard.hr') }}" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
                    <a href="{{ route('impact.analytics') }}" class="nav-link"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon" id="impact-icon"> Impact</a>
                    <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                    <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
                </div>
                <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
                <a href="{{ route('events.management') }}" class="nav-link active"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
                <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            </nav>
        </header>

        <h1>Manage Events</h1>

        @if(session('success'))
            <div class="ev-flash ev-flash-ok">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ev-flash ev-flash-err">{{ $errors->first() }}</div>
        @endif

        <div id="toast" class="ev-toast"></div>

        <main>
            <div class="ev-grid">
                <!-- Create Event -->
                <div class="ev-card">
                    <h2>Create & Publish Events</h2>
                    <p class="ev-desc">Add a new wellness event to the programme.</p>
                    <form method="POST" action="{{ route('events.store') }}" style="display:flex; flex-direction:column; flex:1;">
                        @csrf
                        <div class="ev-field"><label>Event Name</label><input type="text" name="name" value="{{ old('name') }}" required maxlength="255"></div>
                        <div class="ev-field"><label>Date & Time</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required></div>
                        <div class="ev-field"><label>Location</label><input type="text" name="location" value="{{ old('location') }}" required placeholder="Room / Venue" maxlength="255"></div>
                        <div class="ev-field"><label>Capacity</label><input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required></div>
                        
                        <div class="ev-field">
                            <label>Wellness Category</label>
                            <div class="ev-cats">
                                
                                <input type="radio" name="category" id="cat-physical" value="physical" {{ old('category') === 'physical' ? 'checked' : '' }}>
                                <label for="cat-physical">Physical</label>
                                
                                <input type="radio" name="category" id="cat-mental" value="mental" {{ old('category') === 'mental' ? 'checked' : '' }}>
                                <label for="cat-mental">Mental</label>
                                
                                <input type="radio" name="category" id="cat-financial" value="financial" {{ old('category') === 'financial' ? 'checked' : '' }}>
                                <label for="cat-financial">Financial</label>
                                
                                <input type="radio" name="category" id="cat-social" value="social" {{ old('category') === 'social' ? 'checked' : '' }}>
                                <label for="cat-social">Social</label>

                                <input type="radio" name="category" id="cat-occupational" value="occupational" {{ old('category') === 'occupational' ? 'checked' : '' }}>
                                <label for="cat-occupational">Occupational</label>
                                
                                <input type="radio" name="category" id="cat-intellectual" value="intellectual" {{ old('category') === 'intellectual' ? 'checked' : '' }}>
                                <label for="cat-intellectual">Intellectual</label>
                                
                                <input type="radio" name="category" id="cat-spiritual" value="spiritual" {{ old('category') === 'spiritual' ? 'checked' : '' }}>
                                <label for="cat-spiritual">Spiritual</label>
                                
                                <input type="radio" name="category" id="cat-environmental" value="environmental" {{ old('category') === 'environmental' ? 'checked' : '' }}>
                                <label for="cat-environmental">Environmental</label>
                            </div>
                        </div>

                            <div class="ev-field">
                                <label>Points Awarded (on check-in)</label>
                                <input type="number" name="points_checkin" value="{{ old('points_checkin', 5) }}" min="0" required>
                            </div>

                            <div class="ev-field">
                                <label>Points Awarded (on feedback)</label>
                                <input type="number" name="points_feedback" value="{{ old('points_feedback', 5) }}" min="0" required>
                            </div>

                        <div class="ev-field">
                            <label>
                                Description
                            </label>
                            <textarea name="description" rows="3" required style="resize:none;">

                            </textarea>
                        </div>

                        <div class="ev-field">
                            <label >Additional Communication Link</label>
                            <input type="url" name="comlink" value="{{ old('comlink') }}" placeholder="Extra communication link for guests if there is one">
                        </div>

                        

                        <button type="submit" class="ev-btn ev-btn-primary">Publish Event</button>
                    </form>
                </div>

                <!-- QR Code -->
                <div class="ev-card">
                    <h2>Event QR Code</h2>
                    <p class="ev-desc">Click <strong>QR</strong> next to an event to generate its check-in code.</p>
                    <div class="ev-qr-frame" id="qrFrame">
                        <div class="placeholder" id="qrPlaceholder">No QR generated yet</div>
                        <div id="qrHolder" style="display:none;"></div>
                    </div>
                    <div id="qrEventName" style="text-align:center; font-weight:700; color:#02338d; font-size:13px; margin-bottom:8px; min-height:18px;"></div>
                    <button type="button" id="downloadQrBtn" disabled class="ev-btn ev-btn-secondary">Download QR Code</button>
                </div>

                <!-- Upcoming Events -->
                <div class="ev-card">
                    <h2>Upcoming Events</h2>
                    <p class="ev-desc">All published wellness events. Click QR to generate check-in code.</p>
                    <div class="ev-list">
                        @forelse($events as $event)
                            <div class="ev-item">
                                <div class="ev-item-info">
                                    <span class="ev-item-title">{{ $event->name }}</span>
                                    <span class="ev-item-meta">
                                        {{ $event->starts_at->format('D, d M Y • H:i') }} — {{ $event->location }}
                                        • Check-in: {{ $event->points_checkin ?? 0 }} pts | Feedback: {{ $event->points_feedback ?? 0 }} pts
                                    </span>
                                </div>

                                <!-- Action Buttons Group -->
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <!-- 1. QR Button -->
                                    <button type="button" class="ev-item-btn qr-btn" data-qr-id="{{ $event->id }}" data-qr-name="{{ $event->name }}">View QR</button>
                                    
                                    <!-- 2. Edit Button (Links to prefilled edit page) -->
                                    <a href="{{ route('events.edit', $event->id) }}" class="ev-item-btn" style="text-decoration: none; display: inline-flex; align-items: center;">Edit</a>
                                    
                                    <!-- 3. Delete Button (Submits a DELETE form with browser confirmation) -->
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this event? This action is irreversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ev-item-btn" style="background-color: #dc2626; color: #ffffff; border: none; cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; padding:40px 20px; color:#94a3b8; font-size:13px;">No events yet.<br>Create one to get started.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Send Awareness -->
                <div class="ev-card">
                    <h2>Send Awareness Communication</h2>
                    <p class="ev-desc">Email invite to selected staff via Gmail.</p>
                    
                    @php
                        $eventsJson = $events->mapWithKeys(fn($e) => [
                            $e->id => [
                                'name'     => $e->name,
                                'date'     => $e->starts_at->format('D, d M Y • H:i'),
                                'location' => $e->location,
                                'category' => ucfirst($e->category),
                                'capacity' => $e->capacity,
                                'desc'     => $e->description,
                            ]
                        ]);
                    @endphp

                    <div class="ev-field">
                        <label for="eventcategory">Select Event</label>
                        <select id="eventcategory" data-events="{{ json_encode($eventsJson) }}">
                            <option value="">— Choose an event —</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Event Preview</label>
                    <div class="ev-preview" id="eventPreview">
                        <span style="color:#94a3b8;">Select an event above to preview its details.</span>
                    </div>
                    
                    <button type="button" id="sendCommBtn" data-user-email="{{ auth()->user()->email }}" class="ev-btn ev-btn-secondary">
                        Send Email + Calendar Invite
                    </button>
                </div>


        </main>
    </body>
</html>