<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Overview Dashboard | Strathmore University WPMIS</title>
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
        @media (max-width: 639px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <x-hr-nav />

    <main class="max-w-7xl w-full mx-auto px-3.5 sm:px-6 py-6 sm:py-8 flex-1">
        <div class="mb-6 sm:mb-8 flex flex-wrap justify-between items-end gap-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-su-gold">People & Culture Analytics</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-su-black tracking-tight mt-0.5">HR Overview</h1>
                <p class="text-xs sm:text-sm text-su-black/60 mt-1">Live institutional wellness performance for Academic Year {{ date('Y') }}</p>
            </div>
            <div class="text-xs font-extrabold text-su-blue bg-white px-3.5 py-2 rounded-xl border border-su-blue/20 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-su-blue animate-pulse"></span> Live Data
            </div>
        </div>

        <!-- 4 Executive KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-su-blue/10 text-su-blue rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    @if($registrationRate >= 75)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 px-2.5 py-1 rounded-full uppercase">On Track</span>
                    @elseif($registrationRate >= 40)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint px-2.5 py-1 rounded-full uppercase">Moderate</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 px-2.5 py-1 rounded-full uppercase">Low</span>
                    @endif
                </div>
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Registration Rate</h3>
                <div class="text-3xl font-extrabold text-su-black mt-1">{{ $registrationRate }}%</div>
                <p class="text-xs text-su-black/50 mt-2">{{ $registeredStaff }} of {{ $totalStaff }} staff</p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-su-gold-tint text-[#8a6514] rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    @if($surveyVelocityRate >= 80)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 px-2.5 py-1 rounded-full uppercase">Fast</span>
                    @elseif($surveyVelocityRate >= 50)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint px-2.5 py-1 rounded-full uppercase">Average</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 px-2.5 py-1 rounded-full uppercase">Slow</span>
                    @endif
                </div>
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Survey Velocity</h3>
                <div class="text-3xl font-extrabold text-su-black mt-1">{{ $surveyVelocityRate }}%</div>
                <p class="text-xs text-su-black/50 mt-2">Submitted within 24h post event</p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-su-blue/10 text-su-blue rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    @if($avgLift > 3)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 px-2.5 py-1 rounded-full uppercase">High Impact</span>
                    @elseif($avgLift > 0)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint px-2.5 py-1 rounded-full uppercase">Positive</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 px-2.5 py-1 rounded-full uppercase">Needs Review</span>
                    @endif
                </div>
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Avg WHO-5 Lift</h3>
                <div class="text-3xl font-extrabold text-su-blue mt-1">+{{ $avgLift }}</div>
                <p class="text-xs text-su-black/50 mt-2">Pre – Post delta</p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-su-black/10 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-su-gold-tint text-su-gold rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-extrabold text-su-black/70 bg-su-black/5 px-2.5 py-1 rounded-full uppercase">Active</span>
                </div>
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Total Points</h3>
                <div class="text-3xl font-extrabold text-su-black mt-1">{{ number_format($totalPointsEarned) }}</div>
                <p class="text-xs text-su-black/50 mt-2">Earned across all staff</p>
            </div>
        </div>

        <!-- Participation Chart (Horizontally Scrollable on Phone/Half-Screen) & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-extrabold text-su-black">Participation by Wellness Dimension</h2>
                    <span class="sm:hidden text-[10px] font-bold text-slate-400">← Swipe chart →</span>
                </div>
                <div class="overflow-x-auto pb-2">
                    <div class="h-72 w-full min-w-[540px]">
                        <canvas id="categoryChart"
                                data-reg-physical="{{ $regPhysical }}" data-att-physical="{{ $attPhysical }}"
                                data-reg-mental="{{ $regMental }}" data-att-mental="{{ $attMental }}"
                                data-reg-financial="{{ $regFinancial }}" data-att-financial="{{ $attFinancial }}"
                                data-reg-social="{{ $regSocial }}" data-att-social="{{ $attSocial }}"
                                data-reg-intellectual="{{ $regIntellectual }}" data-att-intellectual="{{ $attIntellectual }}"
                                data-reg-occupational="{{ $regOccupational }}" data-att-occupational="{{ $attOccupational }}"
                                data-reg-environmental="{{ $regEnvironmental }}" data-att-environmental="{{ $attEnvironmental }}"
                                data-reg-spiritual="{{ $regSpiritual }}" data-att-spiritual="{{ $attSpiritual }}">
                        </canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6 flex flex-col">
                <h2 class="text-base font-extrabold text-su-black mb-4">Recent Activity</h2>
                <div class="flex-1 overflow-y-auto max-h-80 lg:max-h-none pr-2 space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $activity['marker'] === 'marker-green' ? 'bg-su-blue' : ($activity['marker'] === 'marker-blue' ? 'bg-su-gold' : 'bg-su-red') }}"></div>
                            <div>
                                <div class="text-xs text-su-black/80 leading-snug">{!! $activity['text'] !!}</div>
                                <div class="text-[11px] font-semibold text-su-black/45 mt-1">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No recent activity.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Highly Attended Event & Qualitative Post-Event Feedback -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6 relative overflow-hidden flex flex-col justify-center min-h-[180px]">
                <div class="absolute -right-6 -bottom-6 opacity-[0.04] pointer-events-none">
                    <svg class="w-48 h-48 text-su-blue" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h2 class="text-[11px] font-extrabold text-su-black/60 uppercase tracking-wider mb-4 relative z-10 flex items-center gap-2">
                    <svg class="w-4 h-4 text-su-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Most Highly Attended Event
                </h2>
                <div class="text-lg sm:text-xl font-extrabold text-su-black leading-tight relative z-10">
                    {{ $mostAttendedEvent ? $mostAttendedEvent['name'] : 'No data yet' }}
                </div>
                <div class="mt-4 flex flex-wrap gap-2 relative z-10">
                    @if($mostAttendedEvent)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-su-blue/10 text-su-blue text-xs font-extrabold border border-su-blue/15">
                            {{ $mostAttendedEvent['total'] }} Staff Attended
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-su-gold-tint text-[#8a6514] text-xs font-extrabold">
                            {{ $mostAttendedEvent['registered'] }} Registered
                        </span>
                    @else
                        <span class="text-xs text-su-black/50">—</span>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6">
                <h2 class="text-[11px] font-extrabold text-su-black/60 uppercase tracking-wider mb-4 flex justify-between items-center gap-2">
                    <span>Qualitative Post-Event Feedback</span>
                    <a href="{{ route('hr.feedback.logs') }}" class="text-su-blue hover:underline text-xs font-extrabold shrink-0">View All Logs →</a>
                </h2>
                <div class="space-y-3">
                    @forelse($qualitativeFeedback as $fb)
                        <div class="bg-su-black/[0.02] rounded-xl p-4 border border-su-black/10">
                            <div class="flex justify-between items-start gap-2">
                                <p class="text-xs text-su-black/85 italic leading-relaxed">"{{ $fb['comment'] }}"</p>
                                <span class="text-xs font-extrabold text-su-gold shrink-0">{{ str_repeat('★', $fb['rating'] ?? 5) }}</span>
                            </div>
                            <div class="text-[11px] text-su-black/55 mt-2">— <span class="font-bold text-su-black">{{ $fb['user_name'] }}</span> at {{ $fb['event_name'] }}</div>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No written feedback submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Per-Event Registration Rate, Attendance & Total Points Table (Proportional Sticky First Column) -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-su-black/10 flex flex-wrap justify-between items-center gap-2">
                <h2 class="text-sm sm:text-base font-extrabold text-su-black">Registration Rate, Attendance & Points by Event</h2>
                <div class="flex items-center gap-3">
                    <span class="sm:hidden text-[10px] font-bold text-slate-400">← Swipe →</span>
                    <a href="{{ route('events.management') }}" class="text-xs font-extrabold text-su-blue hover:underline shrink-0">Manage All →</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left border-collapse">
                    <thead>
                        <tr class="bg-su-blue text-white text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                            <th class="sticky left-0 z-20 bg-su-blue px-4 sm:px-6 py-3.5 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)] min-w-[135px] sm:min-w-[190px]">Event Name</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Registered</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Reg. Rate</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Attended</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Total Event Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-su-black/10 text-xs">
                        @forelse($eventsBreakdown as $event)
                            <tr class="group hover:bg-[#f6f8fc] transition-colors">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-[#f6f8fc] px-4 sm:px-6 py-3.5 max-w-[155px] sm:max-w-none border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(2,51,141,0.1)]">
                                    <span class="font-bold text-su-black block truncate sm:whitespace-normal">{{ $event->name }}</span>
                                    <span class="text-[10px] font-extrabold text-su-gold uppercase">{{ $event->category }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-center font-bold text-su-black/80 whitespace-nowrap">{{ $event->total_registered }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-su-blue/10 text-su-blue">
                                        {{ $event->registration_rate }}%
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-center text-su-blue font-extrabold whitespace-nowrap">{{ $event->total_attended }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center font-extrabold text-su-black whitespace-nowrap">{{ number_format((int) ($event->points_transactions_sum_points ?? 0)) }} pts</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-su-black/55">No events found.</td>
                            </tr>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const canvas = document.getElementById('categoryChart');
            const ctx = canvas.getContext('2d');

            const labels = ['Physical', 'Mental', 'Financial', 'Social', 'Intellectual', 'Occupational', 'Environmental', 'Spiritual'];

            const regData = [
                parseInt(canvas.dataset.regPhysical || '0', 10), parseInt(canvas.dataset.regMental || '0', 10),
                parseInt(canvas.dataset.regFinancial || '0', 10), parseInt(canvas.dataset.regSocial || '0', 10),
                parseInt(canvas.dataset.regIntellectual || '0', 10), parseInt(canvas.dataset.regOccupational || '0', 10),
                parseInt(canvas.dataset.regEnvironmental || '0', 10), parseInt(canvas.dataset.regSpiritual || '0', 10)
            ];

            const attData = [
                parseInt(canvas.dataset.attPhysical || '0', 10), parseInt(canvas.dataset.attMental || '0', 10),
                parseInt(canvas.dataset.attFinancial || '0', 10), parseInt(canvas.dataset.attSocial || '0', 10),
                parseInt(canvas.dataset.attIntellectual || '0', 10), parseInt(canvas.dataset.attOccupational || '0', 10),
                parseInt(canvas.dataset.attEnvironmental || '0', 10), parseInt(canvas.dataset.attSpiritual || '0', 10)
            ];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Registered', data: regData, backgroundColor: '#c99a2e', borderRadius: 4, barPercentage: 0.6 },
                        { label: 'Attended', data: attData, backgroundColor: '#02338d', borderRadius: 4, barPercentage: 0.6 }
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
                        tooltip: { backgroundColor: '#1d1d1b', padding: 12 }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#eef0f4', drawBorder: false } },
                        x: { grid: { display: false }, ticks: { maxRotation: 0, minRotation: 0, font: { family: 'Montserrat', size: 11, weight: '600' } } }
                    }
                }
            });
        });
    </script>
</body>
</html>