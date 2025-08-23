<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Carbon;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        // Ambil user dari UserSeeder
        $userManager = User::where('email', 'manager@example.com')->first();
        $userStaff = User::where('email', 'staff@example.com')->first();
        $userSupervisor = User::where('email', 'supervisor@example.com')->first();

        // Seeder cuti untuk Manager
        \App\Models\Leave::create([
            'employee_id' => $userManager ? $userManager->employee_id : null,
            'employee_name' => $userManager ? $userManager->name : 'Manager Satu',
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
        ]);

        // Seeder cuti untuk Staff (disetujui Supervisor)
        \App\Models\Leave::create([
            'employee_id' => $userStaff ? $userStaff->employee_id : null,
            'employee_name' => $userStaff ? $userStaff->name : 'Staff Satu',
            'leave_type' => 'sick',
            'start_date' => $now->copy()->subDays(1)->toDateString(),
            'end_date' => $now->copy()->addDays(1)->toDateString(),
            'reason' => 'Demam tinggi',
            'status' => 'approved',
            'approval_note' => 'Semoga lekas sembuh',
            'approved_at' => $now->copy()->subDay(),
            'rejected_at' => null,
            'processed_by' => $userSupervisor ? $userSupervisor->id : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
