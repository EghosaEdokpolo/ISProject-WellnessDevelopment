<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Overview Dashboard | Strathmore University</title>
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
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">HR Overview</h1>
                <p class="text-sm text-gray-500 mt-1">Live analytics for Semester 1, {{ date('Y') }}</p>
            </div>
            <div class="text-xs font-bold text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Data
            </div>
        </div>

        <!-- 4 Executive KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- CARD 1: Staff Registration Rate -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-brand-light text-brand rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    @if($registrationRate >= 75)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">On Track</span>
                    @elseif($registrationRate >= 40)
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase">Moderate</span>
                    @else
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full uppercase">Low</span>
                    @endif
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Registration Rate</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $registrationRate }}%</div>
                <p class="text-xs text-gray-400 mt-2">{{ $registeredStaff }} of {{ $totalStaff }} staff</p>
            </div>

            <!-- CARD 2: Survey Velocity -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    @if($surveyVelocityRate >= 80)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">Fast</span>
                    @elseif($surveyVelocityRate >= 50)
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase">Average</span>
                    @else
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full uppercase">Slow</span>
                    @endif
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Survey Velocity</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $surveyVelocityRate }}%</div>
                <p class="text-xs text-gray-400 mt-2">Submitted within 24h post event</p>
            </div>

            <!-- CARD 3: Avg WHO-5 Lift -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    @if($avgLift > 3)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase">High Impact</span>
                    @elseif($avgLift > 0)
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full uppercase">Positive</span>
                    @else
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full uppercase">Needs Review</span>
                    @endif
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Avg WHO-5 Lift</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">+{{ $avgLift }}</div>
                <p class="text-xs text-gray-400 mt-2">Pre – Post delta</p>
            </div>

            <!-- CARD 4: Total Points -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-amber-50 text-wl-gold rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-full uppercase">Active</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Points</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalPointsEarned) }}</div>
                <p class="text-xs text-gray-400 mt-2">Earned across all staff</p>
            </div>
        </div>

        <!-- Participation Chart & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Participation by Category</h2>
                </div>
                <div class="h-72 w-full">
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

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h2>
                <div class="flex-1 overflow-y-auto pr-2 space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $activity['marker'] === 'marker-green' ? 'bg-emerald-500' : ($activity['marker'] === 'marker-blue' ? 'bg-brand' : 'bg-amber-500') }}"></div>
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

        <!-- Most Highly Attended Event & Qualitative Post-Event Feedback -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 relative overflow-hidden flex flex-col justify-center min-h-[200px]">
                <div class="absolute -right-6 -bottom-6 opacity-[0.04] pointer-events-none">
                    <svg class="w-48 h-48 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 relative z-10 flex items-center gap-2">
                    <svg class="w-4 h-4 text-wl-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Most Highly Attended Event
                </h2>
                <div class="text-2xl font-extrabold text-gray-900 leading-tight relative z-10">
                    {{ $mostAttendedEvent ? $mostAttendedEvent['name'] : 'No data yet' }}
                </div>
                <div class="mt-4 flex flex-wrap gap-2 relative z-10">
                    @if($mostAttendedEvent)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-light text-brand text-xs font-bold border border-brand/10">
                            {{ $mostAttendedEvent['total'] }} Staff Attended
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                            {{ $mostAttendedEvent['registered'] }} Registered
                        </span>
                    @else
                        <span class="text-sm text-gray-500">—</span>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 flex justify-between items-center">
                    <span>Qualitative Post-Event Feedback</span>
                    <a href="{{ route('hr.feedback.logs') }}" class="text-brand hover:underline text-xs">View All Logs →</a>
                </h2>
                <div class="space-y-3">
                    @forelse($qualitativeFeedback as $fb)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex justify-between items-start gap-2">
                                <p class="text-sm text-gray-700 italic">"{{ $fb['comment'] }}"</p>
                                <span class="text-xs font-bold text-wl-gold shrink-0">{{ str_repeat('★', $fb['rating'] ?? 5) }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-2">— <span class="font-semibold text-gray-600">{{ $fb['user_name'] }}</span> at {{ $fb['event_name'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No written feedback submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Per-Event Registration Rate, Attendance & Total Points Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Registration Rate, Attendance & Points by Event</h2>
                <a href="{{ route('events.management') }}" class="text-xs font-bold text-brand hover:underline">Manage All Events →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Event Name</th>
                            <th class="px-6 py-4 text-center">Registered</th>
                            <th class="px-6 py-4 text-center">Reg. Rate</th>
                            <th class="px-6 py-4 text-center">Attended</th>
                            <th class="px-6 py-4 text-center">Total Event Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($eventsBreakdown as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900 block">{{ $event->name }}</span>
                                    <span class="text-[11px] font-semibold text-gray-400 uppercase">{{ $event->category }}</span>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $event->total_registered }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-light text-brand">
                                        {{ $event->registration_rate }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-emerald-600 font-bold">{{ $event->total_attended }}</td>
                                <td class="px-6 py-4 text-center font-bold text-brand">{{ number_format((int) ($event->points_transactions_sum_points ?? 0)) }} pts</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                        { label: 'Registered', data: regData, backgroundColor: '#cbd5e1', borderRadius: 4, barPercentage: 0.6 },
                        { label: 'Attended', data: attData, backgroundColor: '#02338d', borderRadius: 4, barPercentage: 0.6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { font: { family: 'Sansation', weight: '600' }, usePointStyle: true, boxWidth: 8 } },
                        tooltip: { backgroundColor: '#01225e', padding: 12 }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6', drawBorder: false } },
                        x: { grid: { display: false }, ticks: { font: { family: 'Sansation', weight: '600' } } }
                    }
                }
            });
        });
    </script>
</body>
</html>