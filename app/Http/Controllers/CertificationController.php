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
        ]);
        $data['user_id'] = Auth::id();
        return Certification::create($data);
    }
    public function update(Request $request, Certification $certification)
    {
        $this->authorize('update', $certification);
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
        $this->authorize('delete', $certification);
        $certification->delete();
        return response()->noContent();
    }
}
