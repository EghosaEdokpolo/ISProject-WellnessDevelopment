<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retention Metrics | Strathmore University WPMIS</title>
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
        <div class="mb-8">
            <div class="flex flex-wrap items-center gap-3 mb-1">
                <h1 class="text-3xl font-extrabold text-su-black tracking-tight">Retention & Sustained Participation</h1>

                @if($retention['sustained_participation'] >= 50)
                    <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Strong</span>
                @elseif($retention['sustained_participation'] >= 25)
                    <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Developing</span>
                @else
                    <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">At Risk</span>
                @endif
            </div>
            <p class="text-sm text-su-black/60">Staff who attended 2 or more wellness sessions in the current academic year.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Sustained Participation</h3>
                <div class="text-3xl font-extrabold text-su-blue mt-2">{{ $retention['sustained_participation'] }}%</div>
                <p class="text-xs text-su-black/50 mt-2">{{ $retention['sustained_attendees'] }} of {{ $retention['total_active_staff'] }} active staff</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Average Loyalty</h3>
                <div class="text-3xl font-extrabold text-su-gold mt-2">{{ $retention['avg_loyalty'] }}</div>
                <p class="text-xs text-su-black/50 mt-2">Events attended per active staff member</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Reporting Period</h3>
                <div class="text-sm font-extrabold text-su-black mt-3">{{ $retention['period']['start'] }} → {{ $retention['period']['end'] }}</div>
                <p class="text-xs text-su-black/50 mt-2">Current academic year</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6 flex flex-col">
                <h2 class="text-base font-extrabold text-su-black mb-6">Attendance Frequency</h2>

                <div class="flex flex-col md:flex-row items-center justify-between gap-8 flex-1">
                    <div class="relative w-64 h-64 shrink-0">
                        <canvas id="distributionChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-4xl font-extrabold text-su-black">{{ $retention['total_active_staff'] }}</span>
                            <span class="text-[10px] font-extrabold text-su-black/45 uppercase tracking-wider mt-1">Active Staff</span>
                        </div>
                    </div>

                    <div class="w-full space-y-4">
                        @php
                            $total = max(1, $retention['total_active_staff']);
                            $colors = [
                                '0 Events'  => 'bg-su-black/15',
                                '1 Event'   => 'bg-su-gold',
                                '2 Events'  => 'bg-su-blue',
                                '3+ Events' => 'bg-su-red'
                            ];
                        @endphp

                        @foreach($retention['distribution'] as $label => $count)
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full {{ $colors[$label] ?? 'bg-su-black/20' }}"></span>
                                        <span class="text-xs font-bold text-su-black/80">{{ $label }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-extrabold text-su-black">{{ $count }}</span>
                                        <span class="text-[11px] font-semibold text-su-black/45 ml-1">({{ round(($count / $total) * 100) }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-su-black/5 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $colors[$label] ?? 'bg-su-black/20' }} h-1.5 rounded-full" style="width: {{ ($count / $total) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6">
                <h2 class="text-base font-extrabold text-su-black mb-4">Top Wellness Champions</h2>
                <div class="space-y-3">
                    @forelse($retention['top_attendees'] as $index => $attendee)
                        <div class="flex items-center justify-between bg-su-black/[0.02] rounded-xl px-4 py-3 border border-su-black/10 hover:bg-white hover:border-su-blue/25 hover:shadow-sm transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-su-gold text-su-black' : 'bg-su-blue/10 text-su-blue' }} flex items-center justify-center font-extrabold text-xs">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="text-xs font-extrabold text-su-black">{{ $attendee->name }}</div>
                                    <div class="text-[11px] text-su-black/55">{{ $attendee->department }}</div>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-su-blue bg-su-blue/10 px-3 py-1 rounded-full">{{ $attendee->attended_count }} events</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No attendance data yet.</p>
                    @endforelse
                </div>
            </div>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('distributionChart').getContext('2d');
            const dist = @json($retention['distribution']);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dist),
                    datasets: [{
                        data: Object.values(dist),
                        backgroundColor: ['#d4d4d2', '#c99a2e', '#02338d', '#e03c31'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1d1d1b',
                            padding: 12,
                            bodyFont: { family: 'Montserrat' },
                            titleFont: { family: 'Montserrat', weight: 'bold' }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>