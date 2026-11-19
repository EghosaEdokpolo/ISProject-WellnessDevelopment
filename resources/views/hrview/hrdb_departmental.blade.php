<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departmental Engagement | Strathmore University WPMIS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
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
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-su-black tracking-tight">Departmental Engagement</h1>

                    @if($ranking['stats']['org_average'] >= 60)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Highly Engaged</span>
                    @elseif($ranking['stats']['org_average'] >= 30)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Moderate</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm text-su-black/60">Unique attendees per department divided by total staff in department.</p>
            </div>

            <a href="{{ route('reports.departmental.export') }}"
               class="w-full sm:w-auto justify-center bg-su-gold hover:bg-[#b58924] text-su-black px-5 py-3 sm:py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-2 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Departmental CSV
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-[11px] sm:text-xs font-bold uppercase tracking-wider">Total Departments</h3>
                <div class="text-2xl sm:text-3xl font-extrabold text-su-black mt-2">{{ $ranking['stats']['total_departments'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-[11px] sm:text-xs font-bold uppercase tracking-wider">Org Average</h3>
                <div class="text-2xl sm:text-3xl font-extrabold text-su-blue mt-2">{{ $ranking['stats']['org_average'] }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-[11px] sm:text-xs font-bold uppercase tracking-wider">Engaged Depts</h3>
                <div class="text-2xl sm:text-3xl font-extrabold text-su-gold mt-2">{{ $ranking['stats']['engaged_count'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-[11px] sm:text-xs font-bold uppercase tracking-wider">Needs Attention</h3>
                <div class="text-2xl sm:text-3xl font-extrabold text-su-red mt-2">{{ $ranking['stats']['disengaged_count'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6">
                <h2 class="text-base font-extrabold text-su-black mb-4">Most Active Departments</h2>
                <div class="space-y-3">
                    @forelse($ranking['most_active'] as $dept)
                        <div class="flex items-center justify-between gap-3 bg-su-blue/5 rounded-xl px-4 py-3 border border-su-blue/15">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-su-black truncate">{{ $dept['department'] }}</div>
                                <div class="text-[11px] text-su-black/60">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-extrabold text-su-blue bg-white border border-su-blue/20 px-3 py-1 rounded-full shrink-0">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-5 sm:p-6">
                <h2 class="text-base font-extrabold text-su-black mb-4">Needs Attention</h2>
                <div class="space-y-3">
                    @forelse($ranking['needs_attention'] as $dept)
                        <div class="flex items-center justify-between gap-3 bg-su-red/5 rounded-xl px-4 py-3 border border-su-red/20">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-su-black truncate">{{ $dept['department'] }}</div>
                                <div class="text-[11px] text-su-black/60">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-extrabold text-su-red bg-white border border-su-red/25 px-3 py-1 rounded-full shrink-0">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Mobile-Scrollable Departmental Table (Proportional Sticky First Column) -->
        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm overflow-hidden">
            <div class="sm:hidden px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 flex items-center justify-between">
                <span>All Departments</span>
                <span>← Swipe table horizontally →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left border-collapse">
                    <thead>
                        <tr class="bg-su-blue text-white text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                            <th class="sticky left-0 z-20 bg-su-blue px-4 sm:px-6 py-3.5 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)] min-w-[135px] sm:min-w-[190px]">Department</th>
                            <th class="px-4 sm:px-6 py-3.5">Code</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Staff</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Attendees</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Engagement</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-su-black/10 text-xs">
                        @forelse($ranking['all_departments'] as $dept)
                            <tr class="group hover:bg-[#f6f8fc] transition-colors">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-[#f6f8fc] px-4 sm:px-6 py-3.5 font-bold text-su-black max-w-[155px] sm:max-w-none truncate sm:whitespace-nowrap border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(2,51,141,0.1)]" title="{{ $dept['department'] }}">
                                    {{ $dept['department'] }}
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-su-black/60 font-semibold whitespace-nowrap">{{ $dept['code'] }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center text-su-black/80 font-semibold whitespace-nowrap">{{ $dept['total_staff'] }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center text-su-black/80 font-semibold whitespace-nowrap">{{ $dept['unique_attendees'] }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center font-extrabold text-su-black whitespace-nowrap">{{ $dept['engagement_rate'] }}%</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center whitespace-nowrap">
                                    @if($dept['status'] === 'engaged')
                                        <span class="inline-flex px-2.5 py-1 rounded-md bg-su-blue/10 text-su-blue border border-su-blue/20 font-extrabold text-[10px] uppercase tracking-wider">Engaged</span>
                                    @elseif($dept['status'] === 'developing')
                                        <span class="inline-flex px-2.5 py-1 rounded-md bg-su-gold-tint text-[#8a6514] border border-su-gold/40 font-extrabold text-[10px] uppercase tracking-wider">Developing</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 rounded-md bg-su-red/10 text-su-red border border-su-red/25 font-extrabold text-[10px] uppercase tracking-wider">Disengaged</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-xs text-su-black/55">
                                    No departmental engagement records found yet.
                                </td>
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
</body>
</html>