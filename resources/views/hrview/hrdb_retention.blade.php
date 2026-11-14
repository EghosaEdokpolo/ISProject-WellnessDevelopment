<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sustained Participation Rate</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Sustained Participation</h1>
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full uppercase tracking-wider">Lagging</span>
            </div>
            <p class="text-sm text-gray-500">% of staff who attended more than one wellness event. Proves lasting appeal, not just novelty.</p>
            <p class="text-xs font-semibold text-gray-400 mt-2">Academic Year: {{ $retention['period']['start'] }} to {{ $retention['period']['end'] }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-brand to-blue-800 rounded-xl p-6 text-white shadow-md relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10"><svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></div>
                <h3 class="text-blue-100 text-sm font-medium relative z-10">Sustained Rate</h3>
                <div class="text-4xl font-extrabold mt-2 relative z-10">{{ $retention['sustained_participation'] }}%</div>
                <p class="text-xs text-blue-200 mt-2 relative z-10">Attended 2+ events</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg w-fit mb-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <h3 class="text-gray-500 text-sm font-medium">Repeat Participants</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $retention['sustained_attendees'] }}</div>
                <p class="text-xs text-gray-400 mt-2">of {{ $retention['total_active_staff'] }} tracked</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg w-fit mb-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg></div>
                <h3 class="text-gray-500 text-sm font-medium">Avg Loyalty Score</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $retention['avg_loyalty'] }}</div>
                <p class="text-xs text-gray-400 mt-2">events per person avg</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg w-fit mb-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                <h3 class="text-gray-500 text-sm font-medium">Active Staff</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $retention['total_active_staff'] }}</div>
                <p class="text-xs text-gray-400 mt-2">in the system</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Attendance Frequency</h2>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Distribution</span>
                </div>
                <div class="h-64 w-full"><canvas id="retentionChart"></canvas></div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Top Participants</h2>
                <div class="flex-1 space-y-3">
                    @forelse($retention['top_attendees'] as $person)
                        @if($person->attended_count > 0)
                            <div class="flex items-center justify-between p-3 rounded-lg {{ $loop->first ? 'bg-wl-gold/10 border border-wl-gold/20' : 'bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs">{{ substr($person->name, 0, 1) }}</div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $person->name }}</div>
                                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $person->department ?? 'Staff' }}</div>
                                    </div>
                                </div>
                                <div class="text-sm font-extrabold text-brand">{{ $person->attended_count }} <span class="text-[10px] text-gray-400 font-normal">events</span></div>
                            </div>
                        @endif
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">No attendees yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('retentionChart').getContext('2d');
            const labels = {!! json_encode(array_keys($retention['distribution'])) !!};
            const data = {!! json_encode(array_values($retention['distribution'])) !!};
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(2, 51, 141, 0.9)');
            gradient.addColorStop(1, 'rgba(2, 51, 141, 0.5)');

            new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Number of Staff', data: data, backgroundColor: gradient, borderRadius: 6, barThickness: 40 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6', drawBorder: false } }, x: { grid: { display: false } } } }
            });
        });
    </script>
</body>
</html>