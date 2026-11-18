@extends('layouts.app')

@section('content')
@php
    $catalog = [
        [
            'title'       => 'Strathmore Cafeteria Meal Voucher',
            'description' => 'Complimentary healthy lunch & smoothie voucher redeemable at the Student Centre or STC Cafeteria.',
            'cost'        => 50,
            'category'    => 'Nutrition & Dining',
        ],
        [
            'title'       => 'University Branded Wellness Hoodie',
            'description' => 'Official Strathmore University WellLoop embroidered fleece hoodie from the University Bookshop.',
            'cost'        => 120,
            'category'    => 'Campus Merchandise',
        ],
        [
            'title'       => 'Half-Day Wellness Flex Leave Pass',
            'description' => 'HR-approved afternoon wellness time-off pass (coordinated with your Head of Department).',
            'cost'        => 200,
            'category'    => 'Work-Life Balance',
        ],
        [
            'title'       => 'Sports Complex Fitness & Gym Pass',
            'description' => '1-month priority wellness access to Strathmore Sports Complex sessions and fitness coaching.',
            'cost'        => 80,
            'category'    => 'Physical Wellness',
        ],
    ];

    $activeDeptLabel = ($selectedDept ?? $me->department) === 'all'
        ? 'All Strathmore University Staff'
        : ($selectedDept ?? $me->department);
@endphp

<div class="max-w-7xl mx-auto space-y-8">

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-extrabold border border-rose-200 shadow-sm">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    <!-- Top Summary Banner -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-[#02338d] via-[#02338d] to-[#c99a2e]"></div>
        <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                    Strathmore Staff Wellness Recognition
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Wellness Rewards & Standing
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Earn points by registering (+5), checking in at the venue (+25), and submitting Post-Event WHO-5 reflections (+10).
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="bg-[#02338d] text-white rounded-2xl px-6 py-4 text-center shadow-sm">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-blue-200">Available Balance</span>
                    <span class="text-2xl font-extrabold text-[#c99a2e]">{{ number_format((int) $me->points_balance) }} pts</span>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">{{ $tierIcon }}</span>
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Appraisal Tier</span>
                            <span class="text-sm font-extrabold text-slate-900">{{ $tier }} Standing ({{ $tierPoints }} lifetime pts)</span>
                        </div>
                    </div>
                    <div class="w-44 bg-slate-200 h-1.5 rounded-full overflow-hidden mt-2">
                        <div class="bg-[#c99a2e] h-full rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Rewards Catalog + Department Leaderboard & Live Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left 7 Cols: Redeemable Rewards Catalog & Department Leaderboard -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7">
                <h2 class="text-lg font-extrabold text-slate-900 mb-1">Redeemable Campus Rewards</h2>
                <p class="text-xs text-slate-500 mb-6">
                    Redeeming a reward deducts from your spendable balance without lowering your annual Appraisal Tier.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($catalog as $item)
                        @php $canAfford = (int) $me->points_balance >= $item['cost']; @endphp
                        <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between bg-slate-50/50">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-2.5 py-0.5 rounded-full">
                                        {{ $item['category'] }}
                                    </span>
                                    <span class="text-xs font-extrabold text-[#8a6514] bg-[#f5eccd] px-2.5 py-0.5 rounded-full">
                                        {{ $item['cost'] }} pts
                                    </span>
                                </div>
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $item['title'] }}</h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item['description'] }}</p>
                            </div>

                            <form method="POST" action="{{ route('rewards.redeem') }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="reward_title" value="{{ $item['title'] }}">
                                <input type="hidden" name="cost" value="{{ $item['cost'] }}">
                                <button type="submit"
                                        @disabled(! $canAfford)
                                        class="w-full py-2.5 px-4 rounded-xl text-xs font-extrabold transition {{ $canAfford ? 'bg-[#02338d] hover:bg-[#01225e] text-white shadow-sm' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                                    {{ $canAfford ? 'Redeem Reward →' : 'Need ' . ($item['cost'] - (int) $me->points_balance) . ' more pts' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Department Leaderboard (Matches Photos 2 & 3 with Department Selector) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-[#c99a2e] text-white px-6 py-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <h2 class="text-lg font-extrabold tracking-tight">Department Leaderboard</h2>
                    </div>

                    <p class="text-xs font-bold text-amber-50 mt-0.5">
                        {{ $activeDeptLabel }}
                    </p>
                    <p class="text-[10px] text-amber-100/90 mt-1">
                        Logged in as: <strong>{{ $me->name }}</strong> ({{ $me->department }})
                    </p>

                    @if(!empty($allDepartments))
                        <form method="GET" action="{{ route('rewards.index') }}" class="mt-3 max-w-xs mx-auto">
                            <select name="dept"
                                    onchange="this.form.submit()"
                                    class="w-full bg-white/95 text-[#1d1d1b] text-xs font-extrabold rounded-xl px-3 py-1.5 border-0 shadow-sm focus:ring-2 focus:ring-[#02338d]">
                                <option value="{{ $me->department }}" @selected(($selectedDept ?? $me->department) === $me->department)>
                                    My Department ({{ $me->department }})
                                </option>
                                <option value="all" @selected(($selectedDept ?? '') === 'all')>
                                    All University Departments
                                </option>
                                @foreach($allDepartments as $deptName)
                                    @if($deptName !== $me->department)
                                        <option value="{{ $deptName }}" @selected(($selectedDept ?? '') === $deptName)>
                                            {{ $deptName }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($leaderboard as $idx => $colleague)
                        @php
                            $initial = strtoupper(mb_substr(trim($colleague->name), 0, 1));
                            $isMe    = $colleague->id === $me->id;
                            $avatarBg = match ($idx) {
                                0 => 'bg-amber-500 text-white',
                                1 => 'bg-slate-400 text-white',
                                2 => 'bg-amber-600 text-white',
                                default => 'bg-[#02338d] text-white',
                            };
                        @endphp
                        <div class="px-6 py-4 flex items-center justify-between text-xs {{ $isMe ? 'bg-[#e8f0fe]/70' : 'bg-white' }}">
                            <div class="flex items-center gap-3.5">
                                <span class="w-5 text-center font-extrabold text-slate-400">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="w-9 h-9 rounded-full {{ $avatarBg }} font-extrabold flex items-center justify-center text-xs shrink-0">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 block">{{ $colleague->name }}</span>
                                    @if($isMe)
                                        <span class="inline-block text-[9px] font-extrabold uppercase bg-blue-100 text-[#02338d] px-2 py-0.5 rounded mt-0.5">
                                            YOU
                                        </span>
                                    @elseif(($selectedDept ?? '') === 'all')
                                        <span class="text-[10px] text-slate-400">{{ $colleague->department }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="font-extrabold text-[#02338d] bg-slate-50 border border-slate-200/80 px-3 py-1 rounded-lg">
                                {{ number_format((int) $colleague->points_balance) }} <span class="font-normal text-slate-400">pts</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right 5 Cols: Live & Accurate Recent Activity -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Recent Activity</h2>
                <span class="text-[11px] font-semibold text-slate-400">Last {{ $recentTransactions->count() }} transactions</span>
            </div>

            @if($recentTransactions->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-xs font-bold text-slate-600">No point transactions yet.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Register and check in at a wellness session to earn points.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                    @foreach($recentTransactions as $item)
                        @php
                            $pts        = $item['points'];
                            $isPositive = $pts >= 0;
                        @endphp
                        <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                            <div class="flex items-start gap-3.5 min-w-0">
                                <div class="w-10 h-10 rounded-2xl {{ $isPositive ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/70' : 'bg-amber-50 text-amber-700 border border-amber-200/70' }} flex items-center justify-center shrink-0">
                                    @if($item['reason'] === 'attended_event')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    @elseif($item['reason'] === 'feedback_submitted')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif(! $isPositive)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-900 truncate">
                                        {{ $item['title'] }}
                                    </p>
                                    @if(!empty($item['event_name']))
                                        <p class="text-[11px] font-bold text-[#02338d] truncate mt-0.5">
                                            {{ $item['event_name'] }}
                                        </p>
                                    @endif
                                    <p class="text-[11px] text-slate-400 font-semibold mt-0.5">
                                        {{ $item['date_str'] }} EAT
                                    </p>
                                </div>
                            </div>

                            <span class="text-sm font-extrabold {{ $isPositive ? 'text-emerald-600' : 'text-rose-600' }} shrink-0">
                                {{ $isPositive ? '+' . $pts : $pts }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection