<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event - WPMIS</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    
    <!-- Dynamic Clean Vite Compilation Pipeline Call -->
    @vite(['resources/css/hr_events_edit.css', 'resources/css/hr_view_style.css'])
</head>
<body>

    <h1>Edit Event: {{ $event->name }}</h1>
    <a href="{{ route('events.management') }}" class="back-link">Back to Events Management</a>

    <div class="edit-card-wrapper">
        
        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group-block">
                <label for="eventName">Event Name</label>
                <input type="text" id="eventName" name="eventName" value="{{ $event->name }}" required>
            </div>

            <div class="form-group-block">
                <label for="date">Date</label>
                <input type="datetime-local" id="date" name="date" value="{{ \Carbon\Carbon::parse($event->starts_at)->format('Y-m-d\TH:i') }}" required>
            </div>
            
            <div class="form-group-block">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="{{ $event->location }}" required>
            </div>

            <div class="form-group-block">
                <label>Wellness Category</label>
                <div class="radio-row" style="flex-wrap: wrap;">
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="physical" value="physical" {{ $event->category === 'physical' ? 'checked' : '' }} required>
                        <label for="physical">Physical</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="mental" value="mental" {{ $event->category === 'mental' ? 'checked' : '' }}>
                        <label for="mental">Mental</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="financial" value="financial" {{ $event->category === 'financial' ? 'checked' : '' }}>
                        <label for="financial">Financial</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="social" value="social" {{ $event->category === 'social' ? 'checked' : '' }}>
                        <label for="social">Social</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="occupational" value="occupational" {{ $event->category === 'occupational' ? 'checked' : '' }}>
                        <label for="occupational">Occupational</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="intellectual" value="intellectual" {{ $event->category === 'intellectual' ? 'checked' : '' }}>
                        <label for="intellectual">Intellectual</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="spiritual" value="spiritual" {{ $event->category === 'spiritual' ? 'checked' : '' }}>
                        <label for="spiritual">Spiritual</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="wellcat" id="environmental" value="environmental" {{ $event->category === 'environmental' ? 'checked' : '' }}>
                        <label for="environmental">Environmental</label>
                    </div>
                </div>

            
            <div class="form-group-block">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required>{{ $event->description }}</textarea>
            </div>

            <div class="form-group-block">
                <label for="comlink">Additional Communication Link</label>
                <input type="url" id="comlink" name="comlink" value="{{ old('comlink', $event->comlink) }}" placeholder="https://...">
            </div>


            <div class="form-group-block">
                <label for="points_awarded_checkin">Points Awarded (on check-in)</label>
                <input type="number" id="points_awarded_checkin" name="points_awarded_checkin" value="{{ $event->points_checkin ?? 5 }}" min="0" required>
            </div>

            <div class="form-group-block">
                <label for="points_awarded_feedback">Points Awarded (on feedback)</label>
                <input type="number" id="points_awarded_feedback" name="points_awarded_feedback" value="{{ $event->points_feedback ?? 5 }}" min="0" required>
            </div>

            <button type="submit" class="btn-update-submit">
                Save Changes & Update Event
            </button>
        </form>
    </div>
</body>
</html>
