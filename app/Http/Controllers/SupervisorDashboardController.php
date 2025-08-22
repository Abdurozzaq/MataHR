<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Inertia\Inertia;

class SupervisorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $supervisorId = auth()->user()->employee_id;

        // Ambil anak buah supervisor
        $subordinates = User::where('supervisor_id', $supervisorId)->get();

        // Ambil absensi anak buah untuk bulan berjalan
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $dates = collect(range(1, Carbon::parse($month)->daysInMonth))
            ->map(fn($d) => Carbon::parse($month)->day($d)->format('Y-m-d'));

        $attendanceData = [];
        foreach ($dates as $date) {
            $daily = [];
            foreach ($subordinates as $sub) {
                $attendance = Attendance::where('user_id', $sub->id)
                    ->whereDate('clock_in_at', $date)
                    ->first();

                $status = 'not_clocked_in';
                if ($attendance) {
                    if ($attendance->clock_out_at) {
                        $status = 'clocked_out';
                    } elseif ($attendance->clock_in_at) {
                        $status = 'clocked_in';
                    }
                }
                $daily[] = [
                    'id' => $sub->id,
                    'employee_id' => $sub->employee_id,
                    'name' => $sub->name,
                    'department' => $sub->department,
                    'avatar' => $sub->avatar ?? null,
                    'status' => $status,
                    'date' => $date,
                ];
            }
            $attendanceData[] = [
                'date' => $date,
                'subordinates' => $daily,
            ];
        }

        // Summary hari ini
        $today = Carbon::now()->format('Y-m-d');
        $todaySummary = [
            'totalSubordinates' => $subordinates->count(),
            'totalClockedIn' => $attendanceData[Carbon::now()->day - 1]['subordinates']
                ? collect($attendanceData[Carbon::now()->day - 1]['subordinates'])->where('status', 'clocked_in')->count()
                : 0,
            'totalNotClockedIn' => $attendanceData[Carbon::now()->day - 1]['subordinates']
                ? collect($attendanceData[Carbon::now()->day - 1]['subordinates'])->where('status', 'not_clocked_in')->count()
                : 0,
            'totalClockedOut' => $attendanceData[Carbon::now()->day - 1]['subordinates']
                ? collect($attendanceData[Carbon::now()->day - 1]['subordinates'])->where('status', 'clocked_out')->count()
                : 0,
        ];

        return Inertia::render('absent/dashboardSupervisor', [
            'summary' => $todaySummary,
            'calendarDays' => $attendanceData,
            'breadcrumbs' => [['label' => 'Dashboard']],
        ]);
    }
}