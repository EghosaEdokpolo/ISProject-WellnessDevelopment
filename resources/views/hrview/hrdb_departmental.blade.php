<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departmental Analysis | Strathmore University</title>
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
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Departmental Engagement</h1>
                    
                    <!-- DYNAMIC DEPARTMENTAL BADGE -->
                    @if($ranking['stats']['org_average'] >= 60)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wider">Highly Engaged</span>
                    @elseif($ranking['stats']['org_average'] >= 30)
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wider">Moderate</span>
                    @else
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full uppercase tracking-wider">Needs Attention</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">Unique attendees per department / total staff in department.</p>
            </div>

            <a href="{{ route('reports.departmental.export') }}" class="bg-wl-gold text-brand px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-yellow-600 transition flex items-center gap-2 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Departmental CSV
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Total Departments</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $ranking['stats']['total_departments'] }}</div>
            </div>
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Org Average</h3>
                <div class="text-3xl font-extrabold text-brand mt-1">{{ $ranking['stats']['org_average'] }}%</div>
            </div>
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Engaged Departments</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $ranking['stats']['engaged_count'] }}</div>
            </div>
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Needs Attention</h3>
                <div class="text-3xl font-extrabold text-red-600 mt-1">{{ $ranking['stats']['disengaged_count'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Most Active</h2>
                <div class="space-y-3">
                    @forelse($ranking['most_active'] as $dept)
                        <div class="flex items-center justify-between bg-emerald-50 rounded-lg px-4 py-3 border border-emerald-100">
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $dept['department'] }}</div>
                                <div class="text-xs text-gray-500">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-bold text-emerald-700 bg-white px-3 py-1 rounded-full">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Needs Attention</h2>
                <div class="space-y-3">
                    @forelse($ranking['needs_attention'] as $dept)
                        <div class="flex items-center justify-between bg-red-50 rounded-lg px-4 py-3 border border-red-100">
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $dept['department'] }}</div>
                                <div class="text-xs text-gray-500">{{ $dept['unique_attendees'] }} of {{ $dept['total_staff'] }} staff</div>
                            </div>
                            <span class="text-xs font-bold text-red-700 bg-white px-3 py-1 rounded-full">{{ $dept['engagement_rate'] }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4 text-center">Staff</th>
                            <th class="px-6 py-4 text-center">Attendees</th>
                            <th class="px-6 py-4 text-center">Engagement</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($ranking['all_departments'] as $dept)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $dept['department'] }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $dept['code'] }}</td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $dept['total_staff'] }}</td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $dept['unique_attendees'] }}</td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900">{{ $dept['engagement_rate'] }}%</td>
                                <td class="px-6 py-4 text-center">
                                    @if($dept['status'] === 'engaged')
                                        <span class="inline-flex px-2 py-1 rounded bg-emerald-50 text-emerald-700 font-bold text-xs capitalize">Engaged</span>
                                    @elseif($dept['status'] === 'developing')
                                        <span class="inline-flex px-2 py-1 rounded bg-amber-50 text-amber-700 font-bold text-xs capitalize">Developing</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 rounded bg-red-50 text-red-700 font-bold text-xs capitalize">Disengaged</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No departmental engagement records found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>