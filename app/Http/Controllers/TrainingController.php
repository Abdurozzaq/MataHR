<?php
namespace App\Http\Controllers;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index()
    {
        return Training::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date' => 'nullable|date',
            'provider' => 'nullable|string',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return Training::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($id) {
            $training = Training::findOrFail($id);
        } else {
            $training = Training::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'title' => 'required|string',
            'date' => 'nullable|date',
            'provider' => 'nullable|string',
        ]);
        $training->update($data);
        return $training;
    }
    public function destroy(Training $training)
    {
        $training->delete();
        return response()->noContent();
    }
}
