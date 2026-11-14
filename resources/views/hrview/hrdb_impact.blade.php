<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness Impact Score</title>
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
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Wellness Impact Score</h1>
                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full uppercase tracking-wider">Lagging</span>
                </div>
                <p class="text-sm text-gray-500">WHO-5 Well-Being Index — validated instrument (score 0–25). Pre vs post comparison per event.</p>
            </div>

            <div class="flex gap-1 bg-white p-1 rounded-xl border border-gray-200 shadow-sm w-fit">
                <a href="{{ route('impact.analytics') }}" class="px-4 py-1.5 rounded-lg text-xs font-bold {{ !$activeCategory ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">All</a>
                @foreach(['physical', 'mental', 'financial', 'social'] as $cat)
                    <a href="{{ route('impact.analytics', ['category' => $cat]) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold capitalize {{ $activeCategory === $cat ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        @if($highestDim && $lowestDim)
            <div class="bg-gradient-to-r from-emerald-50 to-red-50 rounded-xl border border-emerald-100 p-4 mb-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
                    <div>
                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Highest Impact Dimension</p>
                        <p class="text-lg font-bold text-emerald-800 capitalize">{{ $highestDim }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-red-700 uppercase tracking-wider">Lowest Impact Dimension</p>
                        <p class="text-lg font-bold text-red-800 capitalize">{{ $lowestDim }}</p>
                    </div>
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg></div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Avg WHO-5 Lift</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">
                    {{ $categories['mental']['avg_lift'] > 0 ? '+' : '' }}{{ $categories['mental']['avg_lift'] ?? 0 }}
                </div>
                <p class="text-xs text-gray-400 mt-2">Points gained (0–25 scale)</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Events Analysed</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ count($eventTable) }}</div>
                <p class="text-xs text-gray-400 mt-2">Events with survey data</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Total Survey Responses</h3>
                <div class="text-3xl font-extrabold text-brand mt-1">
                    {{ collect($eventTable)->sum('responses') }}
                </div>
                <p class="text-xs text-gray-400 mt-2">Completed pre & post pairs</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-6">WHO-5 Pre / Post / Follow-up by Event</h2>
            
            @if(count($eventTable) > 0 && collect($eventTable)->sum('responses') > 0)
                <div class="h-80 w-full">
                    <canvas id="impactChart"></canvas>
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-sm font-medium">No survey data yet.</p>
                    <p class="text-xs mt-1">Once staff complete their WHO-5 surveys, the chart will appear here.</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Responses</th>
                        <th class="px-6 py-4 text-center">Pre Avg</th>
                        <th class="px-6 py-4 text-center">Post Avg</th>
                        <th class="px-6 py-4 text-center">Lift</th>
                        <th class="px-6 py-4 text-center">Follow-up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($eventTable as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $row['event'] }}</td>
                            <td class="px-6 py-4 text-gray-500 capitalize">{{ $row['category'] }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $row['responses'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $row['pre_average'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $row['post_average'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($row['lift'] > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-700 font-bold text-xs">+{{ $row['lift'] }}</span>
                                @elseif($row['lift'] < 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-red-50 text-red-700 font-bold text-xs">{{ $row['lift'] }}</span>
                                @else
                                    <span class="text-gray-400 font-medium">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $row['follow_up'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No events found for this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    @if(count($eventTable) > 0 && collect($eventTable)->sum('responses') > 0)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('impactChart').getContext('2d');
            const labels = {!! json_encode(array_column($eventTable, 'event')) !!};
            const preData = {!! json_encode(array_column($eventTable, 'pre_average')) !!};
            const postData = {!! json_encode(array_column($eventTable, 'post_average')) !!};
            const followUpData = {!! json_encode(array_column($eventTable, 'follow_up')) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Pre-Event', data: preData, backgroundColor: '#e5e7eb', borderRadius: 4, barPercentage: 0.7 },
                        { label: 'Post-Event', data: postData, backgroundColor: '#02338d', borderRadius: 4, barPercentage: 0.7 },
                        { label: 'Follow-up', data: followUpData, backgroundColor: '#c99a2e', borderRadius: 4, barPercentage: 0.7 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', weight: '600' }, usePointStyle: true, boxWidth: 8 } }, tooltip: { backgroundColor: '#1f2937', padding: 12 } },
                    scales: { y: { beginAtZero: true, max: 25, ticks: { stepSize: 5 }, grid: { color: '#f3f4f6', drawBorder: false } }, x: { grid: { display: false } } }
                }
            });
        });
    </script>
    @endif
</body>
</html>