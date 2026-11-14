<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::orderByDesc('created_at')->paginate(15);
        return view('hrview.hrdb_users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'staff_id'    => ['required', 'string', 'max:30', 'unique:users,staff_id'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8'],
            'role'        => ['required', 'in:staff,hr_coordinator'],
            'department'  => ['required', 'string', 'max:255'],
        ]);

        User::create([
            'name'           => $data['name'],
            'staff_id'       => $data['staff_id'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'role'           => $data['role'],
            'department'     => $data['department'],
            'points_balance' => 0,
        ]);

        return redirect()->route('hr.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('hrview.hrdb_users_edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'staff_id'    => ['required', 'string', 'max:30', 'unique:users,staff_id,' . $user->id],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'        => ['required', 'in:staff,hr_coordinator'],
            'department'  => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'name'        => $data['name'],
            'staff_id'    => $data['staff_id'],
            'email'       => $data['email'],
            'role'        => $data['role'],
            'department'  => $data['department'],
        ]);

        return redirect()->route('hr.users.index')->with('success', 'User updated successfully.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        try {
            $csv = Reader::createFromPath($request->file('csv_file')->getRealPath(), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
            $imported = 0;
            $updated = 0;

            DB::beginTransaction();

            foreach ($records as $record) {
                if (!isset($record['name']) || !isset($record['staff_id']) || !isset($record['email']) || !isset($record['department'])) {
                    continue; 
                }

                $user = User::where('staff_id', $record['staff_id'])->first();

                if ($user) {
                    $user->update([
                        'name'       => $record['name'],
                        'email'      => $record['email'],
                        'department' => $record['department'],
                        'role'       => $record['role'] ?? 'staff',
                    ]);
                    $updated++;
                } else {
                    User::create([
                        'name'           => $record['name'],
                        'staff_id'       => $record['staff_id'],
                        'email'          => $record['email'],
                        'department'     => $record['department'],
                        'role'           => $record['role'] ?? 'staff',
                        'password'       => Hash::make('Strathmore2026!'),
                        'points_balance' => 0,
                    ]);
                    $imported++;
                }
            }

            DB::commit();
            return redirect()->back()->with('success', "Import successful: {$imported} created, {$updated} updated.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['csv_file' => 'Error parsing CSV: ' . $e->getMessage()]);
        }
    }
}