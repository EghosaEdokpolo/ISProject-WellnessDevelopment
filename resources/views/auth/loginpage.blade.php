<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strathmore Wellness - Login</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" type="image/x-icon">
    
    <!-- Tailwind for fast styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', wl: { gold: '#c99a2e' } } } } }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center font-sans">

    <!-- Logo -->
    <div class="mb-8">
        <a href="https://strathmore.edu" target="_blank">
            <img src="{{ asset('images/Strathmore_uni_logo-text.png') }}" alt="Strathmore University Logo" class="h-24">
        </a>
    </div>

    <!-- Login Card -->
    <div class="bg-brand text-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-8 border-wl-gold">
        <h1 class="text-3xl font-bold text-center mb-8">Log In To Your Account!</h1>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-500 text-white p-3 rounded-lg text-sm font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="staff_id" class="block text-sm font-semibold mb-1">Staff ID Number</label>
                <input type="text" name="staff_id" id="staff_id" required placeholder="000000" 
                       class="w-full px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-4 focus:ring-wl-gold/50">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold mb-1">Password</label>
                <input type="password" name="password" id="password" required 
                       class="w-full px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-4 focus:ring-wl-gold/50">
            </div>

            <button type="submit" class="w-full bg-wl-gold text-brand font-bold text-lg py-3 rounded-lg hover:bg-yellow-600 transition shadow-md mt-4">
                Login
            </button>
        </form>

        <!-- Updated Forgot Password Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-gray-200 hover:text-white hover:underline transition">
                Forgot your WPMIS password?
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-12 text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} Strathmore University - All Rights Reserved</p>
    </footer>

</body>
</html>