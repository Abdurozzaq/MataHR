<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get department and position IDs
        $departmentHRD = \App\Models\Department::where('name', 'HRD')->first();
        $positionSuperadmin = \App\Models\Position::where('name', 'Superadmin')->first();
        $positionManager = \App\Models\Position::where('name', 'Manager')->first();
        $positionHRD = \App\Models\Position::where('name', 'HRD')->first();
        $positionSupervisor = \App\Models\Position::where('name', 'Supervisor')->first();
        $positionStaff = \App\Models\Position::where('name', 'Staff')->first();

        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'role' => 'superadmin',
            'password' => bcrypt('password'),
            'position_id' => $positionSuperadmin ? $positionSuperadmin->id : null,
            'department_id' => $departmentHRD ? $departmentHRD->id : null,
            'full_name' => 'Superadmin Fullname',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'gender' => 'male',
            'address' => 'Jl. Superadmin No.1',
            'phone' => '081234567890',
            'personal_email' => 'superadmin.personal@example.com',
            'marital_status' => 'single',
            'nik' => '1234567890',
            'employee_id' => '1',
            'start_date' => '2020-01-01',
            'employment_status' => 'permanent',
            'office_location' => 'HQ',
            'supervisor' => null,
            'salary' => 10000000.00,
            'benefits' => 'Health, Transport',
            'bank_account' => '1234567890',
            'bank_name' => 'Bank BCA',
            'email_verified_at' => now(),
            'remember_token' => null,
        ]);

        $manager = User::create([
            'name' => 'Manager Satu',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'password' => bcrypt('password'),
            'position_id' => $positionManager ? $positionManager->id : null,
            'department_id' => $departmentHRD ? $departmentHRD->id : null,
            'full_name' => 'Manager Satu Fullname',
            'birth_date' => '1991-02-02',
            'birth_place' => 'Bandung',
            'gender' => 'female',
            'address' => 'Jl. Manager No.2',
            'phone' => '081234567891',
            'personal_email' => 'manager.personal@example.com',
            'marital_status' => 'married',
            'nik' => '2234567890',
            'employee_id' => '2',
            'start_date' => '2021-02-02',
            'employment_status' => 'permanent',
            'office_location' => 'HQ',
            'supervisor' => null,
            'salary' => 8000000.00,
            'benefits' => 'Health, Transport',
            'bank_account' => '2234567890',
            'bank_name' => 'Bank BCA',
            'email_verified_at' => now(),
            'remember_token' => null,
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor Satu',
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
            'password' => bcrypt('password'),
            'position_id' => $positionSupervisor ? $positionSupervisor->id : null,
            'department_id' => $departmentHRD ? $departmentHRD->id : null,
            'full_name' => 'Supervisor Satu Fullname',
            'birth_date' => '1993-04-04',
            'birth_place' => 'Medan',
            'gender' => 'male',
            'address' => 'Jl. Supervisor No.4',
            'phone' => '081234567893',
            'personal_email' => 'supervisor.personal@example.com',
            'marital_status' => 'married',
            'nik' => '4234567890',
            'employee_id' => '4',
            'start_date' => '2023-04-04',
            'employment_status' => 'permanent',
            'office_location' => 'HQ',
            'supervisor' => null,
            'salary' => 6000000.00,
            'benefits' => 'Health, Transport',
            'bank_account' => '4234567890',
            'bank_name' => 'Bank BCA',
            'email_verified_at' => now(),
            'remember_token' => null,
        ]);

        $staff = User::create([
            'name' => 'Staff Satu',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'password' => bcrypt('password'),
            'position_id' => $positionStaff ? $positionStaff->id : null,
            'department_id' => $departmentHRD ? $departmentHRD->id : null,
            'full_name' => 'Staff Satu Fullname',
            'birth_date' => '1994-05-05',
            'birth_place' => 'Bali',
            'gender' => 'male',
            'address' => 'Jl. Staff No.5',
            'phone' => '081234567894',
            'personal_email' => 'staff.personal@example.com',
            'marital_status' => 'single',
            'nik' => '5234567890',
            'employee_id' => '5',
            'start_date' => '2024-05-05',
            'employment_status' => 'contract',
            'office_location' => 'HQ',
            'supervisor' => $supervisor->name,
            'salary' => 5000000.00,
            'benefits' => 'Health, Transport',
            'bank_account' => '5234567890',
            'bank_name' => 'Bank BCA',
            'email_verified_at' => now(),
            'remember_token' => null,
            'supervisor_id' => $supervisor->id,
        ]);

        // Integrasi ke supervisor_employees
        \App\Models\SupervisorEmployee::create([
            'supervisor_id' => $supervisor->id,
            'employee_id' => $staff->id,
        ]);
    }
}
