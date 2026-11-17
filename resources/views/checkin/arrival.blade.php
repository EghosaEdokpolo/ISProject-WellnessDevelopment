<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Check-In | Strathmore University</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    
    <!-- Official Typography Source -->
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        brand: '#02338d',          /* Strathmore Official Blue */
                        'brand-dark': '#01225e',
                        'brand-light': '#e8f0fe', 
                        wl: { gold: '#c99a2e' }    /* Accent Gold */
                    }, 
                    fontFamily: { 
                        sans: ['Sansation', 'sans-serif'] 
                    } 
                } 
            } 
        }
    </script>
    <style> 
        body, h1, h2, h3, h4, h5, h6, p, div, span, a, button, input, select, textarea { 
            font-family: 'Sansation', sans-serif !important; 
        } 
        body { background-color: #f4f6f9; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        
        <!-- University Header Branding -->
        <div class="text-center mb-6">
            <h2 class="text-xs font-bold tracking-widest text-slate-400 uppercase">Strathmore University</h2>
            <p class="text-sm font-semibold text-brand">Institutional Wellness & Event Management</p>
        </div>

        @if(session('confirmed'))
            @php $confirmation = session('confirmed'); @endphp
            <div class="bg-white rounded-3xl border border-emerald-200 shadow-2xl p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
                
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-extrabold text-slate-900 mb-1">
                    {{ $confirmation['already_checked_in'] ? 'Already Checked In!' : 'Check-In Successful!' }}
                </h1>
                <p class="text-slate-500 mb-6 font-medium">Welcome, <strong class="text-slate-800">{{ $confirmation['name'] }}</strong></p>
                
                @if(!$confirmation['already_checked_in'])
                    <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 mb-6 shadow-sm">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Wellness Points Earned</p>
                        <p class="text-3xl font-extrabold text-emerald-600">+{{ $confirmation['points'] }} Points</p>
                    </div>
                @else
                    <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 mb-6 text-amber-800 text-sm font-medium">
                        You have already completed check-in for this session. Enjoy the event!
                    </div>
                @endif
                
                <p class="text-sm text-slate-500">You are registered for <strong class="text-slate-800">{{ $confirmation['event'] }}</strong>.</p>
                
                <div class="mt-8">
                    <a href="{{ route('events.index') }}" class="inline-block w-full bg-slate-900 text-white font-bold py-3.5 rounded-xl shadow hover:bg-slate-800 transition text-center">
                        Return to Events Dashboard
                    </a>
                </div>
            </div>

        @else
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-brand"></div>
                
                <div class="w-20 h-20 bg-brand-light rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-slate-900 mb-1">Venue Check-In</h1>
                <p class="text-slate-500 text-sm mb-6 font-medium">{{ $event->name }}</p>

                @if($errors->any())
                    <div class="mb-6 bg-rose-50 text-rose-600 px-4 py-3.5 rounded-2xl text-sm font-bold border border-rose-200 text-left shadow-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkin.confirm', $event) }}" class="text-left">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Enter your Staff ID</label>
                        <input type="text" name="staff_id" required placeholder="e.g., 123456" autofocus value="{{ old('staff_id') }}"
                               class="w-full border border-slate-300 rounded-2xl text-lg p-4 bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand font-bold text-center tracking-widest text-slate-800 outline-none transition">
                    </div>
                    <button type="submit" class="w-full bg-brand text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-brand-dark transition text-lg flex items-center justify-center gap-2">
                        Confirm Attendance
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            </div>
        @endif
    </div>

</body>
</html>