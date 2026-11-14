<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: '#02338d' } } } }</script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 max-w-md w-full">
        
        <!-- Logo (Optional) -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('assets/Strathmore_uni_logo-text.png') }}" alt="Strathmore Logo" class="h-16">
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
        <p class="text-sm text-gray-500 mb-6">Enter your Strathmore email address and we'll send you a secure link to reset your WPMIS password.</p>

        @if(session('status'))
            <div class="mb-4 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm font-bold border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm font-bold border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-brand focus:border-brand">
            </div>
            <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition shadow-sm">
                Send Reset Link
            </button>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-brand font-semibold">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>