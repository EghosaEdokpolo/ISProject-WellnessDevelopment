<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: '#02338d' } } } }</script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 max-w-md w-full">
        
        <div class="flex justify-center mb-6">
            <img src="{{ asset('assets/Strathmore_uni_logo-text.png') }}" alt="Strathmore Logo" class="h-16">
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Set New Password</h1>

        @if($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm font-bold border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="w-full border-gray-300 rounded-lg p-3 bg-gray-100 text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-brand focus:border-brand">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-brand focus:border-brand">
            </div>
            
            <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition shadow-sm mt-2">
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>