<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } } }
    </script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } 
        #qr-container svg { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; }
    </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manage Events</h1>
            <p class="text-sm text-gray-500 mt-1">Create new wellness events, generate QR codes, and communicate with staff.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm font-bold border border-emerald-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Event
                    </h2>
                    
                    <form method="POST" action="{{ route('events.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Event Name</label>
                            <input type="text" name="name" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand" placeholder="e.g. Mental Health Awareness">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Date & Time</label>
                                <input type="datetime-local" name="starts_at" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Capacity</label>
                                <input type="number" name="capacity" value="50" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Location</label>
                            <input type="text" name="location" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand" placeholder="Room / Venue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Wellness Category</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label class="cursor-pointer"><input type="radio" name="category" value="physical" class="peer sr-only" checked><div class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-600 peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand transition-colors">Physical</div></label>
                                <label class="cursor-pointer"><input type="radio" name="category" value="mental" class="peer sr-only"><div class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-600 peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand transition-colors">Mental</div></label>
                                <label class="cursor-pointer"><input type="radio" name="category" value="financial" class="peer sr-only"><div class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-600 peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand transition-colors">Financial</div></label>
                                <label class="cursor-pointer"><input type="radio" name="category" value="social" class="peer sr-only"><div class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-600 peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand transition-colors">Social</div></label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Description</label>
                            <textarea name="description" rows="3" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-wl-gold text-brand font-bold py-3 rounded-lg hover:bg-yellow-600 transition shadow-sm mt-2">Publish Event</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Upcoming Events</h2>
                        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">Click QR to generate</span>
                    </div>
                    
                    <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                        @forelse($events as $event)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg {{ $event->category_color }} flex items-center justify-center text-lg">{{ substr($event->category, 0, 1) }}</div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-sm">{{ $event->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $event->starts_at->format('D, d M Y • H:i') }} · {{ $event->location }}</p>
                                    </div>
                                </div>
                                <button type="button" onclick="generateQR({{ $event->id }}, '{{ addslashes($event->name) }}')" class="px-4 py-1.5 bg-brand text-white text-xs font-bold rounded-lg shadow-sm hover:bg-blue-800 transition">QR Code</button>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 text-sm">No events created yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Event QR Code</h2>
                    <div id="qr-container" class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl h-64 flex flex-col items-center justify-center text-gray-400 mb-4 transition-all duration-300 overflow-hidden p-4">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <p class="text-sm font-medium">Select an event above to generate its QR code.</p>
                    </div>
                    <button id="download-qr" disabled class="w-full bg-gray-200 text-gray-400 font-bold py-2.5 rounded-lg cursor-not-allowed transition-colors">Download QR Code</button>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-wl-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Send Awareness Communication
                    </h2>
                    <p class="text-xs text-gray-500 mb-6">Emails invite to selected staff. Emails log to storage/logs/laravel.log.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Select Event</label>
                            <select id="comm-event" onchange="updatePreview()" class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                                <option value="">— Choose an event —</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" data-name="{{ $event->name }}" data-date="{{ $event->starts_at->format('D, d M Y • H:i') }}" data-location="{{ $event->location }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Invitee Category</label>
                            <select id="comm-recipient" class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                                <option value="all">All staff</option>
                                <option value="cafeteria">Cafeteria staff</option>
                                <option value="housekeeping">Housekeeping staff</option>
                                <option value="sces">SCES</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Event Preview</label>
                        <div id="comm-preview" class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-400 h-24 flex items-center justify-center">Select an event above to preview its details.</div>
                    </div>

                    <button onclick="sendCommunication()" id="send-btn" disabled class="w-full bg-gray-200 text-gray-400 font-bold py-2.5 rounded-lg cursor-not-allowed transition-colors mt-4">Send Email + Calendar Invite</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentQrSvg = null;

        async function generateQR(eventId, eventName) {
            const container = document.getElementById('qr-container');
            const downloadBtn = document.getElementById('download-qr');
            container.innerHTML = '<span class="text-sm font-bold text-brand animate-pulse">Generating...</span>';
            downloadBtn.disabled = true;
            downloadBtn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            downloadBtn.classList.remove('bg-brand', 'text-white', 'hover:bg-blue-800');

            try {
                const response = await fetch(`/events/${eventId}/qr`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error('Server error');
                const svgText = await response.text();
                currentQrSvg = svgText;
                container.innerHTML = `<div class="w-full h-full flex flex-col items-center justify-center"><h3 class="font-bold text-gray-900 text-xs mb-1 truncate w-full text-center">${eventName}</h3><div class="flex-1 w-full flex items-center justify-center overflow-hidden"><div class="w-48 h-48 flex items-center justify-center">${svgText}</div></div></div>`;
                container.classList.remove('border-dashed', 'border-gray-200', 'bg-gray-50');
                container.classList.add('border-brand', 'bg-white');
                downloadBtn.disabled = false;
                downloadBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                downloadBtn.classList.add('bg-brand', 'text-white', 'hover:bg-blue-800');
            } catch (error) {
                container.innerHTML = '<span class="text-sm font-bold text-red-500">Failed to generate QR Code.</span>';
            }
        }

        document.getElementById('download-qr').addEventListener('click', () => {
            if (!currentQrSvg) return;
            const blob = new Blob([currentQrSvg], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'event-qr-code.svg';
            a.click();
            URL.revokeObjectURL(url);
        });

        function updatePreview() {
            const select = document.getElementById('comm-event');
            const preview = document.getElementById('comm-preview');
            const sendBtn = document.getElementById('send-btn');
            
            if (!select.value) {
                preview.innerHTML = 'Select an event above to preview its details.';
                preview.classList.add('text-gray-400', 'justify-center', 'items-center');
                preview.classList.remove('text-gray-700', 'block');
                sendBtn.disabled = true;
                sendBtn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                sendBtn.classList.remove('bg-brand', 'text-white', 'hover:bg-blue-800');
                return;
            }
            const option = select.options[select.selectedIndex];
            preview.innerHTML = `<div class="text-left w-full h-full flex flex-col justify-center"><p class="font-bold text-brand text-base mb-1">${option.dataset.name}</p><p class="text-xs text-gray-600"><span class="font-semibold text-gray-800">📅 Date:</span> ${option.dataset.date}</p><p class="text-xs text-gray-600"><span class="font-semibold text-gray-800">📍 Location:</span> ${option.dataset.location}</p></div>`;
            preview.classList.remove('text-gray-400', 'justify-center', 'items-center');
            preview.classList.add('text-gray-700', 'block');
            sendBtn.disabled = false;
            sendBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            sendBtn.classList.add('bg-brand', 'text-white', 'hover:bg-blue-800');
        }

        async function sendCommunication() {
            const eventId = document.getElementById('comm-event').value;
            const recipient = document.getElementById('comm-recipient').value;
            const sendBtn = document.getElementById('send-btn');
            if (!eventId) return;

            const originalText = sendBtn.innerHTML;
            sendBtn.innerHTML = 'Sending...';
            sendBtn.disabled = true;

            try {
                const response = await fetch('{{ route('communications.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ event_id: eventId, recipient: recipient })
                });
                const data = await response.json();
                alert(response.ok ? `✅ Success! ${data.message}` : `❌ Error: ${data.message}`);
            } catch (error) {
                alert('❌ A server error occurred. Check the logs.');
            } finally {
                sendBtn.innerHTML = originalText;
                sendBtn.disabled = false;
            }
        }
    </script>
</body>
</html>