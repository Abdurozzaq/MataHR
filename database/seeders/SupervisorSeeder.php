<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh: Buat 2 supervisor dan 4 bawahan
        DB::table('users')->insert([
            [
                'name' => 'Supervisor 1',
                'email' => 'supervisor1@example.com',
                'password' => bcrypt('password'),
                'role' => 'supervisor',
                'employee_id' => 1001,
                'supervisor_id' => null,
            ],
            [
                'name' => 'Supervisor 2',
                'email' => 'supervisor2@example.com',
                'password' => bcrypt('password'),
                'role' => 'supervisor',
                'employee_id' => 1002,
                'supervisor_id' => null,
            ],
            [
                'name' => 'Karyawan A',
                'email' => 'karyawanA@example.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
                'employee_id' => 2001,
                'supervisor_id' => 1001,
            ],
            [
                'name' => 'Karyawan B',
                'email' => 'karyawanB@example.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
                'employee_id' => 2002,
                'supervisor_id' => 1001,
            ],
            [
                'name' => 'Karyawan C',
                'email' => 'karyawanC@example.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
                'employee_id' => 2003,
                'supervisor_id' => 1002,
            ],
            [
                'name' => 'Karyawan D',
                'email' => 'karyawanD@example.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
                'employee_id' => 2004,
                'supervisor_id' => 1002,
            ],
        ]);
    }
}
