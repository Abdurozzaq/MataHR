<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\CareerPlan;
use App\Models\Certification;
use App\Models\EmergencyContact;
use App\Models\JobHistory;
use App\Models\Leave;
use App\Models\Training;
use App\Models\WorkGoal;
use App\Models\WorkSchedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Statistik Kehadiran
            $attendanceSummary = [
                // Jika ada minimal satu clock_in di hari ini, maka 'Hadir'
                'today' => Attendance::where('user_id', $user->id)
                    ->whereDate('clock_in_at', now())
                    ->count() > 0 ? 'Hadir' : 'Tidak Hadir',
            // Hitung jumlah hari unik user melakukan clock_in di bulan ini
            'monthTotal' => Attendance::where('user_id', $user->id)
                ->whereMonth('clock_in_at', now()->month)
                ->selectRaw('DATE(clock_in_at) as hari')
                ->groupBy('hari')
                ->get()
                ->count(),
            'clockIn' => Attendance::where('user_id', $user->id)->whereDate('clock_in_at', now())->value('clock_in_at') ?? '-',
            'clockOut' => Attendance::where('user_id', $user->id)->whereDate('clock_out_at', now())->value('clock_out_at') ?? '-',
        ];

        // Statistik Jam Kerja
        $attendancesToday = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_at', now())
            ->get();
        $attendancesWeek = Attendance::where('user_id', $user->id)
            ->whereBetween('clock_in_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();
        $attendancesMonth = Attendance::where('user_id', $user->id)
            ->whereMonth('clock_in_at', now()->month)
            ->get();

        function sumWorkHours($attendances) {
            $total = 0;
            foreach ($attendances as $a) {
                if ($a->clock_in_at && $a->clock_out_at) {
                    $in = strtotime($a->clock_in_at);
                    $out = strtotime($a->clock_out_at);
                    $diff = ($out - $in) / 3600;
                    if ($diff > 0) $total += $diff;
                }
            }
            return round($total, 2);
        }

        $workHourStats = [
            'today' => sumWorkHours($attendancesToday),
            'week' => sumWorkHours($attendancesWeek),
            'month' => sumWorkHours($attendancesMonth),
        ];

        // Statistik Cuti
        $taken = Leave::where('employee_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
        // Sisa cuti, misal field remaining_days, jika tidak ada default 12 - taken
        $remaining = method_exists($user, 'remaining_days') ? $user->remaining_days : (12 - $taken);
        $leaveStats = [
            'taken' => $taken,
            'remaining' => $remaining < 0 ? 0 : $remaining,
        ];

        // Jadwal Kerja - format agar lebih informatif
        $days = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        ];
        $workSchedulesRaw = WorkSchedule::orderBy('day_of_week')->get();
        $workSchedules = [];
        foreach ($workSchedulesRaw as $ws) {
            $workSchedules[] = [
                'id' => $ws->id,
                'day' => $days[$ws->day_of_week],
                'clock_in' => $ws->clock_in_start && $ws->clock_in_end ? ($ws->clock_in_start . ' - ' . $ws->clock_in_end) : '-',
                'clock_out' => $ws->clock_out_start && $ws->clock_out_end ? ($ws->clock_out_start . ' - ' . $ws->clock_out_end) : '-',
                'is_workday' => $ws->is_workday,
                'min_work_duration' => $ws->min_work_duration ? round($ws->min_work_duration / 60, 2) : 0, // jam
            ];
        }

        // Target jam kerja
        $todayIdx = now()->dayOfWeek; // 0=Sunday
        $targetToday = isset($workSchedules[$todayIdx]) ? $workSchedules[$todayIdx]['min_work_duration'] : 0;
        $targetWeek = array_sum(array_map(fn($ws) => $ws['min_work_duration'], $workSchedules));
        $targetMonth = $targetWeek * 4; // asumsi 4 minggu per bulan
        $workHourTargets = [
            'today' => $targetToday,
            'week' => $targetWeek,
            'month' => $targetMonth,
        ];

        return response()->json([
            'attendanceSummary' => $attendanceSummary,
            'leaveStats' => $leaveStats,
            'workSchedules' => $workSchedules,
            'workHourStats' => $workHourStats,
            'workHourTargets' => $workHourTargets,
        ]);
    }
}
