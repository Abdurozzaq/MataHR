<?php
namespace App\Http\Controllers;
use App\Models\CareerPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerPlanController extends Controller
{
    public function index()
    {
        return CareerPlan::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|string',
            'target_date' => 'nullable|date',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return CareerPlan::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($id) {
            $careerPlan = CareerPlan::findOrFail($id);
        } else {
            $careerPlan = CareerPlan::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'plan' => 'required|string',
            'target_date' => 'nullable|date',
        ]);
        $careerPlan->update($data);
        return $careerPlan;
    }
    public function destroy(CareerPlan $careerPlan)
    {
        $careerPlan->delete();
        return response()->noContent();
    }
}
