@extends('layouts.app')

@section('content')
@php
    $effectiveTierPoints = (int) ($tierPoints ?? $me->points_balance);
    $nextThreshold = $nextTier === 'Silver' ? 100 : 200;
    $pointsRemaining = max(0, $nextThreshold - $effectiveTierPoints);

    $rewardsCatalogue = [
        [
            'title'       => 'Extra WFH Day',
            'description' => 'Take an approved remote wellness & focus day.',
            'cost'        => 40,
            'icon'        => '🏖️',
        ],
        [
            'title'       => 'SU Wellness Hoodie',
            'description' => 'Official Strathmore University wellness apparel.',
            'cost'        => 60,
            'icon'        => '👕',
        ],
        [
            'title'       => 'Priority Campus Parking',
            'description' => 'Reserved campus parking bay for 1 full month.',
            'cost'        => 80,
            'icon'        => '🅿️',
        ],
        [
            'title'       => 'Executive Spa Voucher',
            'description' => 'A restorative weekend wellness & spa package.',
            'cost'        => 100,
            'icon'        => '💆',
        ],
    ];
@endphp

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Points, Rewards & Advancement</h1>
                <span class="text-[10px] font-bold text-[#02338d] bg-[#e8f0fe] border border-[#02338d]/20 px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    Leading Indicator
                </span>
            </div>
            <p class="text-sm text-slate-500">
                Earn wellness points across all 8 dimensions to redeem rewards and strengthen your People & Culture holistic appraisal profile.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold border border-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT COLUMN: Balance, Performance Advancement Pathway, Ledger & Catalogue -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Hero Points Card -->
            <div class="bg-gradient-to-br from-[#02338d] via-[#0346c2] to-[#01153d] rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#c99a2e] rounded-full mix-blend-multiply filter blur-3xl opacity-15 transform -translate-x-1/2 translate-y-1/2"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div>
                        <p class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#c99a2e]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Redeemable Points Balance
                        </p>
                        <div class="flex items-baseline gap-3">
                            <span class="text-7xl md:text-8xl font-black text-white drop-shadow-lg tracking-tighter">{{ $me->points_balance }}</span>
                            <span class="text-xl font-medium text-blue-300">pts</span>
                        </div>
                        @if($effectiveTierPoints > (int) $me->points_balance)
                            <p class="text-xs text-blue-200 mt-2">
                                Cumulative Appraisal Standing: <strong class="text-white">{{ $effectiveTierPoints }} pts earned</strong>
                            </p>
                        @endif
                    </div>
                    
                    <!-- Tier Progress Card -->
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-5 border border-white/20 min-w-[250px] shadow-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-100">Current Tier</span>
                            <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider border {{ $tierColor }}">
                                {{ $tierIcon }} {{ $tier }}
                            </span>
                        </div>
                        @if($nextTier !== 'Max Level')
                            <div class="w-full bg-black/40 rounded-full h-3 mb-2 shadow-inner overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-400 to-[#c99a2e] h-3 rounded-full shadow-[0_0_10px_rgba(251,191,36,0.8)]" style="width: {{ min(100, $progress) }}%"></div>
                            </div>
                            <p class="text-[11px] text-blue-100 text-right font-medium">
                                Earn <strong class="text-white">{{ $pointsRemaining }} pts</strong> to reach {{ $nextTier }} ({{ $nextThreshold }} pts)
                            </p>
                        @else
                            <p class="text-xs text-[#c99a2e] font-bold text-center mt-2">Highest Wellness & Appraisal Tier Unlocked!</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Link to Strathmore Staff Performance Advancement & Appraisal Recognition -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-[#02338d] to-[#01225e] px-6 py-4 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 text-white">
                        <svg class="w-5 h-5 text-[#c99a2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        <h2 class="text-sm font-bold uppercase tracking-wider">Performance Advancement & Appraisal Recognition</h2>
                    </div>
                    <span class="text-[11px] font-bold bg-[#c99a2e] text-[#1a1c23] px-3 py-0.5 rounded-full">
                        Your Standing: {{ $tier }} Tier
                    </span>
                </div>

                <div class="p-6">
                    <p class="text-sm text-slate-600 leading-relaxed mb-5">
                        At Strathmore University, holistic staff wellbeing directly supports sustained workplace excellence. Your verified wellness participation tier is recorded as supporting evidence under the <strong>Holistic Staff Development & Institutional Engagement</strong> pillar during annual People & Culture performance appraisals and promotion reviews.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl border {{ $tier === 'Bronze' ? 'border-[#02338d] bg-[#e8f0fe]/50' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-extrabold text-orange-800">🔥 Bronze (0–99 pts)</span>
                                @if($tier === 'Bronze')
                                    <span class="text-[10px] font-bold text-[#02338d] uppercase">Active</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-snug">
                                Baseline wellness onboarding; counts toward annual <strong>Personal Growth & Wellbeing</strong> appraisal check-in.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl border {{ $tier === 'Silver' ? 'border-[#02338d] bg-[#e8f0fe]/50' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-extrabold text-slate-800">⭐ Silver (100–199 pts)</span>
                                @if($tier === 'Silver')
                                    <span class="text-[10px] font-bold text-[#02338d] uppercase">Active</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-snug">
                                Eligible for <strong>Continuous Professional Development (CPD)</strong> wellness credit and departmental merit commendation.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl border {{ $tier === 'Gold' ? 'border-[#c99a2e] bg-amber-50/70' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-extrabold text-amber-800">👑 Gold (200+ pts)</span>
                                @if($tier === 'Gold')
                                    <span class="text-[10px] font-bold text-amber-800 uppercase">Active</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-snug">
                                Recognised as a <strong>Strathmore Wellness Ambassador</strong> in annual appraisal dossiers, leadership readiness reviews, and Vice-Chancellor Staff Awards.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Ledger -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Recent Activity</h2>
                    <span class="text-xs text-slate-400 font-medium">Last 10 transactions</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    @forelse($me->pointsTransactions()->latest()->take(10)->get() as $tx)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border
                                    {{ $tx->points > 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    @if($tx->reason === 'waiver_signed')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($tx->reason === 'attended_event')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @elseif($tx->reason === 'feedback_submitted')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $tx->reason) }}</p>
                                    <p class="text-xs text-slate-500">{{ $tx->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="font-black text-lg {{ $tx->points > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Rewards Catalogue (Live Database Redemption) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Rewards Catalogue</h2>
                    <span class="text-xs text-slate-400">Redemptions do not reduce your Appraisal Tier standing</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($rewardsCatalogue as $reward)
                        @php $canRedeem = (int) $me->points_balance >= $reward['cost']; @endphp
                        <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between hover:border-[#02338d] hover:shadow-md transition-all group bg-white">
                            <div>
                                <div class="w-14 h-14 bg-[#e8f0fe] text-[#02338d] rounded-2xl flex items-center justify-center text-3xl mb-4 group-hover:scale-105 transition-transform">
                                    {{ $reward['icon'] }}
                                </div>
                                <h3 class="font-bold text-slate-900">{{ $reward['title'] }}</h3>
                                <p class="text-xs text-slate-500 mt-1 mb-4">{{ $reward['description'] }}</p>
                            </div>
                            @if($canRedeem)
                                <form method="POST" action="{{ route('rewards.redeem') }}" onsubmit="return confirm('Redeem {{ addslashes($reward['title']) }} for {{ $reward['cost'] }} pts?');">
                                    @csrf
                                    <input type="hidden" name="reward_title" value="{{ $reward['title'] }}">
                                    <input type="hidden" name="cost" value="{{ $reward['cost'] }}">
                                    <button type="submit" class="w-full bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-2.5 rounded-xl text-sm transition-colors flex justify-between px-4 items-center shadow-sm">
                                        <span>Redeem Reward</span> <span>{{ $reward['cost'] }} pts</span>
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled
                                        class="w-full bg-slate-100 text-slate-400 font-bold py-2.5 rounded-xl text-sm cursor-not-allowed flex justify-between px-4 items-center">
                                    <span>Need {{ $reward['cost'] - (int) $me->points_balance }} more pts</span> <span>{{ $reward['cost'] }} pts</span>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Departmental Leaderboard -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="bg-gradient-to-r from-[#c99a2e] to-yellow-600 px-6 py-6 text-center relative overflow-hidden">
                    <h2 class="text-lg font-bold text-white relative z-10 flex items-center justify-center gap-2 drop-shadow-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a1 1 0 000-2H8.414l1.293-1.293z" clip-rule="evenodd"></path></svg>
                        Department Leaderboard
                    </h2>
                    <p class="text-xs text-yellow-50 mt-1 font-semibold relative z-10">{{ $me->department }}</p>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @foreach($leaderboard as $index => $person)
                        <div class="flex items-center justify-between p-4 transition-colors
                            {{ $person->id === $me->id ? 'bg-[#e8f0fe]/70 border-l-4 border-[#02338d]' : 
                               ($index === 0 ? 'bg-amber-50/50' : ($index === 1 ? 'bg-slate-50' : ($index === 2 ? 'bg-orange-50/50' : 'hover:bg-slate-50'))) }}">
                            
                            <div class="flex items-center gap-3">
                                <div class="w-8 flex justify-center font-black">
                                    @if($index === 0) <span class="text-2xl drop-shadow-sm">🥇</span>
                                    @elseif($index === 1) <span class="text-2xl drop-shadow-sm">🥈</span>
                                    @elseif($index === 2) <span class="text-2xl drop-shadow-sm">🥉</span>
                                    @else <span class="text-slate-400 text-sm">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0 shadow-sm text-white
                                    {{ $index === 0 ? 'bg-amber-500' : ($index === 1 ? 'bg-slate-400' : ($index === 2 ? 'bg-orange-400' : 'bg-[#02338d]')) }}">
                                    {{ substr($person->name, 0, 1) }}
                                </div>
                                
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $person->name }}</div>
                                    @if($person->id === $me->id)
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-1.5 py-0.5 rounded">You</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="font-black text-[#02338d] text-sm bg-white/70 px-2.5 py-1 rounded-md">
                                {{ $person->points_balance }} <span class="text-[10px] text-slate-400 font-bold">pts</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection