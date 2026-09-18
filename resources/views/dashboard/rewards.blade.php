@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Points &amp; Leaderboard</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Points reset annually — controlled by HR.</p>

    <div class="grid grid-cols-2 gap-5">
        <div class="bg-wl-indigo text-white rounded-xl p-6">
            <p class="text-xs uppercase text-white/70">Your balance</p>
            <p class="text-4xl font-bold text-wl-gold mt-1">{{ $me->points_balance }}</p>
            <p class="text-xs text-white/70">wellness points</p>

            <div class="mt-5 space-y-2 text-sm">
                {{-- POINTER: pluck('reason') pulls just that one column out
                     as a simple array — cheaper than loading full model
                     objects when all we need is the transaction reasons. --}}
                @foreach($me->pointsTransactions()->latest()->take(5)->get() as $tx)
                    <div class="flex justify-between bg-white/10 rounded-lg px-3 py-2">
                        <span class="capitalize">{{ str_replace('_', ' ', $tx->reason) }}</span>
                        <span class="text-wl-gold">+{{ $tx->points }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="font-semibold text-gray-900 mb-3">🏆 Department Leaderboard</p>
            @foreach($leaderboard as $i => $person)
                <div class="flex justify-between px-3 py-2 rounded-lg {{ $person->id === $me->id ? 'bg-wl-lavender' : '' }}">
                    <span class="text-sm text-gray-700">{{ $i + 1 }}. {{ $person->name }}</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $person->points_balance }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
