<?php
namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkSchedule::query();
        $searchField = $request->input('searchField', 'id');
        $searchValue = $request->input('searchValue', null);
        if ($searchValue !== null && $searchValue !== '') {
            if (in_array($searchField, ['id', 'day_of_week', 'min_work_duration'])) {
                $query->where($searchField, $searchValue);
            } else if ($searchField === 'is_workday') {
                // Support searchValue as boolean, string 'true'/'false', or 1/0
                $boolValue = null;
                if ($searchValue === true || $searchValue === 1 || $searchValue === '1' || $searchValue === 'true') {
                    $boolValue = 1;
                } else if ($searchValue === false || $searchValue === 0 || $searchValue === '0' || $searchValue === 'false') {
                    $boolValue = 0;
                }
                if ($boolValue !== null) {
                    $query->where('is_workday', $boolValue);
                }
            } else {
                $query->where($searchField, 'like', "%$searchValue%");
            }
        }
        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortOrder);
        $rows = $request->input('rows', 10);
        $workSchedules = $query->paginate($rows);

        return Inertia::render('masterdata/workSchedules/index', [
            'workSchedules' => [
                'data' => $workSchedules->items(),
                'total' => $workSchedules->total(),
                'current_page' => $workSchedules->currentPage(),
                'per_page' => $workSchedules->perPage(),
                'last_page' => $workSchedules->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'clock_in_start' => 'required',
            'clock_in_end' => 'required',
            'clock_out_start' => 'required',
            'clock_out_end' => 'required',
            'is_workday' => 'required|boolean',
            'min_work_duration' => 'nullable|integer',
        ]);
        WorkSchedule::create($data);
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'clock_in_start' => 'required',
            'clock_in_end' => 'required',
            'clock_out_start' => 'required',
            'clock_out_end' => 'required',
            'is_workday' => 'required|boolean',
            'min_work_duration' => 'nullable|integer',
        ]);
        $ws = WorkSchedule::findOrFail($id);
        $ws->update($data);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $ws = WorkSchedule::findOrFail($id);
        $ws->delete();
        return redirect()->back();
    }
}
