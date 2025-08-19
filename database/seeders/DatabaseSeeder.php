<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            // Karyawan (Employee)
            User::factory()->create([
                'name' => 'Karyawan Satu',
                'email' => 'karyawan@example.com',
                'role' => 'karyawan',
            ]);

            // Manajer/Atasan (Manager/Supervisor)
            User::factory()->manager()->create([
                'name' => 'Manajer Satu',
                'email' => 'manajer@example.com',
            ]);

            // HR Generalist
            User::factory()->hrGeneralist()->create([
                'name' => 'HR Generalist',
                'email' => 'hrgeneralist@example.com',
            ]);

            // Payroll Specialist
            User::factory()->payrollSpecialist()->create([
                'name' => 'Payroll Specialist',
                'email' => 'payroll@example.com',
            ]);

            // Recruitment Specialist
            User::factory()->recruitmentSpecialist()->create([
                'name' => 'Recruitment Specialist',
                'email' => 'recruitment@example.com',
            ]);

            // Administrator Sistem (System Administrator)
            User::factory()->systemAdministrator()->create([
                'name' => 'Administrator Sistem',
                'email' => 'admin@example.com',
            ]);

            // Pimpinan/Eksekutif (Executive/Leadership)
            User::factory()->executive()->create([
                'name' => 'Eksekutif',
                'email' => 'eksekutif@example.com',
            ]);

            $this->call([
                LeaveSeeder::class,
                SupervisorSeeder::class,
                LeaveApprovalSeeder::class,
                SettingSeeder::class,
                WorkScheduleSeeder::class,
            ]);
    }
}
