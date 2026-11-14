<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendees</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Attendee Table</h1>
                <p class="text-sm text-gray-500 mt-1">Full participation record incl. WHO-5 scores.</p>
            </div>
            <a href="{{ route('reports.departmental.export') }}" class="bg-wl-gold text-brand px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-yellow-600 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export WPMIS Report
            </a>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row gap-4">
            <form method="GET" action="{{ route('attendees.index') }}" class="flex flex-col md:flex-row gap-4 w-full">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by staff name or ID..." class="w-full border-gray-200 rounded-lg text-sm pl-10 py-2.5 focus:ring-brand focus:border-brand shadow-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <select name="event_id" class="border-gray-200 rounded-lg text-sm py-2.5 shadow-sm focus:ring-brand focus:border-brand min-w-[200px]" onchange="this.form.submit()">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="border-gray-200 rounded-lg text-sm py-2.5 shadow-sm focus:ring-brand focus:border-brand min-w-[150px]" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Registered</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                @if(request()->hasAny(['search', 'event_id', 'status']))
                    <a href="{{ route('attendees.index') }}" class="px-4 py-2.5 text-sm font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 flex items-center justify-center transition">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Event</th>
                            <th class="px-6 py-4 text-center">Attended</th>
                            <th class="px-6 py-4 text-center">Consent</th>
                            <th class="px-6 py-4 text-center">Feedback</th>
                            <th class="px-6 py-4 text-center">WHO-5 Pre</th>
                            <th class="px-6 py-4 text-center">WHO-5 Post</th>
                            <th class="px-6 py-4 text-center">Points</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($registrations as $reg)
                            @php
                                $pre = $reg->wellbeingResponses->firstWhere('stage', 'pre');
                                $post = $reg->wellbeingResponses->firstWhere('stage', 'post');
                                $lift = ($pre && $post) ? ($post->total_score - $pre->total_score) : 0;
                                $statusClass = match($reg->status) {
                                    'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'checked_in' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default      => 'bg-gray-100 text-gray-600 border-gray-200',
                                };
                                $statusLabel = str_replace('_', ' ', $reg->status);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $reg->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $reg->user->department }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->event->name }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($reg->checkIn)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($reg->waiver)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($reg->feedback)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-700">{{ $pre->total_score ?? '—' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-gray-700">
                                    {{ $post->total_score ?? '—' }}
                                    @if($lift > 0)
                                        <span class="text-[10px] text-emerald-600 block">+{{ $lift }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-extrabold text-brand">{{ $reg->user->points_balance }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="text-4xl mb-3">📭</div>
                                    <h3 class="text-lg font-bold text-gray-900">No records found</h3>
                                    <p class="text-sm text-gray-500 mt-1">Try adjusting your filters or search query.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($registrations->hasPages())
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </main>
</body>
</html>