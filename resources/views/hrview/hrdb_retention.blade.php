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
        .font-wordmark {
            font-family: 'Sansation', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <x-hr-nav />

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 py-8 flex-1">
        <div class="mb-8">
            <div class="flex flex-wrap items-center gap-3 mb-1">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-su-black tracking-tight">Retention & Sustained Participation</h1>

                @if($retention['sustained_participation'] >= 50)
                    <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Strong</span>
                @elseif($retention['sustained_participation'] >= 25)
                    <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Developing</span>
                @else
                    <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">At Risk</span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-su-black/60">Staff who attended 2 or more wellness sessions in the current academic year.</p>
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
                    <div class="relative w-60 h-60 sm:w-64 sm:h-64 shrink-0">
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
                        <div class="flex items-center justify-between gap-3 bg-su-black/[0.02] rounded-xl px-4 py-3 border border-su-black/10 hover:bg-white hover:border-su-blue/25 hover:shadow-sm transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-su-gold text-su-black' : 'bg-su-blue/10 text-su-blue' }} flex items-center justify-center font-extrabold text-xs shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-su-black truncate">{{ $attendee->name }}</div>
                                    <div class="text-[11px] text-su-black/55 truncate">{{ $attendee->department }}</div>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-su-blue bg-su-blue/10 px-3 py-1 rounded-full shrink-0">{{ $attendee->attended_count }} events</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No attendance data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- Contact Us Centered Footer -->
    <footer class="bg-[#02338d] text-white mt-12">
        <div class="max-w-4xl mx-auto px-6 py-10 text-center flex flex-col items-center justify-center space-y-4">
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