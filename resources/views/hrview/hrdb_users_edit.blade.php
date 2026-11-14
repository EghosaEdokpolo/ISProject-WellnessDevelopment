<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d' } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-3xl mx-auto px-6 py-8">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit User</h1>
                <p class="text-sm text-gray-500 mt-1">Update the details for {{ $user->name }}.</p>
            </div>
            <a href="{{ route('hr.users.index') }}" class="text-sm font-bold text-gray-500 hover:text-brand">&larr; Back to Users</a>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm font-bold border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
            <form method="POST" action="{{ route('hr.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border-gray-200 rounded-lg text-sm p-3 focus:ring-brand focus:border-brand">
                </div>

                <!-- REMOVED EMPLOYEE ID INPUT -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Staff ID (Login)</label>
                    <input type="text" name="staff_id" value="{{ old('staff_id', $user->staff_id) }}" required class="w-full border-gray-200 rounded-lg text-sm p-3 focus:ring-brand focus:border-brand">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border-gray-200 rounded-lg text-sm p-3 focus:ring-brand focus:border-brand">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Role</label>
                        <select name="role" required class="w-full border-gray-200 rounded-lg text-sm p-3 focus:ring-brand focus:border-brand">
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff Member</option>
                            <option value="hr_coordinator" {{ old('role', $user->role) === 'hr_coordinator' ? 'selected' : '' }}>HR Coordinator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Department</label>
                        <select name="department" required class="w-full border-gray-200 rounded-lg text-sm p-3 focus:ring-brand focus:border-brand">
                            @foreach(['School of Computing and Engineering Sciences', 'Strathmore Business School', 'School of Law', 'School of Humanities and Social Sciences', 'Finance and Accounting', 'Human Resources', 'Cafeteria Staff', 'Housekeeping'] as $dept)
                                <option value="{{ $dept }}" {{ old('department', $user->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('hr.users.index') }}" class="px-6 py-2.5 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-100 transition">Cancel</a>
                    <button type="submit" class="bg-brand text-white font-bold py-2.5 px-6 rounded-lg hover:bg-blue-800 transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>