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
        ]);
        $data['user_id'] = Auth::id();
        return EmergencyContact::create($data);
    }
    public function update(Request $request, EmergencyContact $emergencyContact)
    {
        $this->authorize('update', $emergencyContact);
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
        $this->authorize('delete', $emergencyContact);
        $emergencyContact->delete();
        return response()->noContent();
    }
}
