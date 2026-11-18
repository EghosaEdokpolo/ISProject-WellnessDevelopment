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
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <x-hr-nav />

    <main class="max-w-7xl w-full mx-auto px-6 py-8 flex-1">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-3xl font-extrabold text-su-black tracking-tight">Departmental Engagement</h1>

                    @if($ranking['stats']['org_average'] >= 60)
                        <span class="text-[10px] font-extrabold text-su-blue bg-su-blue/10 border border-su-blue/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Highly Engaged</span>
                    @elseif($ranking['stats']['org_average'] >= 30)
                        <span class="text-[10px] font-extrabold text-[#8a6514] bg-su-gold-tint border border-su-gold/40 px-2.5 py-1 rounded-full uppercase tracking-wider">Moderate</span>
                    @else
                        <span class="text-[10px] font-extrabold text-su-red bg-su-red/10 border border-su-red/25 px-2.5 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-sm text-su-black/60">Unique attendees per department divided by total staff in department.</p>
            </div>

            <a href="{{ route('reports.departmental.export') }}"
               class="bg-su-gold hover:bg-[#b58924] text-su-black px-5 py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-2 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Departmental CSV
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Total Departments</h3>
                <div class="text-3xl font-extrabold text-su-black mt-2">{{ $ranking['stats']['total_departments'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Org Average</h3>
                <div class="text-3xl font-extrabold text-su-blue mt-2">{{ $ranking['stats']['org_average'] }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Engaged Departments</h3>
                <div class="text-3xl font-extrabold text-su-gold mt-2">{{ $ranking['stats']['engaged_count'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-su-black/10 shadow-sm">
                <h3 class="text-su-black/60 text-xs font-bold uppercase tracking-wider">Needs Attention</h3>
                <div class="text-3xl font-extrabold text-su-red mt-2">{{ $ranking['stats']['disengaged_count'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6">
                <h2 class="text-base font-extrabold text-su-black mb-4">Most Active Departments</h2>
                <div class="space-y-3">
                    @forelse($ranking['most_active'] as $dept)
                        <div class="flex items-center justify-between bg-su-blue/5 rounded-xl px-4 py-3 border border-su-blue/15">
                            <div>
                                <div class="text-xs font-extrabold text-su-black">{{ $dept['department'] }}</div>
                                <div class="text-[11px] text-su-black/60">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-extrabold text-su-blue bg-white border border-su-blue/20 px-3 py-1 rounded-full">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm p-6">
                <h2 class="text-base font-extrabold text-su-black mb-4">Needs Attention</h2>
                <div class="space-y-3">
                    @forelse($ranking['needs_attention'] as $dept)
                        <div class="flex items-center justify-between bg-su-red/5 rounded-xl px-4 py-3 border border-su-red/20">
                            <div>
                                <div class="text-xs font-extrabold text-su-black">{{ $dept['department'] }}</div>
                                <div class="text-[11px] text-su-black/60">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-extrabold text-su-red bg-white border border-su-red/25 px-3 py-1 rounded-full">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-xs text-su-black/55">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-su-black/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-su-blue text-white text-[11px] font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4 text-center">Staff</th>
                            <th class="px-6 py-4 text-center">Attendees</th>
                            <th class="px-6 py-4 text-center">Engagement</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-su-black/10 text-xs">
                        @forelse($ranking['all_departments'] as $dept)
                            <tr class="hover:bg-su-blue/[0.02] transition-colors">
                                <td class="px-6 py-4 font-bold text-su-black">{{ $dept['department'] }}</td>
                                <td class="px-6 py-4 text-su-black/60 font-semibold">{{ $dept['code'] }}</td>
                                <td class="px-6 py-4 text-center text-su-black/80 font-semibold">{{ $dept['total_staff'] }}</td>
                                <td class="px-6 py-4 text-center text-su-black/80 font-semibold">{{ $dept['unique_attendees'] }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-su-black">{{ $dept['engagement_rate'] }}%</td>
                                <td class="px-6 py-4 text-center">
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
</body>
</html>