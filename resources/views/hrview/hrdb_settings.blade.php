<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Settings &amp; Compliance | Strathmore University</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    
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

        html.a11y-large-text {
            font-size: 112.5%;
        }
        html.a11y-high-contrast body {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        html.a11y-high-contrast .border-gray-200,
        html.a11y-high-contrast .border-gray-100 {
            border-color: #1e293b !important;
        }
        html.a11y-high-contrast .text-gray-500,
        html.a11y-high-contrast .text-gray-400 {
            color: #1e293b !important;
        }
        html.a11y-reduced-motion *,
        html.a11y-reduced-motion *::before,
        html.a11y-reduced-motion *::after {
            animation-duration: 0.001ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.001ms !important;
            scroll-behavior: auto !important;
        }
        html.a11y-readable-spacing body {
            letter-spacing: 0.03em;
            word-spacing: 0.08em;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-wrap justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">HR Settings &amp; Compliance</h1>
                <p class="text-sm text-gray-500 mt-1">UC-15 - Points management, data protection, accessibility controls.</p>
            </div>
            <span class="text-xs font-bold bg-brand-light text-brand px-3.5 py-2 rounded-xl border border-brand/15">
                Active Staff Directory: {{ number_format($activeStaffCount ?? 0) }}
            </span>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-bold border border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 text-rose-600 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Top Two-Column Grid: Points Reset Control + Data Protection Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Left Panel: Points Reset Control -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Points Reset Control</h2>
                    </div>

                    <p class="text-xs text-gray-500 leading-relaxed mb-6">
                        Points reset annually, controlled by HR only. This action is irreversible — all staff balances will be zeroed.
                    </p>

                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-5 space-y-3.5 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Current cycle:</span>
                            <span class="font-bold text-gray-900">{{ $points['current_academic_year'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Next scheduled reset:</span>
                            <span class="font-bold text-gray-900">{{ $points['next_reset'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm pt-2 border-t border-gray-200/70">
                            <span class="text-gray-500">Total points in circulation:</span>
                            <span class="font-bold text-base text-brand">{{ number_format((int) $points['total_active_points']) }}</span>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="openResetModal()" class="w-full py-3.5 px-4 rounded-xl border border-rose-200 bg-rose-50/40 text-rose-600 font-bold text-sm hover:bg-rose-100 hover:border-rose-300 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset All Staff Points
                </button>
            </div>

            <!-- Right Panel: Data Protection Panel -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Data Protection Panel</h2>
                    </div>

                    <div class="divide-y divide-gray-100 text-sm">
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500">Data Controller</span>
                            <span class="font-bold text-gray-900">Strathmore University, Madaraka Estate</span>
                        </div>
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500">Applicable Law</span>
                            <span class="font-bold text-gray-900">Kenya Data Protection Act, 2019</span>
                        </div>
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500">Retention Period</span>
                            <span class="font-bold text-gray-900">1 academic year from event date</span>
                        </div>
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500">Contact</span>
                            <a href="mailto:dpo@strathmore.edu" class="font-bold text-brand hover:underline">dpo@strathmore.edu</a>
                        </div>
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500">DPIA Status</span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Completed June 2025
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <span>Audit trail logged via DataProcessingLog</span>
                    <span class="font-bold text-brand">KDPA Compliant</span>
                </div>
            </div>
        </div>

        <!-- Middle Section: HR Coordinator Profile & Security (POST /settings -> UpdateSettingsRequest) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">HR Coordinator Account &amp; Security</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Update your HR display name or rotate your administrative password.</p>
                </div>
                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full bg-brand-light text-brand">Staff ID: {{ $user->staff_id }}</span>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">HR Coordinator Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Password (Optional)</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-brand focus:border-brand">
                    </div>
                </div>

                <div class="pt-1 flex justify-end">
                    <button type="submit" class="bg-wl-gold text-brand font-bold text-sm px-6 py-3 rounded-xl shadow-sm hover:bg-yellow-600 transition">
                        Save Account Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Bottom Section: Accessibility Controls (UC-15) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Accessibility Controls</h2>
                        <p class="text-xs text-gray-500">WCAG 2.1 AA workplace display preferences saved for this workstation.</p>
                    </div>
                </div>

                <button type="button" onclick="resetAccessibility()" class="text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 px-3.5 py-2 rounded-lg transition">
                    Reset Defaults
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <label class="flex items-start justify-between gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">High Contrast</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Boost border &amp; text sharpness</span>
                    </div>
                    <input type="checkbox" id="toggle-high-contrast" onchange="toggleA11yPreference('a11y-high-contrast', this.checked)" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand">
                </label>

                <label class="flex items-start justify-between gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">Large Text (+12%)</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Increase dashboard font scale</span>
                    </div>
                    <input type="checkbox" id="toggle-large-text" onchange="toggleA11yPreference('a11y-large-text', this.checked)" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand">
                </label>

                <label class="flex items-start justify-between gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">Readable Spacing</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Widen letter &amp; line spacing</span>
                    </div>
                    <input type="checkbox" id="toggle-readable-spacing" onchange="toggleA11yPreference('a11y-readable-spacing', this.checked)" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand">
                </label>

                <label class="flex items-start justify-between gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">Reduced Motion</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Minimize UI transitions</span>
                    </div>
                    <input type="checkbox" id="toggle-reduced-motion" onchange="toggleA11yPreference('a11y-reduced-motion', this.checked)" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand">
                </label>
            </div>
        </div>

        <!-- Bottom-Right Logout Bar -->
        <div class="flex justify-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Log out
                </button>
            </form>
        </div>

    </main>

    <!-- Two-Step Confirmation Modal for Reset All Staff Points -->
    <div id="reset-points-modal" class="hidden fixed inset-0 z-50 bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-6 sm:p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Confirm Annual Points Reset</h3>
                    <p class="text-xs text-rose-600 font-bold">Irreversible Action • Cycle {{ $points['current_academic_year'] }}</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 leading-relaxed mb-4">
                You are about to zero out <strong>{{ number_format((int) $points['total_active_points']) }} wellness points</strong> across all staff accounts. Historical attendance and WHO-5 wellbeing records remain preserved, but all redeemable staff point balances will reset to <strong>0</strong>.
            </p>

            <form method="POST" action="{{ route('settings.reset_points') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                        Type <span class="text-rose-600">RESET</span> to authorize
                    </label>
                    <input type="text" id="reset-confirmation-input" name="confirmation" required autocomplete="off" placeholder="Type RESET" oninput="validateResetInput(this.value)" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50 focus:ring-rose-500 focus:border-rose-500 uppercase">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeResetModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" id="confirm-reset-btn" disabled class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-300 cursor-not-allowed transition">
                        Zero All Staff Balances
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const A11Y_KEYS = [
            { className: 'a11y-high-contrast', checkboxId: 'toggle-high-contrast' },
            { className: 'a11y-large-text', checkboxId: 'toggle-large-text' },
            { className: 'a11y-readable-spacing', checkboxId: 'toggle-readable-spacing' },
            { className: 'a11y-reduced-motion', checkboxId: 'toggle-reduced-motion' }
        ];

        document.addEventListener('DOMContentLoaded', function() {
            A11Y_KEYS.forEach(item => {
                if (localStorage.getItem(item.className) === 'true') {
                    document.documentElement.classList.add(item.className);
                    const cb = document.getElementById(item.checkboxId);
                    if (cb) cb.checked = true;
                }
            });
        });

        function toggleA11yPreference(className, enabled) {
            if (enabled) {
                document.documentElement.classList.add(className);
                localStorage.setItem(className, 'true');
            } else {
                document.documentElement.classList.remove(className);
                localStorage.removeItem(className);
            }
        }

        function resetAccessibility() {
            A11Y_KEYS.forEach(item => {
                document.documentElement.classList.remove(item.className);
                localStorage.removeItem(item.className);
                const cb = document.getElementById(item.checkboxId);
                if (cb) cb.checked = false;
            });
        }

        function openResetModal() {
            document.getElementById('reset-points-modal').classList.remove('hidden');
            const input = document.getElementById('reset-confirmation-input');
            input.value = '';
            validateResetInput('');
            setTimeout(() => input.focus(), 100);
        }

        function closeResetModal() {
            document.getElementById('reset-points-modal').classList.add('hidden');
        }

        function validateResetInput(val) {
            const btn = document.getElementById('confirm-reset-btn');
            if (val.trim().toUpperCase() === 'RESET') {
                btn.disabled = false;
                btn.classList.remove('bg-rose-300', 'cursor-not-allowed');
                btn.classList.add('bg-rose-600', 'hover:bg-rose-700', 'shadow-sm');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-rose-300', 'cursor-not-allowed');
                btn.classList.remove('bg-rose-600', 'hover:bg-rose-700', 'shadow-sm');
            }
        }
    </script>
</body>
</html>