<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'School of Computing and Engineering Sciences', 'code' => 'SCES', 'faculty' => 'Faculty of Information Technology'],
            ['name' => 'School of Law',                                'code' => 'SOL',  'faculty' => 'Faculty of Law'],
            ['name' => 'Strathmore Business School',                   'code' => 'SBS',  'faculty' => 'Business'],
            ['name' => 'School of Humanities and Social Sciences',     'code' => 'SHSS', 'faculty' => 'Humanities'],
            ['name' => 'Finance and Accounting',                       'code' => 'FIN',  'faculty' => 'Administration'],
            ['name' => 'Human Resources',                              'code' => 'HR',   'faculty' => 'Administration'],
            ['name' => 'Cafeteria Staff',                              'code' => 'CAF',  'faculty' => 'Support'],
            ['name' => 'Housekeeping',                                 'code' => 'HK',   'faculty' => 'Support'],
        ];

        foreach ($departments as $d) {
            Department::updateOrCreate(['code' => $d['code']], $d);
        }

        $hr = Department::where('code', 'HR')->first();

        Staff::updateOrCreate(
            ['staff_id_number' => '000001'],
            [
                'first_name'       => 'HR',
                'last_name'        => 'Admin',
                'email'            => 'hradmin@strathmore.edu',
                'password'         => Hash::make('password'),
                'department_id'    => $hr?->id,
                'role'             => StaffRole::HrAdmin->value,
                'consent_given'    => true,
                'consent_given_at' => now(),
                'is_active'        => true,
            ]
        );

        
    }
}