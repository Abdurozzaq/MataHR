<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkScheduleSeeder extends Seeder
{
    public function run()
    {
    // Hapus data lama agar tidak duplikat
    DB::table('work_schedules')->truncate();
    $data = [];
        // 0=Sunday, 1=Monday, ..., 6=Saturday
        foreach (range(0,6) as $day) {
            $isWorkday = in_array($day, [1,2,3,4,5]); // Senin-Jumat
            $data[] = [
                'day_of_week' => $day,
                'clock_in_start' => $isWorkday ? '07:00:00' : null,
                'clock_in_end' => $isWorkday ? '09:00:00' : null,
                'clock_out_start' => $isWorkday ? '16:00:00' : null,
                'clock_out_end' => $isWorkday ? '18:00:00' : null,
                'is_workday' => $isWorkday,
                'min_work_duration' => $isWorkday ? 480 : null, // 8 jam
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('work_schedules')->insert($data);
    }
}
