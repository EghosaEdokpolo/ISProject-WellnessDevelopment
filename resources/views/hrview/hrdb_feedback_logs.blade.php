<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Feedback Analysis Logs</title>
    <!-- We load ONLY your clean, dedicated feedback logs stylesheet here -->
    @vite(['resources/css/hr_feedback_logs.css'])
</head>
<body class="feedback-logs-body">

    <!-- Top Navigation Link -->
    <a href="{{ route('dashboard.hr') }}" class="back-nav-link">← Back to Main Dashboard Overview</a>

    <!-- Headings Header Track -->
    <h1 class="logs-title">Qualitative Feedback Analysis Logs</h1>
    <p class="logs-subtitle">Review and filter all submitted post-event staff comments across wellness dimensions.</p>

    <!-- THE DYNAMIC DROPDOWN FILTER CARD -->
    <div class="filter-panel-card">
        <form action="{{ route('hr.feedback.logs') }}" method="GET" class="filter-form-layout">
            <div style="display: flex; flex-direction: column; width: 100%; max-width: 400px;">
                <label for="event_id" style="font-size: 14px; font-weight: 700; color: #4b5563;">Filter Comments by Specific Event:</label>
                <select name="event_id" id="event_id" onchange="this.form.submit()" class="select-dropdown-box">
                    <option value="">— Show Feedback for All System Events —</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                            {{ $event->name }} ({{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedEventId)
                <a href="{{ route('hr.feedback.logs') }}" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: bold; margin-top: 20px;">Clear Active Filter ✕</a>
            @endif
        </form>
    </div>

    <!-- THE STREAM ENTRIES TIMELINE MATRIX -->
    <div class="feedback-stream-column">
        @forelse($allFeedback as $fb)
            <div class="feedback-log-row-card">
                
                <!-- Comment Content -->
                <p class="feedback-comment-text">
                    "{{ $fb->comments }}"
                </p>
                
                <!-- Meta Attribution Line Footer -->
                <div class="feedback-meta-line">
                    <span class="author-attribution">
                        — Staff Member {{ $fb->registration->user->id ?? $fb->registration->user_id }}
                    </span>
                    <span class="event-tag-pill" title="{{ $fb->registration->event->name ?? 'Unknown Event' }}">
                        {{ $fb->registration->event->name ?? 'Unknown Event' }}
                    </span>
                </div>

            </div>
        @empty
            <div class="feedback-log-row-card" style="text-align: center; color: #6b7280; padding: 40px;">
                No written qualitative feedback forms submitted yet for the selected parameters.
            </div>
        @endforelse
    </div>

</body>
</html>
