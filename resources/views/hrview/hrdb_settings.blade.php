@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-4">

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-extrabold border border-rose-200 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Page Header (Matches Photo 1) -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1d1d1b] tracking-tight">HR Settings & Compliance</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            UC-15 · Points management, data protection, accessibility controls
        </p>
    </div>

    <!-- Two-Column Cards Layout (Matches Photo 1) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

        <!-- Left Card: Points Reset Control -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <svg class="w-5 h-5 text-[#02338d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <h2 class="text-base font-extrabold text-[#1d1d1b]">Points Reset Control</h2>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed mb-5">
                    Points reset annually, controlled by HR only. This action is irreversible — all staff balances will be zeroed.
                </p>

                <div class="bg-[#f3f3ef] rounded-xl p-4 space-y-1.5 text-xs text-slate-700 mb-6">
                    <div>
                        <span class="font-bold text-slate-700">Current cycle:</span>
                        <span class="text-slate-600">{{ $points['current_cycle_label'] ?? ('Semester 1, ' . date('Y')) }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-700">Next scheduled reset:</span>
                        <span class="text-slate-600">{{ $points['next_reset'] ?? ('31 Jan ' . ((int) date('Y') + 1)) }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-700">Total points in circulation:</span>
                        <span class="font-extrabold text-[#1d1d1b]">{{ number_format((int) ($points['total_active_points'] ?? 0)) }}</span>
                    </div>
                </div>
            </div>

            <div>
                <button type="button"
                        onclick="document.getElementById('reset-points-modal').classList.remove('hidden')"
                        class="w-full py-3.5 px-5 rounded-xl border border-red-400 bg-red-50/50 hover:bg-red-100/70 text-[#e03c31] font-bold text-xs transition text-center">
                    Reset all staff points
                </button>
            </div>
        </div>

        <!-- Right Card: Data Protection Panel -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-5">
                    <svg class="w-5 h-5 text-[#02338d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <h2 class="text-base font-extrabold text-[#1d1d1b]">Data Protection Panel</h2>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 items-baseline">
                        <span class="sm:col-span-5 text-slate-500 font-medium">Data Controller</span>
                        <span class="sm:col-span-7 text-[#1d1d1b] font-semibold">Strathmore University, Madaraka Estate, Nairobi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 items-baseline">
                        <span class="sm:col-span-5 text-slate-500 font-medium">Applicable Law</span>
                        <span class="sm:col-span-7 text-[#1d1d1b] font-semibold">Kenya Data Protection Act, 2019</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 items-baseline">
                        <span class="sm:col-span-5 text-slate-500 font-medium">Retention Period</span>
                        <span class="sm:col-span-7 text-[#1d1d1b] font-semibold">1 academic year from event date</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 items-baseline">
                        <span class="sm:col-span-5 text-slate-500 font-medium">Contact</span>
                        <span class="sm:col-span-7 text-[#1d1d1b] font-semibold">dataprotection@strathmore.edu</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 items-baseline">
                        <span class="sm:col-span-5 text-slate-500 font-medium">DPIA Status</span>
                        <span class="sm:col-span-7 text-[#1d1d1b] font-semibold">Initial DPIA completed — June 2025</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Confirmation Modal for Irreversible Points Reset -->
    <div id="reset-points-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-7 shadow-xl">
            <h3 class="text-lg font-extrabold text-slate-900">Confirm Staff Points Reset</h3>
            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                This will zero the wellness point balances for all <strong>{{ $activeStaffCount ?? 0 }}</strong> active staff members. Type <strong>RESET</strong> below to confirm.
            </p>

            <form method="POST" action="{{ route('settings.reset_points') }}" class="mt-5 space-y-4">
                @csrf
                <input type="text"
                       name="confirmation"
                       required
                       placeholder="Type RESET to confirm"
                       class="w-full border border-slate-300 rounded-xl text-xs font-extrabold p-3 bg-slate-50 focus:bg-white focus:ring-[#e03c31] focus:border-[#e03c31]">

                <div class="flex items-center justify-end gap-2.5">
                    <button type="button"
                            onclick="document.getElementById('reset-points-modal').classList.add('hidden')"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#e03c31] hover:bg-red-700 text-white text-xs font-extrabold shadow-sm transition">
                        Confirm Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection