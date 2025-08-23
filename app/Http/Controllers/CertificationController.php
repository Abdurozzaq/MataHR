<?php
namespace App\Http\Controllers;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    public function index()
    {
        return Certification::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date' => 'nullable|date',
            'issuer' => 'nullable|string',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return Certification::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($id) {
            $certification = Certification::findOrFail($id);
        } else {
            $certification = Certification::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'title' => 'required|string',
            'date' => 'nullable|date',
            'issuer' => 'nullable|string',
        ]);
        $certification->update($data);
        return $certification;
    }
    public function destroy(Certification $certification)
    {
        $certification->delete();
        return response()->noContent();
    }
}
