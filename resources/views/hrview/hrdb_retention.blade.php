<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retention Metrics</title>
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
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Retention & Sustained Participation</h1>
                
                <!-- DYNAMIC RETENTION BADGE -->
                @if($retention['sustained_participation'] >= 50)
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wider">Strong</span>
                @elseif($retention['sustained_participation'] >= 25)
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wider">Developing</span>
                @else
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full uppercase tracking-wider">At Risk</span>
                @endif
            </div>
            <p class="text-sm text-gray-500">Staff who attended 2+ wellness events in the current academic year.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Sustained Participation</h3>
                <div class="text-3xl font-extrabold text-brand mt-1">{{ $retention['sustained_participation'] }}%</div>
                <p class="text-xs text-gray-400 mt-2">{{ $retention['sustained_attendees'] }} of {{ $retention['total_active_staff'] }} active staff</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Average Loyalty</h3>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $retention['avg_loyalty'] }}</div>
                <p class="text-xs text-gray-400 mt-2">Events attended per active staff member</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-sm font-medium">Reporting Period</h3>
                <div class="text-sm font-bold text-gray-900 mt-2">{{ $retention['period']['start'] }} → {{ $retention['period']['end'] }}</div>
                <p class="text-xs text-gray-400 mt-2">Current academic year</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Attendance Frequency</h2>
                
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 flex-1">
                    <div class="relative w-64 h-64 shrink-0">
                        <canvas id="distributionChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-4xl font-extrabold text-gray-900">{{ $retention['total_active_staff'] }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Active Staff</span>
                        </div>
                    </div>

                    <div class="w-full space-y-4">
                        @php
                            $total = max(1, $retention['total_active_staff']);
                            $colors = [
                                '0 Events' => 'bg-slate-200',
                                '1 Event' => 'bg-[#c99a2e]',
                                '2 Events' => 'bg-[#02338d]',
                                '3+ Events' => 'bg-emerald-500'
                            ];
                        @endphp
                        
                        @foreach($retention['distribution'] as $label => $count)
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full {{ $colors[$label] ?? 'bg-gray-300' }}"></span>
                                        <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-extrabold text-gray-900">{{ $count }}</span>
                                        <span class="text-xs font-medium text-gray-400 ml-1">({{ round(($count / $total) * 100) }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $colors[$label] ?? 'bg-gray-300' }} h-1.5 rounded-full" style="width: {{ ($count / $total) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Top Attendees</h2>
                <div class="space-y-3">
                    @forelse($retention['top_attendees'] as $index => $attendee)
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3 border border-gray-100 group hover:bg-white hover:shadow-sm transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-light text-brand flex items-center justify-center font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $attendee->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $attendee->department }}</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-brand bg-brand-light px-3 py-1.5 rounded-full">{{ $attendee->attended_count }} events</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No attendance data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

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
                        backgroundColor: ['#e2e8f0', '#c99a2e', '#02338d', '#10b981'], 
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
                            backgroundColor: '#1f2937', 
                            padding: 12, 
                            bodyFont: { family: 'Sansation' },
                            titleFont: { family: 'Sansation', weight: 'bold' }
                        } 
                    }
                }
            });
        });
    </script>
</body>
</html>