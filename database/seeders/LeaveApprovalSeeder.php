<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveApprovalSeeder extends Seeder
{
    public function run(): void
    {
        // 5 cuti karyawan bawahan Supervisor 1 (employee_id: 1001)
        $leaves = [
            [
                'employee_id' => 2001,
                'employee_name' => 'Karyawan A',
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->addDays(1)->toDateString(),
                'end_date' => Carbon::now()->addDays(3)->toDateString(),
                'reason' => 'Liburan keluarga',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2002,
                'employee_name' => 'Karyawan B',
                'leave_type' => 'sick',
                'start_date' => Carbon::now()->addDays(2)->toDateString(),
                'end_date' => Carbon::now()->addDays(4)->toDateString(),
                'reason' => 'Sakit demam',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2001,
                'employee_name' => 'Karyawan A',
                'leave_type' => 'important',
                'start_date' => Carbon::now()->addDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'reason' => 'Urusan keluarga penting',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2002,
                'employee_name' => 'Karyawan B',
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->addDays(10)->toDateString(),
                'end_date' => Carbon::now()->addDays(12)->toDateString(),
                'reason' => 'Liburan sekolah anak',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2001,
                'employee_name' => 'Karyawan A',
                'leave_type' => 'sick',
                'start_date' => Carbon::now()->addDays(15)->toDateString(),
                'end_date' => Carbon::now()->addDays(16)->toDateString(),
                'reason' => 'Sakit flu',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        DB::table('leaves')->insert($leaves);
    }
}
