<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Arrival Check-In — WPMIS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    @vite(['resources/css/checkin_arrival.css'])
</head>
<body class="arrival-page">

    <div class="arrival-card">
        <div class="arrival-header">
            <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" alt="Strathmore Logo" class="arrival-logo">
            <span class="arrival-badge">Venue Arrival Check-In</span>
            <h1 class="arrival-title">{{ $event->name }}</h1>
            <p class="arrival-meta">
                {{ $event->starts_at->format('D, d M Y • H:i') }} — {{ $event->location }}
            </p>
        </div>

        @if(session('confirmed'))
            <div class="arrival-confirmed">
                <div class="arrival-confirmed-icon">✅</div>
                <h2 class="arrival-confirmed-title">Attendance Confirmed!</h2>
                <p class="arrival-confirmed-text">
                    Welcome, <strong>{{ session('confirmed')['name'] }}</strong>. Your attendance has been recorded and <strong>+{{ session('confirmed')['points'] }} pts</strong> have been added to your balance.
                </p>
                <p class="arrival-confirmed-note">You may now close this page and enjoy the session.</p>
            </div>
        @else
            <form action="{{ route('checkin.verify', $event) }}" method="POST">
                @csrf

                <div class="indemnity-notice">
                    <strong>⚠️ Indemnity & Participation Notice</strong>
                    By checking in, you confirm that you have previously read and signed the event's attached indemnity waiver. Participation in the event is entirely voluntary, and the university is not liable for any accident, incident, loss, or damage to personal property.
                </div>

                <div>
                    <label for="staff_id" class="arrival-label">Enter Your Staff ID</label>
                    <input
                        type="text"
                        name="staff_id"
                        id="staff_id"
                        value="{{ old('staff_id') }}"
                        placeholder="e.g. 123456"
                        required
                        autofocus
                        class="arrival-input"
                    >
                    @error('staff_id')
                        <p class="arrival-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="arrival-submit-btn">
                    Verify Staff ID & Confirm Attendance
                </button>
            </form>
        @endif
    </div>

</body>
</html>
