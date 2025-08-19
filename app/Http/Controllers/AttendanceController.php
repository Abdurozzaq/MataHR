<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Events\UserClockedIn;
use App\Events\UserClockedOut;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    // Geofencing: kantor (diambil dari database Setting)
    private $officeLat;
    private $officeLon;
    private $officeName;
    private $radius;

    private function getTodaySchedule()
    {
        $dayOfWeek = now()->dayOfWeek; // 0=Sunday, 1=Monday, ...
        return \App\Models\WorkSchedule::where('day_of_week', $dayOfWeek)->first();
    }
 

    public function __construct()
    {
        $this->officeLat = \App\Models\Setting::get('office_lat', '-6.2546847');
        $this->officeLon = \App\Models\Setting::get('office_lon', '106.624721');
        $this->officeName = \App\Models\Setting::get('office_name', 'Kantor Pusat');
        $this->radius = (int) \App\Models\Setting::get('office_radius', 100);
    }

    public function clockIn(Request $request) {
        $schedule = $this->getTodaySchedule();
        if (!$schedule || !$schedule->is_workday) {
            return response()->json(['error' => 'Hari ini bukan hari kerja.'], 422);
        }
        $now = now();
    
        $user = Auth::user();
        $userLat = $request->latitude;
        $userLon = $request->longitude;
        $distance = $this->haversine($this->officeLat, $this->officeLon, $userLat, $userLon);
        if ($distance > $this->radius) {
            return response()->json(['error' => 'Anda berada di luar area kantor.'], 422);
        }
        // Cek sudah clock in hari ini
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_at', $today)
            ->first();
            if ($attendance) {
                // Ambil min_work_duration dari jadwal hari ini (dalam menit)
                $dayOfWeek = now()->dayOfWeek;
                $workSchedule = \App\Models\WorkSchedule::where('day_of_week', $dayOfWeek)->first();
                $minWorkDuration = $workSchedule ? ($workSchedule->min_work_duration * 60) : 0;
                $clockIn = \Carbon\Carbon::parse($attendance->clock_in_at);
                $nowTime = now();
                $diffSec = $nowTime->diffInSeconds($clockIn);
                if ($diffSec > $minWorkDuration) {
                    return response()->json(['error' => 'Anda sudah clock in hari ini.'], 422);
                }
            }
            // Ambil timezone kantor dari Setting, default Asia/Jakarta
            $officeTimezone = \App\Models\Setting::get('office_timezone', 'Asia/Jakarta');
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'clock_in_at' => now($officeTimezone),
                'latitude' => $userLat,
                'longitude' => $userLon,
            ]);

        // Ambil data absensi user hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_at', $today)
            ->first();
        $userLocationName = null;
        if ($userLat && $userLon) {
            $userLocationName = $this->getLocationName($userLat, $userLon);
        }
        return response()->json([
            'success' => 'Clock in berhasil!',
            'attendance' => $attendance,
            'office_name' => $this->officeName,
            'office_latitude' => (string) $this->officeLat,
            'office_longitude' => (string) $this->officeLon,
            'user_location_name' => $userLocationName,
            'user_latitude' => (string) $userLat,
            'user_longitude' => (string) $userLon
        ]);
    }
    
    // <-- Add this closing brace to properly end the clockIn function

    public function clockOut(Request $request) {
        $schedule = $this->getTodaySchedule();
        if (!$schedule || !$schedule->is_workday) {
            return response()->json(['error' => 'Hari ini bukan hari kerja.'], 422);
        }
        // Tidak perlu cek jam clock out, bisa clock out kapan saja pada hari kerja
   
        $user = Auth::user();
        $userLat = $request->latitude;
        $userLon = $request->longitude;
        $distance = $this->haversine($this->officeLat, $this->officeLon, $userLat, $userLon);
        if ($distance > $this->radius) {
            return response()->json(['error' => 'Anda berada di luar area kantor.'], 422);
        }
        $today = now()->toDateString();
        // Ambil attendance yang clock_out_at masih null (belum clock out)
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_at', $today)
            ->whereNull('clock_out_at')
            ->first();
        if (!$attendance) {
            return response()->json(['error' => 'Anda belum clock in hari ini atau sudah clock out.'], 422);
        }

        $officeTimezone = \App\Models\Setting::get('office_timezone', 'Asia/Jakarta');
        $attendance->clock_out_at = now($officeTimezone);
        $attendance->latitude = $userLat;
        $attendance->longitude = $userLon;
        $attendance->save();
        // Ambil data absensi user hari ini (terakhir clock in)
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_at', $today)
            ->orderByDesc('clock_in_at')
            ->first();
        $userLocationName = null;
        if ($userLat && $userLon) {
            $userLocationName = $this->getLocationName($userLat, $userLon);
        }
        return response()->json([
            'success' => 'Clock out berhasil!',
            'attendance' => $attendance,
            'office_name' => $this->officeName,
            'office_latitude' => $this->officeLat,
            'office_longitude' => $this->officeLon,
            'user_location_name' => $userLocationName,
            'user_latitude' => $userLat,
            'user_longitude' => $userLon
        ]);
    }

    public function today()
    {
        $today = now()->toDateString();
        $attendances = Attendance::with('user')
            ->whereDate('clock_in_at', $today)
            ->get();

        // Hitung durasi kerja user hari ini
        $user = Auth::user();
        $myAttendance = $attendances->where('user_id', $user->id)->first();
        $workDuration = null;
        if ($myAttendance && $myAttendance->clock_in_at) {
            $clockIn = \Carbon\Carbon::parse($myAttendance->clock_in_at);
            $clockOut = $myAttendance->clock_out_at ? \Carbon\Carbon::parse($myAttendance->clock_out_at) : now();
            $diffSec = $clockOut->diffInSeconds($clockIn);
            $hours = floor($diffSec / 3600);
            $minutes = floor(($diffSec % 3600) / 60);
            $seconds = $diffSec % 60;
            $workDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // Ambil min_work_duration dari jadwal hari ini (dalam detik)
        $dayOfWeek = now()->dayOfWeek; // 0=Sunday
        $workSchedule = \App\Models\WorkSchedule::where('day_of_week', $dayOfWeek)->first();
        $minWorkDuration = $workSchedule ? ($workSchedule->min_work_duration * 60) : 0;

        // Ambil lokasi user dari request jika tersedia
        $userLat = request()->latitude;
        $userLon = request()->longitude;
        $userLocationName = null;
        if ($userLat && $userLon) {
            $userLocationName = $this->getLocationName($userLat, $userLon);
        }

        return Inertia::render('absent/index', [
            'attendances' => $attendances,
            'office_name' => $this->officeName,
            'office_latitude' => $this->officeLat,
            'office_longitude' => $this->officeLon,
            'user_location_name' => $userLocationName,
            'user_latitude' => $userLat,
            'user_longitude' => $userLon,
            'work_duration' => $workDuration,
            'min_work_duration' => $minWorkDuration,
        ]);
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    // Mendapatkan nama lokasi dari koordinat (reverse geocoding Nominatim)
    private function getLocationName($lat, $lon)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=16&addressdetails=1";
        $opts = [
            "http" => [
                "header" => "User-Agent: MataHR/1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $json = @file_get_contents($url, false, $context);
        if ($json === false) return 'Tidak diketahui';
        $data = json_decode($json, true);
        return $data['display_name'] ?? 'Tidak diketahui';
    }
}
