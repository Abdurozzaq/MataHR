<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        // Ambil user karyawan
        $user1 = DB::table('users')->where('email', 'karyawan@example.com')->first();
        $user2 = DB::table('users')->where('email', 'siti@example.com')->first();
        $user3 = DB::table('users')->where('email', 'joko@example.com')->first();

        // Jika user belum ada, buat user dummy
        if (!$user2) {
            $user2Id = DB::table('users')->insertGetId([
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'password' => bcrypt('password'),
                'role' => 'karyawan',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $user2 = DB::table('users')->where('id', $user2Id)->first();
        }
        if (!$user3) {
            $user3Id = DB::table('users')->insertGetId([
                'name' => 'Joko Widodo',
                'email' => 'joko@example.com',
                'password' => bcrypt('password'),
                'role' => 'karyawan',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $user3 = DB::table('users')->where('id', $user3Id)->first();
        }

        DB::table('leaves')->insert([
            [
                'employee_id' => $user1 ? $user1->id : null,
                'employee_name' => $user1 ? $user1->name : 'Karyawan Satu',
                'leave_type' => 'annual',
                'start_date' => $now->copy()->addDays(2)->toDateString(),
                'end_date' => $now->copy()->addDays(5)->toDateString(),
                'reason' => 'Liburan keluarga',
                'status' => 'pending',
                'approval_note' => null,
                'approved_at' => null,
                'rejected_at' => null,
                'processed_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => $user2->id,
                'employee_name' => $user2->name,
                'leave_type' => 'sick',
                'start_date' => $now->copy()->subDays(1)->toDateString(),
                'end_date' => $now->copy()->addDays(1)->toDateString(),
                'reason' => 'Demam tinggi',
                'status' => 'approved',
                'approval_note' => 'Semoga lekas sembuh',
                'approved_at' => $now->copy()->subDay(),
                'rejected_at' => null,
                'processed_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => $user3->id,
                'employee_name' => $user3->name,
                'leave_type' => 'important',
                'start_date' => $now->copy()->addDays(10)->toDateString(),
                'end_date' => $now->copy()->addDays(12)->toDateString(),
                'reason' => 'Acara keluarga penting',
                'status' => 'rejected',
                'approval_note' => 'Tidak bisa karena deadline proyek',
                'approved_at' => null,
                'rejected_at' => $now->copy()->addDays(1),
                'processed_by' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
