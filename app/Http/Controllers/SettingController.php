<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', [
            'office_lat', 'office_lon', 'office_name', 'office_radius'
        ])->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->only(['office_lat', 'office_lon', 'office_name', 'office_radius']);
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    return redirect()->route('global-setting.index')->with('success', 'Settings updated successfully');
    }
}
