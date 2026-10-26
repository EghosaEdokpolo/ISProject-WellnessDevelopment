@extends('layouts.app')

@section('content')
    <div class="mb-8 flex items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Points &amp; Leaderboard</h1>
            <p class="text-sm text-gray-500 mt-1">UC-13 Incentive System · UC-14 Leaderboard. Points reset annually — controlled by HR.</p>
        </div>
        <span class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider mb-4">Leading</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        
        <!-- Left Column: Points & History -->
        <div class="md:col-span-3 space-y-6">
            <!-- Balance Card -->
            <div class="bg-gradient-to-br from-wl-indigo to-indigo-800 rounded-xl p-8 text-white shadow-md">
                <p class="text-[10px] font-bold text-wl-gold uppercase tracking-wider mb-2">Your Balance</p>
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="text-6xl font-extrabold">{{ $me->points_balance }}</span>
                </div>
                <p class="text-xs text-indigo-200">wellness points · Semester 1, 2026</p>
            </div>

            <!-- Transaction Ledger -->
            <div class="bg-wl-indigo rounded-xl shadow-sm overflow-hidden text-white">
                <div class="p-4 bg-indigo-800/50 border-b border-indigo-400/30 text-xs font-bold uppercase tracking-wider">Recent Activity</div>
                <div class="divide-y divide-indigo-400/20">
                    @forelse($me->pointsTransactions()->latest()->take(5)->get() as $tx)
                        <div class="flex justify-between items-center p-4 hover:bg-white/5">
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-2 h-2 rounded-full {{ $tx->points > 0 ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                                <span class="capitalize">{{ str_replace('_', ' ', $tx->reason) }}</span>
                            </div>
                            <span class="font-bold text-emerald-300 text-sm">+{{ $tx->points }}</span>
                        </div>
                    @empty
                        <div class="p-4 text-sm text-indigo-200">No activity yet.</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Rewards Catalogue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rewards Catalogue</div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                        <span class="flex items-center gap-2">🏖️ Extra WFH day</span>
                        <span class="font-bold text-wl-indigo">40 pts</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                        <span class="flex items-center gap-2">👕 SU Wellness Hoodie</span>
                        <span class="font-bold text-wl-indigo">60 pts</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                        <span class="flex items-center gap-2">💆 Spa voucher</span>
                        <span class="font-bold text-wl-indigo">100 pts</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center gap-2">🅿️ Priority parking (1 month)</span>
                        <span class="font-bold text-wl-indigo">80 pts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Leaderboard -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-full">
                <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2 mb-6">
                    <svg class="w-4 h-4 text-wl-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a1 1 0 000-2H8.414l1.293-1.293z" clip-rule="evenodd"></path></svg>
                    Department Leaderboard
                </h2>
                
                <div class="space-y-2">
                    @foreach($leaderboard as $index => $person)
                        <div class="flex items-center justify-between p-3 rounded-lg border {{ $person->id === $me->id ? 'bg-wl-cream border-wl-gold/30' : 'bg-gray-50 border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-5 text-center font-bold text-xs {{ $index === 0 ? 'text-wl-gold text-sm' : 'text-gray-400' }}">{{ $index + 1 }}</span>
                                <span class="font-semibold text-gray-800 text-sm">{{ $person->name }}</span>
                            </div>
                            <span class="font-bold text-wl-indigo text-sm">{{ $person->points_balance }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection