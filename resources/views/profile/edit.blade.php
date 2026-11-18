@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Staff Wellness Profile</h1>
            <p class="text-xs text-slate-500 italic mt-0.5">
                Strathmore University WellLoop Participant Account
            </p>
        </div>

        <span class="text-[11px] font-extrabold uppercase tracking-wider bg-[#e8f0fe] text-[#02338d] px-3.5 py-1.5 rounded-full">
            STAFF ID: {{ $user->staff_id }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-extrabold">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3.5 rounded-2xl text-xs font-extrabold">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                        Full Name
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full border border-slate-200 rounded-xl text-sm font-bold p-3.5 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                        Department / School
                    </label>
                    <input type="text"
                           name="department"
                           value="{{ old('department', $user->department) }}"
                           class="w-full border border-slate-200 rounded-xl text-sm font-bold p-3.5 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                        Institutional Email
                    </label>
                    <input type="text"
                           value="{{ $user->email }}"
                           disabled
                           class="w-full border border-slate-200 rounded-xl text-sm font-bold p-3.5 bg-slate-100 text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('dashboard') }}" class="text-xs font-extrabold text-slate-500 hover:text-slate-800 transition">
                    ← Back to Dashboard
                </a>
                <button type="submit"
                        class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-6 py-3.5 rounded-xl shadow-sm transition">
                    Save Profile
                </button>
            </div>
        </form>
    </div>

</div>
@endsection