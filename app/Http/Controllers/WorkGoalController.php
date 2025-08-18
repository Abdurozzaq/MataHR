<?php
namespace App\Http\Controllers;
use App\Models\WorkGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkGoalController extends Controller
{
    public function index()
    {
        return WorkGoal::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'goal' => 'required|string',
            'target_date' => 'nullable|date',
        ]);
        $data['user_id'] = Auth::id();
        return WorkGoal::create($data);
    }
    public function update(Request $request, WorkGoal $workGoal)
    {
        $this->authorize('update', $workGoal);
        $data = $request->validate([
            'goal' => 'required|string',
            'target_date' => 'nullable|date',
        ]);
        $workGoal->update($data);
        return $workGoal;
    }
    public function destroy(WorkGoal $workGoal)
    {
        $this->authorize('delete', $workGoal);
        $workGoal->delete();
        return response()->noContent();
    }
}
