<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Overview Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } }
        }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">HR Overview</h1>
                <p class="text-sm text-gray-500 mt-1">Live analytics for Semester 1, {{ date('Y') }}</p>
            </div>
            <div class="text-xs font-bold text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Data
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">Leading</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Registration Rate</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $registrationRate }}%</div>
                <p class="text-xs text-gray-400 mt-2">{{ $registeredStaff }} of {{ $totalStaff }} staff</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">Leading</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Consent & Waivers</h3>
                <div class="flex items-baseline gap-2 mt-1">
                    <div class="text-3xl font-extrabold text-gray-900">{{ $consentRate }}%</div>
                    <span class="text-xs text-gray-400">/ {{ $waiverRate }}%</span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Consent / Waiver completion</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full uppercase">Lagging</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Avg WHO-5 Lift</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">+{{ $avgLift }}</div>
                <p class="text-xs text-gray-400 mt-2">Pre – Post delta</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">Leading</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Points</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalPointsEarned) }}</div>
                <p class="text-xs text-gray-400 mt-2">Earned across all staff</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Participation by Category</h2>
                </div>
                <div class="h-72 w-full">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h2>
                <div class="flex-1 overflow-y-auto pr-2 space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $activity['marker'] === 'marker-green' ? 'bg-emerald-500' : ($activity['marker'] === 'marker-blue' ? 'bg-blue-500' : 'bg-amber-500') }}"></div>
                            <div>
                                <div class="text-sm text-gray-700 leading-snug">{!! $activity['text'] !!}</div>
                                <div class="text-[11px] font-medium text-gray-400 mt-1">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No recent activity.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Most Attended Event</h2>
                <div class="text-xl font-bold text-gray-900">{{ $mostAttendedEvent ? $mostAttendedEvent['name'] : 'No data yet' }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $mostAttendedEvent ? $mostAttendedEvent['total'] . ' staff registered' : '—' }}</div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Qualitative Feedback</h2>
                <div class="space-y-4">
                    @forelse($qualitativeFeedback as $fb)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <p class="text-sm text-gray-700 italic">"{{ $fb['comment'] }}"</p>
                            <div class="text-xs text-gray-400 mt-2">— <span class="font-semibold text-gray-600">{{ $fb['user_name'] }}</span> at {{ $fb['event_name'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No written feedback submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(2, 51, 141, 0.9)');
            gradient.addColorStop(1, 'rgba(2, 51, 141, 0.4)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Physical', 'Mental', 'Financial', 'Social'],
                    datasets: [{
                        label: 'Registrations',
                        data: [{{ $physicalCount }}, {{ $mentalCount }}, {{ $financialCount }}, {{ $socialCount }}],
                        backgroundColor: gradient,
                        borderColor: '#02338d',
                        borderWidth: 1,
                        borderRadius: 6,
                        barThickness: 50,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1f2937', padding: 12, displayColors: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6', drawBorder: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</body>
</html>