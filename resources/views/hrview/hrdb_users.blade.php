<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#02338d', 'brand-light': '#e8f0fe' } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body>
    <x-hr-nav />

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manage Users</h1>
                <p class="text-sm text-gray-500 mt-1">Add new staff members or HR coordinators to the system.</p>
            </div>
            <!-- UPDATED CSV TEMPLATE LINK (Removed employee_id) -->
            <a href="data:text/csv;charset=utf-8,name,staff_id,email,department,role%0AJohn%20Doe,000200,john@strathmore.edu,School%20of%20Law,staff" download="wpmis_user_template.csv" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download CSV Template
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm font-bold border border-emerald-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm font-bold border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-brand font-bold text-lg mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Bulk Import Users
                </h3>
                <p class="text-sm text-gray-600">Upload a CSV file to create or update up to 1,000+ users instantly. Default password will be <strong>Strathmore2026!</strong></p>
            </div>
            
            <form method="POST" action="{{ route('hr.users.import') }}" enctype="multipart/form-data" class="flex gap-2 w-full md:w-auto">
                @csrf
                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-brand file:text-white hover:file:bg-blue-800 border border-gray-200 rounded-lg bg-white">
                <button type="submit" class="bg-brand text-white font-bold px-6 py-2.5 rounded-lg shadow-sm hover:bg-blue-800 whitespace-nowrap">Upload CSV</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Add Single User</h2>
                    
                    <form method="POST" action="{{ route('hr.users.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                            <input type="text" name="name" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                        </div>

                        <!-- REMOVED EMPLOYEE ID INPUT, KEPT STAFF ID -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Staff ID (Login)</label>
                            <input type="text" name="staff_id" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand" placeholder="e.g. 000200">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                            <input type="email" name="email" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Password</label>
                            <input type="text" name="password" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand" value="Strathmore2026!">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Role</label>
                            <select name="role" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                                <option value="staff">Staff Member</option>
                                <option value="hr_coordinator">HR Coordinator</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Department</label>
                            <select name="department" required class="w-full border-gray-200 rounded-lg text-sm p-2.5 focus:ring-brand focus:border-brand">
                                <option value="School of Computing and Engineering Sciences">School of Computing and Engineering Sciences</option>
                                <option value="Strathmore Business School">Strathmore Business School</option>
                                <option value="School of Law">School of Law</option>
                                <option value="School of Humanities and Social Sciences">School of Humanities and Social Sciences</option>
                                <option value="Finance and Accounting">Finance and Accounting</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Cafeteria Staff">Cafeteria Staff</option>
                                <option value="Housekeeping">Housekeeping</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-brand text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition shadow-sm mt-2">Create User</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">System Users ({{ $users->total() }})</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">Name</th>
                                    <th class="px-6 py-4">Staff ID</th>
                                    <th class="px-6 py-4">Role</th>
                                    <th class="px-6 py-4">Department</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($users as $u)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $u->staff_id }}</td>
                                        <td class="px-6 py-4">
                                            @if($u->role === 'hr_coordinator')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700 border border-purple-200">HR Admin</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">Staff</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $u->department }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('hr.users.edit', $u) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1.5 rounded-lg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>
</body>
</html>