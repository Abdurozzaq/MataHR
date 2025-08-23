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
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return WorkGoal::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($id) {
            $workGoal = WorkGoal::findOrFail($id);
        } else {
            $workGoal = WorkGoal::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'goal' => 'required|string',
            'target_date' => 'nullable|date',
        ]);
        $workGoal->update($data);
        return $workGoal;
    }
    public function destroy(WorkGoal $workGoal)
    {
        $workGoal->delete();
        return response()->noContent();
    }
}
