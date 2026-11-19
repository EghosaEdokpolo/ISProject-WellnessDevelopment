@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        font-family: 'Montserrat', sans-serif !important;
        border-radius: 16px !important;
        box-shadow: 0 12px 32px rgba(2, 51, 141, 0.16) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
    }
    .flatpickr-day.selected, .flatpickr-day.selected:hover {
        background: #02338d !important;
        border-color: #02338d !important;
    }
    .flatpickr-time {
        border-top: 1px solid #e2e8f0 !important;
        padding: 4px 8px !important;
    }
    .flatpickr-time .flatpickr-am-pm {
        background: #e8f0fe !important;
        color: #02338d !important;
        font-weight: 800 !important;
        border-radius: 8px !important;
        margin: 4px !important;
        cursor: pointer !important;
        transition: all 0.15s ease;
    }
    .flatpickr-time .flatpickr-am-pm:hover {
        background: #02338d !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
@php
    $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
    $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

    $isPast = $isPast ?? ($event->starts_at === null || $event->starts_at->copy()->shiftTimezone($tz)->lte($cutoff));
    $formattedDate = old('starts_at', $event->starts_at?->copy()->shiftTimezone($tz)->format('Y-m-d H:i'));
@endphp

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('events.management') }}" class="text-xs font-extrabold text-[#02338d] hover:underline transition">
            ← Back to Events Management
        </a>

        <div class="flex items-center gap-2">
            @if($isPast)
                <span class="text-[10px] font-extrabold uppercase tracking-wider bg-slate-200 text-slate-700 border border-slate-300 px-3 py-1 rounded-full">
                    Past Event • Audit-Protected Mode
                </span>
            @else
                <span class="text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 px-3 py-1 rounded-full">
                    Upcoming Event • Full Edit Mode
                </span>
            @endif
            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-[#f5eccd]/70 text-[#8a6514] border border-[#c99a2e]/30 px-3 py-1 rounded-full">
                Session ID #{{ $event->id }}
            </span>
        </div>
    </div>

    @if($isPast)
        <div class="bg-amber-50/90 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <div class="text-xs text-amber-950 leading-relaxed">
                <span class="font-extrabold block">Historical Audit Protection Active</span>
                You can edit the <strong>Event Name</strong>, <strong>Location</strong>, <strong>WhatsApp URL</strong>, and <strong>Description</strong> to correct typos. Critical audit fields (<strong>Date & Start Time</strong>, <strong>Capacity</strong>, <strong>Wellness Pillar</strong>, and <strong>Points</strong>) are locked to preserve completed attendance and WHO-5 records.
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#e03c31]/10 text-[#b8281e] px-5 py-4 rounded-2xl text-xs font-bold border border-[#e03c31]/30 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-[#02338d] to-[#c99a2e]"></div>

        <div class="p-6 sm:p-8">
            <div class="pb-5 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d]">
                    {{ $isPast ? 'Edit Past Session Details (Typo & Text Correction)' : 'Edit Upcoming Wellness Session' }}
                </span>
                <h1 class="text-xl font-extrabold text-[#1d1d1b] mt-1 tracking-tight">{{ $event->name }}</h1>
            </div>

            <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-5 pt-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        Event Name <span class="text-emerald-600 font-bold">(Editable)</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $event->name) }}"
                           required
                           class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                                Date & Start Time (EAT)
                            </label>
                            @if($isPast)
                                <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">
                                    🔒 Locked
                                </span>
                            @endif
                        </div>

                        @if($isPast)
                            <input type="text"
                                   value="{{ $event->starts_at?->copy()->shiftTimezone($tz)->format('D, d M Y \a\t h:i A') }} EAT"
                                   disabled
                                   readonly
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 p-3 bg-slate-100 cursor-not-allowed select-none">
                        @else
                            <div class="relative flex items-center" id="edit-datetime-picker">
                                <input type="text"
                                       id="edit_starts_at"
                                       name="starts_at"
                                       value="{{ $formattedDate }}"
                                       placeholder="Click calendar to pick..."
                                       data-input
                                       required
                                       readonly
                                       class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] py-3 pl-3 pr-10 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d] cursor-pointer">
                                <button type="button"
                                        title="Open Calendar & AM/PM Time Picker"
                                        data-toggle
                                        class="absolute right-2.5 text-[#02338d] hover:text-[#01225e] p-1 rounded-lg hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p id="edit-datetime-clamp-notice" class="hidden text-[10px] font-extrabold text-amber-700 mt-1">
                                Past time prevented — automatically set to current EAT time.
                            </p>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                                Capacity
                            </label>
                            @if($isPast)
                                <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">
                                    🔒 Locked
                                </span>
                            @endif
                        </div>

                        @if($isPast)
                            <input type="number"
                                   value="{{ $event->capacity }}"
                                   disabled
                                   readonly
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 p-3 bg-slate-100 cursor-not-allowed select-none">
                        @else
                            <input type="number"
                                   name="capacity"
                                   value="{{ old('capacity', $event->capacity) }}"
                                   min="1"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        Location <span class="text-emerald-600 font-bold">(Editable)</span>
                    </label>
                    <input type="text"
                           name="location"
                           value="{{ old('location', $event->location) }}"
                           required
                           class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                            Wellness Category
                        </label>
                        @if($isPast)
                            <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">
                                🔒 Locked
                            </span>
                        @endif
                    </div>

                    @php $selectedCat = old('category', $event->category); @endphp
                    @if($isPast)
                        <input type="text"
                               value="{{ ucfirst((string) $event->category) }} Wellness"
                               disabled
                               readonly
                               class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 p-3 bg-slate-100 cursor-not-allowed select-none">
                    @else
                        <select name="category"
                                required
                                class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                            <option value="physical" {{ $selectedCat === 'physical' ? 'selected' : '' }}>Physical</option>
                            <option value="mental" {{ $selectedCat === 'mental' ? 'selected' : '' }}>Mental</option>
                            <option value="financial" {{ $selectedCat === 'financial' ? 'selected' : '' }}>Financial</option>
                            <option value="social" {{ $selectedCat === 'social' ? 'selected' : '' }}>Social</option>
                            <option value="intellectual" {{ $selectedCat === 'intellectual' ? 'selected' : '' }}>Intellectual</option>
                            <option value="occupational" {{ $selectedCat === 'occupational' ? 'selected' : '' }}>Occupational</option>
                            <option value="environmental" {{ $selectedCat === 'environmental' ? 'selected' : '' }}>Environmental</option>
                            <option value="spiritual" {{ $selectedCat === 'spiritual' ? 'selected' : '' }}>Spiritual</option>
                        </select>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                                Check-In Pts
                            </label>
                            @if($isPast)
                                <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">
                                    🔒 Locked
                                </span>
                            @endif
                        </div>

                        @if($isPast)
                            <input type="number"
                                   value="{{ $event->points_checkin }}"
                                   disabled
                                   readonly
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 p-3 bg-slate-100 cursor-not-allowed select-none">
                        @else
                            <input type="number"
                                   name="points_checkin"
                                   value="{{ old('points_checkin', $event->points_checkin) }}"
                                   min="0"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600">
                                Feedback Pts
                            </label>
                            @if($isPast)
                                <span class="text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">
                                    🔒 Locked
                                </span>
                            @endif
                        </div>

                        @if($isPast)
                            <input type="number"
                                   value="{{ $event->points_feedback }}"
                                   disabled
                                   readonly
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 p-3 bg-slate-100 cursor-not-allowed select-none">
                        @else
                            <input type="number"
                                   name="points_feedback"
                                   value="{{ old('points_feedback', $event->points_feedback) }}"
                                   min="0"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                        Event WhatsApp Channel URL <span class="text-emerald-600 font-bold">(Editable)</span>
                    </label>
                    <p class="text-[11px] text-slate-500 mb-1.5">
                        Staff are directed to this link after completing registration and waiver signing.
                    </p>
                    <input type="text"
                           name="whatsapp_channel_url"
                           value="{{ old('whatsapp_channel_url', $event->whatsapp_channel_url) }}"
                           placeholder="https://whatsapp.com/channel/... or https://chat.whatsapp.com/..."
                           class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                        Description <span class="text-emerald-600 font-bold">(Editable)</span>
                    </label>
                    <textarea name="description"
                              rows="4"
                              class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">{{ old('description', $event->description) }}</textarea>
                </div>

                <!-- Action Bar with Cancel, Delete Session, and Update -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('events.management') }}"
                           class="text-xs font-extrabold bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-3.5 rounded-xl transition">
                            Cancel
                        </a>

                        <button type="button"
                                onclick="document.getElementById('delete-event-modal').classList.remove('hidden')"
                                class="text-xs font-extrabold bg-rose-50 hover:bg-[#e03c31] text-[#e03c31] hover:text-white border border-rose-200 hover:border-[#e03c31] px-4 py-3.5 rounded-xl transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Delete Session</span>
                        </button>
                    </div>

                    <button type="submit"
                            class="bg-[#02338d] hover:bg-[#012466] text-white text-xs font-extrabold px-6 py-3.5 rounded-xl shadow-sm transition">
                        {{ $isPast ? 'Save Text Corrections' : 'Update Wellness Session' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Safe Delete Confirmation Modal (Outside Update Form to Prevent Nested Forms) -->
    <div id="delete-event-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-7 shadow-xl">
            <h3 class="text-lg font-extrabold text-slate-900">Delete Wellness Session?</h3>
            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                Are you sure you want to permanently delete <strong>"{{ $event->name }}"</strong>? All registrations, check-ins, and WHO-5 responses attached to this session will be removed, and affected staff point balances will be automatically recalculated.
            </p>

            <form method="POST" action="{{ route('events.destroy', $event) }}" class="mt-6 flex items-center justify-end gap-2.5">
                @csrf
                @method('DELETE')
                <button type="button"
                        onclick="document.getElementById('delete-event-modal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#e03c31] hover:bg-red-700 text-white text-xs font-extrabold shadow-sm transition">
                    Yes, Delete Permanently
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(! $isPast)
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof flatpickr !== 'function') return;

        function clampEditDateToCurrentTime(selectedDates, instance) {
            const now = new Date();
            now.setSeconds(0, 0);

            if (!selectedDates || selectedDates.length === 0) return;

            const picked = selectedDates[0];
            const isToday =
                picked.getFullYear() === now.getFullYear() &&
                picked.getMonth() === now.getMonth() &&
                picked.getDate() === now.getDate();

            if (isToday) {
                const minH = String(now.getHours()).padStart(2, '0');
                const minM = String(now.getMinutes()).padStart(2, '0');
                instance.set('minTime', `${minH}:${minM}`);
            } else {
                instance.set('minTime', null);
            }

            if (picked.getTime() < now.getTime()) {
                instance.setDate(now, false);
                if (instance.altInput) {
                    instance.altInput.value = instance.formatDate(now, instance.config.altFormat);
                }
                instance.input.value = instance.formatDate(now, instance.config.dateFormat);

                const notice = document.getElementById('edit-datetime-clamp-notice');
                if (notice) {
                    notice.classList.remove('hidden');
                    clearTimeout(window.__editClampTimer);
                    window.__editClampTimer = setTimeout(() => notice.classList.add('hidden'), 4000);
                }
            }
        }

        flatpickr('#edit-datetime-picker', {
            wrap: true,
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D, M j, Y \\a\\t h:i K',
            time_24hr: false,
            minDate: 'today',
            minuteIncrement: 5,
            allowInput: false,
            clickOpens: true,
            onOpen: function (selectedDates, dateStr, instance) {
                clampEditDateToCurrentTime(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                clampEditDateToCurrentTime(selectedDates, instance);
            },
            onClose: function (selectedDates, dateStr, instance) {
                clampEditDateToCurrentTime(selectedDates, instance);
            }
        });
    });
</script>
@endif
@endpush