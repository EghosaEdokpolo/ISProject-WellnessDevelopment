<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departmental Engagement Growth</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Departmental Engagement</h1>
                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full uppercase tracking-wider">Lagging</span>
                </div>
                <p class="text-sm text-gray-500">Which departments moved from disengaged to engaged across the semester.</p>
                <p class="text-xs font-semibold text-gray-400 mt-2">Academic Year: {{ $ranking['period']['start'] }} to {{ $ranking['period']['end'] }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('reports.departmental.export') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </a>
                <a href="{{ route('reports.pdf.export') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-blue-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Download PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Departments Tracked</h3>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $ranking['stats']['total_departments'] }}</div>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Org-Wide Average</h3>
                <div class="text-3xl font-extrabold text-brand mt-1">{{ $ranking['stats']['org_average'] }}%</div>
                <p class="text-xs text-gray-400 mt-2">Average engagement rate</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Engaged Depts</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $ranking['stats']['engaged_count'] }}</div>
                <p class="text-xs text-gray-400 mt-2">Rate > 60%</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Disengaged Depts</h3>
                <div class="text-3xl font-extrabold text-red-600 mt-1">{{ $ranking['stats']['disengaged_count'] }}</div>
                <p class="text-xs text-gray-400 mt-2">Rate < 30%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Engagement by Department</h2>
                </div>
                <div class="h-80 w-full"><canvas id="deptChart"></canvas></div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-wl-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Most Active
                    </h2>
                    @foreach($ranking['most_active'] as $i => $d)
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700 truncate">{{ $i+1 }}. {{ $d['department'] }}</span>
                            <span class="text-sm font-bold text-emerald-600">{{ $d['engagement_rate'] }}%</span>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Needs Attention
                    </h2>
                    @foreach($ranking['needs_attention'] as $i => $d)
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700 truncate">{{ $i+1 }}. {{ $d['department'] }}</span>
                            <span class="text-sm font-bold text-red-600">{{ $d['engagement_rate'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Rank</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4 text-center">Staff</th>
                        <th class="px-6 py-4 text-center">Attendees</th>
                        <th class="px-6 py-4 text-center">Rate</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach($ranking['all_departments'] as $i => $d)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-400">{{ $i+1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $d['department'] }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $d['code'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $d['total_staff'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $d['unique_attendees'] }}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-brand">{{ $d['engagement_rate'] }}%</td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($d['status']) {
                                        'engaged' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'developing' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'disengaged' => 'bg-red-50 text-red-700 border-red-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">{{ $d['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('deptChart').getContext('2d');
            const labels = {!! json_encode(array_column($ranking['all_departments'], 'code')) !!};
            const data = {!! json_encode(array_column($ranking['all_departments'], 'engagement_rate')) !!};
            const backgroundColors = data.map(rate => {
                if (rate >= 60) return 'rgba(16, 185, 129, 0.8)';
                if (rate >= 30) return 'rgba(245, 158, 11, 0.8)';
                return 'rgba(239, 68, 68, 0.8)';
            });

            new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Engagement Rate (%)', data: data, backgroundColor: backgroundColors, borderRadius: 4, barPercentage: 0.6 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, ticks: { callback: function(value) { return value + "%" } }, grid: { color: '#f3f4f6', drawBorder: false } }, x: { grid: { display: false } } } }
            });
        });
    </script>
</body>
</html>