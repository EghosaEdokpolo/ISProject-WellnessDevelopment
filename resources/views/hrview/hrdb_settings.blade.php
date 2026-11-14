<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Settings & Compliance</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe', wl: { gold: '#c99a2e' } } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-5xl mx-auto px-6 py-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">HR Settings & Compliance</h1>
            <p class="text-sm text-gray-500 mt-1">UC-15 · Points management, data protection, accessibility controls.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm font-bold border border-emerald-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-2">
                    <div class="p-1.5 bg-red-50 text-red-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <h2 class="text-lg font-bold text-gray-900">Points Reset Control</h2>
                </div>
                <p class="text-sm text-gray-500 mb-6">Points reset annually, controlled by HR only. This action is irreversible — all staff balances will be zeroed.</p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100 space-y-3 flex-1">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Current cycle:</span>
                        <span class="font-bold text-gray-900">{{ $cycleStart }}/{{ $cycleEnd }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Next scheduled reset:</span>
                        <span class="font-bold text-gray-900">31 Aug {{ $cycleEnd }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Total points in circulation:</span>
                        <span class="font-bold text-brand">{{ number_format(\App\Models\User::sum('points_balance')) }}</span>
                    </div>
                </div>

                <button type="button" onclick="confirmReset()" id="reset-btn" class="w-full bg-red-50 text-red-600 border border-red-200 font-bold py-2.5 rounded-lg hover:bg-red-100 transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Reset All Staff Points
                </button>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-6">
                    <div class="p-1.5 bg-blue-50 text-brand rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                    <h2 class="text-lg font-bold text-gray-900">Data Protection Panel</h2>
                </div>

                <div class="space-y-0 border-t border-gray-100 flex-1">
                    <div class="flex py-3 border-b border-gray-100 text-sm">
                        <span class="w-1/3 text-gray-500 font-medium">Data Controller</span>
                        <span class="w-2/3 text-gray-900 font-semibold">Strathmore University, Madaraka Estate</span>
                    </div>
                    <div class="flex py-3 border-b border-gray-100 text-sm">
                        <span class="w-1/3 text-gray-500 font-medium">Applicable Law</span>
                        <span class="w-2/3 text-gray-900 font-semibold">Kenya Data Protection Act, 2019</span>
                    </div>
                    <div class="flex py-3 border-b border-gray-100 text-sm">
                        <span class="w-1/3 text-gray-500 font-medium">Retention Period</span>
                        <span class="w-2/3 text-gray-900 font-semibold">1 academic year from event date</span>
                    </div>
                    <div class="flex py-3 border-b border-gray-100 text-sm">
                        <span class="w-1/3 text-gray-500 font-medium">Contact</span>
                        <span class="w-2/3 text-gray-900 font-semibold">dataprotection@strathmore.edu</span>
                    </div>
                    <div class="flex py-3 text-sm">
                        <span class="w-1/3 text-gray-500 font-medium">DPIA Status</span>
                        <span class="w-2/3 text-emerald-600 font-bold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Completed — June 2025
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-8 flex justify-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 border border-red-600 text-white font-bold py-2.5 px-6 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Log out
                </button>
            </form>
        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function confirmReset() {
            const confirmed = confirm("⚠️ WARNING: This action is irreversible.\n\nAre you sure you want to reset points for ALL staff members? This will immediately zero out their balances.");
            if (!confirmed) return;

            const btn = document.getElementById('reset-btn');
            btn.innerHTML = 'Resetting... Please wait';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch('{{ route('settings.reset_points') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    btn.innerHTML = 'Reset All Staff Points';
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    alert('❌ An error occurred. Please check the logs.');
                }
            } catch (error) {
                btn.innerHTML = 'Reset All Staff Points';
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                alert('❌ A server error occurred.');
            }
        }
    </script>
</body>
</html>