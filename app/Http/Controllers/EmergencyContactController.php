<?php
namespace App\Http\Controllers;
use App\Models\EmergencyContact;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        return EmergencyContact::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'relationship' => 'required|string',
            'phone' => 'required|string',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return EmergencyContact::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($id) {
            $emergencyContact = EmergencyContact::findOrFail($id);
        } else {
            $emergencyContact = EmergencyContact::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'name' => 'required|string',
            'relationship' => 'required|string',
            'phone' => 'required|string',
        ]);
        $emergencyContact->update($data);
        return $emergencyContact;
    }
    public function destroy(EmergencyContact $emergencyContact)
    {
        $emergencyContact->delete();
        return response()->noContent();
    }
}
