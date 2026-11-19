@extends('layouts.app')

@section('content')
@php
    $photoUrl = ! empty($user->profile_photo_path) ? asset($user->profile_photo_path) : null;
    $initials = collect(explode(' ', trim((string) $user->name)))
        ->filter()
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
            Strathmore Staff Account
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">My Wellness Profile</h1>
        <p class="text-xs text-slate-500 mt-1">
            Official HR identity fields (Name, Email, Staff ID) are read-only. You may update your school/department, residence, phone, and profile photo.
        </p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-extrabold border border-rose-200 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Profile Details Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Profile Photo Upload & Live Preview -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 pb-6 border-b border-slate-100">
                <div class="relative w-20 h-20 rounded-2xl overflow-hidden bg-[#02338d] text-white flex items-center justify-center font-extrabold text-xl shrink-0 shadow-sm border-2 border-slate-200">
                    @if($photoUrl)
                        <img id="profile-photo-preview" src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <img id="profile-photo-preview" src="" alt="" class="hidden w-full h-full object-cover">
                        <span id="profile-initials-fallback">{{ $initials ?: 'SU' }}</span>
                    @endif
                </div>

                <div class="flex-1 space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                        Profile Photo <span class="text-emerald-600">(Editable)</span>
                    </label>
                    <input type="file"
                           name="profile_photo"
                           id="profile_photo_input"
                           accept="image/jpeg,image/png,image/webp"
                           onchange="previewProfilePhoto(event)"
                           class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-[#e8f0fe] file:text-[#02338d] hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400">JPG, PNG, or WEBP up to 2 MB. Displayed during venue check-in verification.</p>
                </div>
            </div>

            <!-- Locked HR Master Identity Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Full Name</label>
                        <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded">🔒 HR Locked</span>
                    </div>
                    <input type="text"
                           value="{{ $user->name }}"
                           disabled
                           readonly
                           class="w-full border border-slate-200 rounded-xl text-xs font-bold text-slate-500 p-3 bg-slate-100 cursor-not-allowed">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Strathmore Email</label>
                        <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded">🔒 HR Locked</span>
                    </div>
                    <input type="email"
                           value="{{ $user->email }}"
                           disabled
                           readonly
                           class="w-full border border-slate-200 rounded-xl text-xs font-bold text-slate-500 p-3 bg-slate-100 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Staff ID</label>
                        <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded">🔒 HR Locked</span>
                    </div>
                    <input type="text"
                           value="{{ $user->staff_id }}"
                           disabled
                           readonly
                           class="w-full border border-slate-200 rounded-xl text-xs font-extrabold text-[#02338d] p-3 bg-slate-100 cursor-not-allowed">
                </div>

                <!-- Editable School / Department Dropdown -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        School / Department <span class="text-emerald-600">(Editable)</span>
                    </label>
                    <select name="department"
                            required
                            class="w-full border border-slate-200 rounded-xl text-xs font-bold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" @selected(old('department', $user->department) === $dept)>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Editable Residence & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        Where You Live (Residence) <span class="text-emerald-600">(Editable)</span>
                    </label>
                    <input type="text"
                           name="residence"
                           value="{{ old('residence', $user->residence ?? '') }}"
                           placeholder="e.g., Madaraka Estate, South B, Lang'ata"
                           class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        Phone Number <span class="text-emerald-600">(Editable)</span>
                    </label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $user->phone ?? '') }}"
                           placeholder="e.g., +254 712 345 678"
                           class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="submit"
                        class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-6 py-3.5 rounded-xl shadow-sm transition">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <h2 class="text-base font-extrabold text-slate-900 mb-1">Change Password</h2>
        <p class="text-xs text-slate-500 mb-5">Use a strong password to keep your Strathmore WellLoop account secure.</p>

        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required class="w-full border border-slate-200 rounded-xl text-xs font-semibold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">New Password</label>
                    <input type="password" name="password" required class="w-full border border-slate-200 rounded-xl text-xs font-semibold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d]">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border border-slate-200 rounded-xl text-xs font-semibold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d]">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold px-6 py-3 rounded-xl shadow-sm transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewProfilePhoto(event) {
        const file = event.target.files?.[0];
        if (!file) return;
        const img      = document.getElementById('profile-photo-preview');
        const fallback = document.getElementById('profile-initials-fallback');
        const reader   = new FileReader();
        reader.onload  = function (e) {
            if (img) {
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            if (fallback) fallback.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush