<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Impact Score | Strathmore University WPMIS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'su-blue': '#02338d',
                        'su-blue-dark': '#012466',
                        'su-gold': '#c99a2e',
                        'su-gold-tint': '#f5eccd',
                        'su-red': '#e03c31',
                        'su-black': '#1d1d1b'
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        wordmark: ['Sansation', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fb;
            color: #1d1d1b;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <x-hr-nav />

    <main class="max-w-7xl w-full mx-auto px-6 py-8 flex-1">
        @php
            $catCollection = collect($categories);
            $overallAvgLift = round($catCollection->avg('avg_lift') ?? 0, 1);
            $highestStats = $highestDim ? ($categories[$highestDim] ?? []) : [];
            $lowestStats  = $lowestDim ? ($categories[$lowestDim] ?? []) : [];
            $distinctExtremes = $highestDim && $lowestDim && $highestDim !== $lowestDim;
        @endphp

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-3xl font-extrabold text-su-black tracking-tight">Wellness Impact Score</h1>

                    @if($overallAvgLift > 2)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Positive Impact</span>
                    @elseif($overallAvgLift > 0)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Moderate Impact</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-sm text-su-black/60">WHO-5 Well-Being Index (0–25 scale). Pre vs. post comparison across all 8 wellness dimensions.</p>
            </div>

            <div class="flex gap-1 bg-white p-1.5 rounded-2xl border border-su-black/10 shadow-sm w-fit flex-wrap">
                <a href="{{ route('impact.analytics') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold {{ !$activeCategory ? 'bg-su-blue text-white shadow-sm' : 'text-su-black/70 hover:bg-su-black/5' }}">All</a>
                @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                    <a href="{{ route('impact.analytics', ['category' => $cat]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold capitalize {{ $activeCategory === $cat ? 'bg-su-blue text-white shadow-sm' : 'text-su-black/70 hover:bg-su-black/5' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Highest & Lowest Impact Dimension Comparative Analysis -->
        @if($highestDim && $lowestDim)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl border-l-4 border-l-su-blue border border-su-black/10 shadow-sm p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Highest Impact Wellness Dimension
                        </span>
                        <h2 class="text-xl font-extrabold text-su-black capitalize mt-2.5">{{ $highestDim }} Wellness</h2>
                        <p class="text-xs text-su-black/60 mt-1">
                            @if(isset($highestStats['pre_avg'], $highestStats['post_avg']))
                                Pre Avg: <strong class="text-su-black">{{ number_format((float) $highestStats['pre_avg'], 1) }}</strong> → Post Avg: <strong class="text-su-black">{{ number_format((float) $highestStats['post_avg'], 1) }}</strong>
                            @else
                                Strongest average WHO-5 wellbeing improvement among evaluated dimensions.
                            @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-2xl font-extrabold text-su-blue">
                            {{ ($highestStats['avg_lift'] ?? 0) > 0 ? '+' : '' }}{{ number_format((float) ($highestStats['avg_lift'] ?? 0), 1) }}
                        </div>
                        <span class="text-[11px] font-bold text-su-black/45">Avg WHO-5 Lift</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border-l-4 {{ $distinctExtremes ? 'border-l-su-red' : 'border-l-su-gold' }} border border-su-black/10 shadow-sm p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold {{ $distinctExtremes ? 'text-su-red bg-su-red/10' : 'text-[#8a6514] bg-su-gold-tint' }} px-2.5 py-1 rounded-full uppercase tracking-wider">
                            {{ $distinctExtremes ? 'Lowest Impact Wellness Dimension' : 'Secondary Dimension Benchmark' }}
                        </span>
                        <h2 class="text-xl font-extrabold text-su-black capitalize mt-2.5">
                            {{ $distinctExtremes ? $lowestDim . ' Wellness' : 'Awaiting Multi-Dimension Data' }}
                        </h2>
                        <p class="text-xs text-su-black/60 mt-1">
                            @if($distinctExtremes)
                                @if(isset($lowestStats['pre_avg'], $lowestStats['post_avg']))
                                    Pre Avg: <strong class="text-su-black">{{ number_format((float) $lowestStats['pre_avg'], 1) }}</strong> → Post Avg: <strong class="text-su-black">{{ number_format((float) $lowestStats['post_avg'], 1) }}</strong> — prioritize targeted HR interventions.
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
                            <div class="text-2xl font-extrabold {{ ($lowestStats['avg_lift'] ?? 0) >= 0 ? 'text-[#8a6514]' : 'text-su-red' }}">
                                {{ ($lowestStats['avg_lift'] ?? 0) > 0 ? '+' : '' }}{{ number_format((float) ($lowestStats['avg_lift'] ?? 0), 1) }}
                            </div>
                            <span class="text-[11px] font-bold text-su-black/45">Avg WHO-5 Lift</span>
                        @else
                            <div class="text-lg font-extrabold text-su-black/35">—</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Avg WHO-5 Lift ({{ $activeCategory ? ucfirst($activeCategory) : 'All' }})</h3>
                <div class="text-3xl font-extrabold text-su-blue mt-2">
                    {{ $overallAvgLift > 0 ? '+' : '' }}{{ $overallAvgLift }}
                </div>
                <p class="text-xs text-su-black/50 mt-2">Points gained (0–25 scale)</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Events Analysed</h3>
                <div class="text-3xl font-extrabold text-su-black mt-2">{{ count($eventTable) }}</div>
                <p class="text-xs text-su-black/50 mt-2">Events with survey data</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Total Responses</h3>
                <div class="text-3xl font-extrabold text-su-gold mt-2">{{ collect($eventTable)->sum('responses') }}</div>
                <p class="text-xs text-su-black/50 mt-2">Completed pre & post pairs</p>
            </div>
        </div>

        <!-- Dimension-by-Dimension Comparison Grid -->
        @if(!$activeCategory && count($categories) > 0)
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6 mb-8">
                <h2 class="text-base font-extrabold text-su-black mb-4">Impact Breakdown by Wellness Dimension</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($categories as $dimName => $dimData)
                        @php $lift = round((float) ($dimData['avg_lift'] ?? 0), 1); @endphp
                        <a href="{{ route('impact.analytics', ['category' => $dimName]) }}"
                           class="p-4 rounded-xl border {{ $dimName === $highestDim ? 'border-su-blue/30 bg-su-blue/5' : ($distinctExtremes && $dimName === $lowestDim ? 'border-su-red/30 bg-su-red/5' : 'border-su-black/10 bg-su-black/[0.02]') }} hover:border-su-blue transition">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold uppercase text-su-black capitalize">{{ $dimName }}</span>
                                @if($dimName === $highestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-su-blue text-white">Highest</span>
                                @elseif($distinctExtremes && $dimName === $lowestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-su-red text-white">Lowest</span>
                                @endif
                            </div>
                            <div class="text-xl font-extrabold mt-2 {{ $lift > 0 ? 'text-su-blue' : ($lift < 0 ? 'text-su-red' : 'text-su-black/60') }}">
                                {{ $lift > 0 ? '+' : '' }}{{ $lift }}
                            </div>
                            <div class="text-[11px] text-su-black/50 mt-0.5">WHO-5 Net Lift</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pre / Post Chart -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6 mb-8">
            <h2 class="text-base font-extrabold text-su-black mb-6">WHO-5 Pre / Post Comparison by Event</h2>
            @if(count($eventTable) > 0 && collect($eventTable)->sum('responses') > 0)
                <div class="h-80 w-full"><canvas id="impactChart"></canvas></div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-su-black/45 bg-su-black/[0.02] rounded-xl border border-dashed border-su-black/15">
                    <svg class="w-12 h-12 mb-3 text-su-black/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-xs font-bold">No WHO-5 survey pairs recorded yet.</p>
                </div>
            @endif
        </div>

        <!-- Event-Level Table -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-su-blue text-white text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Responses</th>
                        <th class="px-6 py-4 text-center">Pre Avg</th>
                        <th class="px-6 py-4 text-center">Post Avg</th>
                        <th class="px-6 py-4 text-center">Lift</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-su-black/10 text-xs">
                    @forelse($eventTable as $row)
                        <tr class="hover:bg-su-blue/[0.02] transition-colors">
                            <td class="px-6 py-4 font-bold text-su-black">{{ $row['event'] }}</td>
                            <td class="px-6 py-4 text-su-black/60 capitalize font-semibold">{{ $row['category'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-su-black/80">{{ $row['responses'] }}</td>
                            <td class="px-6 py-4 text-center text-su-black/70 font-semibold">{{ $row['pre_average'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-center text-su-black/70 font-semibold">{{ $row['post_average'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($row['lift'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-su-blue/10 text-su-blue border border-su-blue/20 font-extrabold text-[11px]">+{{ $row['lift'] }}</span>
                                @elseif($row['lift'] < 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-su-red/10 text-su-red border border-su-red/25 font-extrabold text-[11px]">{{ $row['lift'] }}</span>
                                @else
                                    <span class="text-su-black/40 font-bold">0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-su-black/55">No events found for this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <footer class="border-t border-su-black/10 bg-white mt-12">
        <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-su-black/60">
            <div class="flex items-center gap-2">
                <span class="font-wordmark font-bold text-su-black text-sm">Strathmore</span>
                <span class="font-wordmark tracking-widest uppercase text-[11px] text-su-black/75">University</span>
                <span class="text-su-black/30">|</span>
                <span class="italic text-[11px]">Ut omnes unum sint</span>
            </div>
            <div class="text-[11px]">
                Wellness Performance Management Information System • Ole Sangale Road, Madaraka Estate, Nairobi
            </div>
        </div>
    </footer>

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
                        { label: 'Pre Avg', data: rows.map(r => r.pre_average), backgroundColor: '#c99a2e', borderRadius: 4, barPercentage: 0.6 },
                        { label: 'Post Avg', data: rows.map(r => r.post_average), backgroundColor: '#02338d', borderRadius: 4, barPercentage: 0.6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { family: 'Montserrat', weight: '600' }, usePointStyle: true, boxWidth: 8 }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 25, grid: { color: '#eef0f4', drawBorder: false } },
                        x: { grid: { display: false }, ticks: { font: { family: 'Montserrat', weight: '600' } } }
                    }
                }
            });
        });
    </script>
    @endif
</body>
</html>