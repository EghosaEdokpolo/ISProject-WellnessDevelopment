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
        .font-wordmark {
            font-family: 'Sansation', sans-serif;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @media (max-width: 639px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <x-hr-nav />

    <main class="max-w-7xl w-full mx-auto px-3.5 sm:px-6 py-6 sm:py-8 flex-1">
        @php
            $catCollection    = collect($categories);
            $overallAvgLift   = round($catCollection->avg('avg_lift') ?? 0, 1);
            $highestStats     = $highestDim ? ($categories[$highestDim] ?? []) : [];
            $lowestStats      = $lowestDim ? ($categories[$lowestDim] ?? []) : [];
            $distinctExtremes = $highestDim && $lowestDim && $highestDim !== $lowestDim;

            $evaluatedEvents  = collect($eventTable)
                ->filter(fn ($row) => ($row['responses'] ?? 0) > 0 && ($row['pre_average'] ?? null) !== null && ($row['post_average'] ?? null) !== null)
                ->values();

            $monthlyRows      = collect($monthlyTrend ?? [])->values();
            $hasChartData     = $monthlyRows->isNotEmpty() || (! $activeCategory && count($categories) > 0);
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-su-black tracking-tight">Wellness Impact Score</h1>

                    @if($overallAvgLift > 2)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Positive Impact</span>
                    @elseif($overallAvgLift > 0)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Moderate Impact</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm text-su-black/60">WHO-5 Well-Being Index (0–25 scale). Pre vs. post comparison across all 8 wellness dimensions.</p>
            </div>

            <!-- Horizontally Scrollable Category Pills on Mobile/Half-Screen -->
            <div class="flex items-center gap-1 bg-white p-1.5 rounded-2xl border border-su-black/10 shadow-sm max-w-full overflow-x-auto whitespace-nowrap scrollbar-hide">
                <a href="{{ route('impact.analytics') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold shrink-0 {{ !$activeCategory ? 'bg-su-blue text-white shadow-sm' : 'text-su-black/70 hover:bg-su-black/5' }}">All</a>
                @foreach(['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'] as $cat)
                    <a href="{{ route('impact.analytics', ['category' => $cat]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold capitalize shrink-0 {{ $activeCategory === $cat ? 'bg-su-blue text-white shadow-sm' : 'text-su-black/70 hover:bg-su-black/5' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Highest & Lowest Impact Dimension Comparative Analysis -->
        @if($highestDim && $lowestDim)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-8">
                <div class="bg-white rounded-2xl border-l-4 border-l-su-blue border border-su-black/10 shadow-sm p-5 sm:p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Highest Impact Wellness Dimension
                        </span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-su-black capitalize mt-2.5">{{ $highestDim }} Wellness</h2>
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

                <div class="bg-white rounded-2xl border-l-4 {{ $distinctExtremes ? 'border-l-su-red' : 'border-l-su-gold' }} border border-su-black/10 shadow-sm p-5 sm:p-6 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold {{ $distinctExtremes ? 'text-su-red bg-su-red/10' : 'text-[#8a6514] bg-su-gold-tint' }} px-2.5 py-1 rounded-full uppercase tracking-wider">
                            {{ $distinctExtremes ? 'Lowest Impact Wellness Dimension' : 'Secondary Dimension Benchmark' }}
                        </span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-su-black capitalize mt-2.5">
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Avg WHO-5 Lift ({{ $activeCategory ? ucfirst($activeCategory) : 'All' }})</h3>
                <div class="text-3xl font-extrabold text-su-blue mt-2">
                    {{ $overallAvgLift > 0 ? '+' : '' }}{{ $overallAvgLift }}
                </div>
                <p class="text-xs text-su-black/50 mt-2">Points gained (0–25 scale)</p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Events Analysed</h3>
                <div class="text-3xl font-extrabold text-su-black mt-2">{{ $evaluatedEvents->count() }}</div>
                <p class="text-xs text-su-black/50 mt-2">Events with completed Pre & Post pairs</p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Total Responses</h3>
                <div class="text-3xl font-extrabold text-su-gold mt-2">{{ $evaluatedEvents->sum('responses') }}</div>
                <p class="text-xs text-su-black/50 mt-2">Completed pre & post pairs</p>
            </div>
        </div>

        <!-- Dimension-by-Dimension Comparison Grid -->
        @if(!$activeCategory && count($categories) > 0)
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6 mb-8">
                <h2 class="text-base font-extrabold text-su-black mb-4">Impact Breakdown by Wellness Dimension</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($categories as $dimName => $dimData)
                        @php $lift = round((float) ($dimData['avg_lift'] ?? 0), 1); @endphp
                        <a href="{{ route('impact.analytics', ['category' => $dimName]) }}"
                           class="p-3.5 sm:p-4 rounded-xl border {{ $dimName === $highestDim ? 'border-su-blue/30 bg-su-blue/5' : ($distinctExtremes && $dimName === $lowestDim ? 'border-su-red/30 bg-su-red/5' : 'border-su-black/10 bg-su-black/[0.02]') }} hover:border-su-blue transition">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs font-extrabold uppercase text-su-black capitalize truncate">{{ $dimName }}</span>
                                @if($dimName === $highestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-su-blue text-white shrink-0">High</span>
                                @elseif($distinctExtremes && $dimName === $lowestDim)
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-su-red text-white shrink-0">Low</span>
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

        <!-- WHO-5 Pre / Post Comparison Chart (Monthly & By Dimension Only) -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <h2 id="impactChartTitle" class="text-base font-extrabold text-su-black">WHO-5 Pre / Post Impact by Month</h2>
                    <p id="impactChartSubtitle" class="text-xs text-su-black/50 mt-0.5">
                        Average monthly group scores on 0–25 scale · Aggregated from verified Pre & Post survey pairs
                    </p>
                </div>

                @if($hasChartData)
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
                        <button type="button"
                                onclick="switchImpactChartMode('monthly', this)"
                                class="impact-chart-tab px-3.5 py-1.5 text-[11px] font-extrabold rounded-lg bg-white text-su-blue shadow-sm transition">
                            Monthly
                        </button>
                        @if(!$activeCategory && count($categories) > 0)
                            <button type="button"
                                    onclick="switchImpactChartMode('dimensions', this)"
                                    class="impact-chart-tab px-3.5 py-1.5 text-[11px] font-extrabold rounded-lg text-slate-500 hover:text-slate-800 transition">
                                By Dimension
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            @if($monthlyRows->isNotEmpty())
                <div class="overflow-x-auto pb-2">
                    <div id="impactChartContainer" class="h-72 w-full min-w-[460px]">
                        <canvas id="impactChart"></canvas>
                    </div>
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-su-black/45 bg-su-black/[0.02] rounded-xl border border-dashed border-su-black/15">
                    <svg class="w-12 h-12 mb-3 text-su-black/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-xs font-bold">No completed WHO-5 Pre/Post survey pairs recorded yet.</p>
                </div>
            @endif
        </div>

        <!-- Event-Level Table (Proportional Sticky First Column + Mobile Horizontal Scroll) -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm overflow-hidden">
            <div class="sm:hidden px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 flex items-center justify-between">
                <span>Session Breakdown</span>
                <span>← Swipe table horizontally →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left border-collapse">
                    <thead>
                        <tr class="bg-su-blue text-white text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                            <th class="sticky left-0 z-20 bg-su-blue px-4 sm:px-6 py-3.5 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)] min-w-[135px] sm:min-w-[190px]">Event</th>
                            <th class="px-4 sm:px-6 py-3.5">Category</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Responses</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Pre Avg</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Post Avg</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Lift</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-su-black/10 text-xs">
                        @forelse($eventTable as $row)
                            <tr class="group hover:bg-[#f6f8fc] transition-colors">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-[#f6f8fc] px-4 sm:px-6 py-3.5 font-bold text-su-black max-w-[155px] sm:max-w-none truncate sm:whitespace-nowrap border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(2,51,141,0.1)]" title="{{ $row['event'] }}">
                                    {{ $row['event'] }}
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-su-black/60 capitalize font-semibold whitespace-nowrap">{{ $row['category'] }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center font-bold text-su-black/80 whitespace-nowrap">{{ $row['responses'] }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center text-su-black/70 font-semibold whitespace-nowrap">{{ $row['pre_average'] ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center text-su-black/70 font-semibold whitespace-nowrap">{{ $row['post_average'] ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center whitespace-nowrap">
                                    @if(($row['lift'] ?? 0) > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-su-blue/10 text-su-blue border border-su-blue/20 font-extrabold text-[11px]">+{{ $row['lift'] }}</span>
                                    @elseif(($row['lift'] ?? 0) < 0)
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
        </div>
    </main>

    <!-- Contact Us Centered Footer -->
    <footer class="bg-[#02338d] text-white mt-12">
        <div class="max-w-4xl mx-auto px-5 sm:px-6 py-10 text-center flex flex-col items-center justify-center space-y-4">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Contact Us</h2>

            <p class="text-xs sm:text-sm text-white/95 leading-relaxed max-w-xl">
                Madaraka Estate Ole Sangale Road, PO Box 59857, 00200 City Square Nairobi, Kenya
            </p>

            <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-white/95">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-white text-[#02338d] shrink-0">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.61 21 3 13.39 3 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/>
                    </svg>
                </span>
                <span>Phone : (+254) (0)703-034000 (+254) (0)703-034200 (+254) (0)703-034300</span>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-white/95">
                <svg class="w-5 h-5 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span>
                    Email :
                    <a href="mailto:systems@strathmore.edu" class="underline hover:text-[#f5eccd] transition">
                        systems@strathmore.edu
                    </a>
                </span>
            </div>
        </div>
    </footer>

    @if($monthlyRows->isNotEmpty())
    <script>
        const monthlyRows   = @json($monthlyRows->values());
        const dimensionData = @json($categories);
        let impactChartInstance = null;

        function renderImpactChart(mode) {
            const canvas    = document.getElementById('impactChart');
            const container = document.getElementById('impactChartContainer');
            const titleEl   = document.getElementById('impactChartTitle');
            const subEl     = document.getElementById('impactChartSubtitle');
            if (!canvas) return;

            let labels      = [];
            let fullTitles  = [];
            let footerLines = [];
            let preVals     = [];
            let postVals    = [];

            if (mode === 'dimensions') {
                const keys  = Object.keys(dimensionData);
                labels      = keys.map(k => k.charAt(0).toUpperCase() + k.slice(1));
                fullTitles  = labels.map(l => l + ' Wellness');
                preVals     = keys.map(k => dimensionData[k].pre_avg ?? 0);
                postVals    = keys.map(k => dimensionData[k].post_avg ?? 0);
                footerLines = keys.map(k => {
                    const lift = Number(dimensionData[k].avg_lift ?? 0).toFixed(1);
                    return 'Net WHO-5 Lift: ' + (lift > 0 ? '+' : '') + lift;
                });

                container.style.minWidth = '540px';
                if (titleEl) titleEl.textContent = 'WHO-5 Pre / Post Impact by Wellness Dimension';
                if (subEl) subEl.textContent     = 'Average group scores on 0–25 scale across all 8 Strathmore wellness pillars';
            } else {
                labels      = monthlyRows.map(r => r.month);
                fullTitles  = monthlyRows.map(r => r.full_label);
                preVals     = monthlyRows.map(r => r.pre_avg);
                postVals    = monthlyRows.map(r => r.post_avg);
                footerLines = monthlyRows.map(r => {
                    const lift = Number(r.lift ?? 0).toFixed(1);
                    const evLabel = r.events_count === 1 ? '1 session' : `${r.events_count} sessions`;
                    return `${evLabel} · ${r.responses} paired responses · Lift: ${lift > 0 ? '+' : ''}${lift}`;
                });

                container.style.minWidth = Math.max(460, monthlyRows.length * 88) + 'px';
                if (titleEl) titleEl.textContent = 'WHO-5 Pre / Post Impact by Month';
                if (subEl) subEl.textContent     = 'Average monthly group scores on 0–25 scale · Aggregated from verified Pre & Post survey pairs';
            }

            if (impactChartInstance) {
                impactChartInstance.destroy();
            }

            impactChartInstance = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pre-event Avg',
                            data: preVals,
                            backgroundColor: '#c99a2e',
                            borderRadius: 6,
                            maxBarThickness: 36,
                            barPercentage: 0.72,
                            categoryPercentage: 0.56
                        },
                        {
                            label: 'Post-event Avg',
                            data: postVals,
                            backgroundColor: '#02338d',
                            borderRadius: 6,
                            maxBarThickness: 36,
                            barPercentage: 0.72,
                            categoryPercentage: 0.56
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { family: 'Montserrat', weight: '600' }, usePointStyle: true, boxWidth: 8 }
                        },
                        tooltip: {
                            backgroundColor: '#1d1d1b',
                            padding: 12,
                            callbacks: {
                                title: function (items) {
                                    const idx = items[0]?.dataIndex ?? 0;
                                    return fullTitles[idx] || labels[idx];
                                },
                                afterBody: function (items) {
                                    const idx = items[0]?.dataIndex ?? 0;
                                    return footerLines[idx] ? '\n' + footerLines[idx] : '';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 25, ticks: { stepSize: 5 }, grid: { color: '#eef0f4', drawBorder: false } },
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0,
                                font: { family: 'Montserrat', size: 11, weight: '600' }
                            }
                        }
                    }
                }
            });
        }

        function switchImpactChartMode(mode, clickedBtn) {
            document.querySelectorAll('.impact-chart-tab').forEach(btn => {
                btn.classList.remove('bg-white', 'text-su-blue', 'shadow-sm');
                btn.classList.add('text-slate-500');
            });
            clickedBtn.classList.add('bg-white', 'text-su-blue', 'shadow-sm');
            clickedBtn.classList.remove('text-slate-500');
            renderImpactChart(mode);
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderImpactChart('monthly');
        });
    </script>
    @endif
</body>
</html>