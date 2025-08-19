<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function reverseGeocode(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        if (!$lat || !$lon) {
            return response()->json(['location_name' => 'Tidak diketahui']);
        }
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=16&addressdetails=1";
        $opts = [
            "http" => [
                "header" => "User-Agent: MataHR/1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $json = @file_get_contents($url, false, $context);
        if ($json === false) return response()->json(['location_name' => 'Tidak diketahui']);
        $data = json_decode($json, true);
        return response()->json(['location_name' => $data['display_name'] ?? 'Tidak diketahui']);
    }
}
