<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Impact Score | Strathmore University</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        @php
            $catCollection = collect($categories);
            $overallAvgLift = round($catCollection->avg('avg_lift') ?? 0, 1);
            $highestStats = $highestDim ? ($categories[$highestDim] ?? []) : [];
            $lowestStats  = $lowestDim ? ($categories[$lowestDim] ?? []) : [];
            $distinctExtremes = $highestDim && $lowestDim && $highestDim !== $lowestDim;
        @endphp

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Wellness Impact Score</h1>
                    
                    @if($overallAvgLift > 2)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Positive Impact</span>
                    @elseif($overallAvgLift > 0)
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Moderate Impact</span>
                    @else
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">WHO-5 Well-Being Index — validated instrument (score 0–25). Pre vs post comparison across all 8 wellness dimensions.</p>
            </div>

            <div class="flex gap-1 bg-white p-1 rounded-xl border border-gray-200 shadow-sm w-fit flex-wrap">
                <a href="{{ route('impact.analytics') }}" class="px-4 py-1.5 rounded-lg text-xs font-bold {{ !$activeCategory ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">All</a>
                @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                    <a href="{{ route('impact.analytics', ['category' => $cat]) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold capitalize {{ $activeCategory === $cat ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Highest & Lowest Impact Dimension Comparative Analysis -->
        @if($highestDim && $lowestDim)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Highest Impact Dimension -->
                <div class="bg-white rounded-2xl border-l-4 border-l-emerald-500 border border-gray-200 shadow-sm p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Highest Impact Wellness Dimension
                        </span>
                        <h2 class="text-2xl font-extrabold text-gray-900 capitalize mt-2">{{ $highestDim }} Wellness</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(isset($highestStats['pre_avg'], $highestStats['post_avg']))
                                Pre Avg: <strong>{{ number_format((float) $highestStats['pre_avg'], 1) }}</strong> → Post Avg: <strong>{{ number_format((float) $highestStats['post_avg'], 1) }}</strong>
                            @else
                                Strongest average WHO-5 wellbeing improvement among evaluated dimensions.
                            @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-2xl font-extrabold text-emerald-600">
                            {{ ($highestStats['avg_lift'] ?? 0) > 0 ? '+' : '' }}{{ number_format((float) ($highestStats['avg_lift'] ?? 0), 1) }}
                        </div>
                        <span class="text-[11px] font-bold text-gray-400">Avg WHO-5 Lift</span>
                    </div>
                </div>

                <!-- Lowest Impact Dimension -->
                <div class="bg-white rounded-2xl border-l-4 border-l-rose-500 border border-gray-200 shadow-sm p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                            {{ $distinctExtremes ? 'Lowest Impact Wellness Dimension' : 'Secondary Dimension Benchmark' }}
                        </span>
                        <h2 class="text-2xl font-extrabold text-gray-900 capitalize mt-2">
                            {{ $distinctExtremes ? $lowestDim . ' Wellness' : 'Awaiting Multi-Dimension Data' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($distinctExtremes)
                                @if(isset($lowestStats['pre_avg'], $lowestStats['post_avg']))
                                    Pre Avg: <strong>{{ number_format((float) $lowestStats['pre_avg'], 1) }}</strong> → Post Avg: <strong>{{ number_format((float) $lowestStats['post_avg'], 1) }}</strong> — prioritize targeted HR interventions.
                                @else
                                    Lowest net WHO-5 lift — prioritize targeted HR program redesign.
                                @endif
                            @else
                                Survey responses are currently recorded for one dimension; collect post-event feedback in additional categories to compare.
                            @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        @if($distinctExtremes)
                            <div class="text-2xl font-extrabold {{ ($lowestStats['avg_lift'] ?? 0) >= 0 ? 'text-amber-600' : 'text-rose-600' }}">
                                {{ ($lowestStats['avg_lift'] ?? 0) > 0 ? '+' : '' }}{{ number_format((float) ($lowestStats['avg_lift'] ?? 0), 1) }}
                            </div>
                            <span class="text-[11px] font-bold text-gray-400">Avg WHO-5 Lift</span>
                        @else
                            <div class="text-lg font-extrabold text-gray-400">—</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Avg WHO-5 Lift ({{ $activeCategory ? ucfirst($activeCategory) : 'All' }})</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">
                    {{ $overallAvgLift > 0 ? '+' : '' }}{{ $overallAvgLift }}
                </div>
                <p class="text-xs text-gray-400 mt-2">Points gained (0–25 scale)</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Events Analysed</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ count($eventTable) }}</div>
                <p class="text-xs text-gray-400 mt-2">Events with survey data</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Total Responses</h3>
                <div class="text-3xl font-extrabold text-brand mt-1">{{ collect($eventTable)->sum('responses') }}</div>
                <p class="text-xs text-gray-400 mt-2">Completed pre & post pairs</p>
            </div>
        </div>

        <!-- Dimension-by-Dimension Comparison Grid (Visible on "All") -->
        @if(!$activeCategory && count($categories) > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Impact Breakdown by Wellness Dimension</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($categories as $dimName => $dimData)
                        @php $lift = round((float) ($dimData['avg_lift'] ?? 0), 1); @endphp
                        <a href="{{ route('impact.analytics', ['category' => $dimName]) }}"
                           class="p-4 rounded-xl border {{ $dimName === $highestDim ? 'border-emerald-200 bg-emerald-50/40' : ($distinctExtremes && $dimName === $lowestDim ? 'border-rose-200 bg-rose-50/40' : 'border-gray-100 bg-gray-50') }} hover:border-brand transition">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold uppercase text-gray-700 capitalize">{{ $dimName }}</span>
                                @if($dimName === $highestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Highest</span>
                                @elseif($distinctExtremes && $dimName === $lowestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-rose-100 text-rose-700">Lowest</span>
                                @endif
                            </div>
                            <div class="text-xl font-extrabold mt-2 {{ $lift > 0 ? 'text-emerald-600' : ($lift < 0 ? 'text-rose-600' : 'text-gray-600') }}">
                                {{ $lift > 0 ? '+' : '' }}{{ $lift }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5">WHO-5 Net Lift</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pre / Post Chart -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-6">WHO-5 Pre / Post by Event</h2>
            @if(count($eventTable) > 0 && collect($eventTable)->sum('responses') > 0)
                <div class="h-80 w-full"><canvas id="impactChart"></canvas></div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-sm font-medium">No survey data yet.</p>
                </div>
            @endif
        </div>

        <!-- Event-Level Table -->
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
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No events found for this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    @if(count($eventTable) > 0 && collect($eventTable)->sum('responses') > 0)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('impactChart').getContext('2d');
            const rows = @json($eventTable);
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: rows.map(r => r.event),
                    datasets: [
                        { label: 'Pre Avg', data: rows.map(r => r.pre_average), backgroundColor: '#cbd5e1', borderRadius: 4, barPercentage: 0.6 },
                        { label: 'Post Avg', data: rows.map(r => r.post_average), backgroundColor: '#02338d', borderRadius: 4, barPercentage: 0.6 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Sansation', weight: '600' }, usePointStyle: true, boxWidth: 8 } } },
                    scales: { y: { beginAtZero: true, max: 25, grid: { color: '#f3f4f6', drawBorder: false } }, x: { grid: { display: false }, ticks: { font: { family: 'Sansation', weight: '600' } } } }
                }
            });
        });
    </script>
    @endif
</body>
</html>