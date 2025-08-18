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
        ]);
        $data['user_id'] = Auth::id();
        return CareerPlan::create($data);
    }
    public function update(Request $request, CareerPlan $careerPlan)
    {
        $this->authorize('update', $careerPlan);
        $data = $request->validate([
            'plan' => 'required|string',
            'target_date' => 'nullable|date',
        ]);
        $careerPlan->update($data);
        return $careerPlan;
    }
    public function destroy(CareerPlan $careerPlan)
    {
        $this->authorize('delete', $careerPlan);
        $careerPlan->delete();
        return response()->noContent();
    }
}
