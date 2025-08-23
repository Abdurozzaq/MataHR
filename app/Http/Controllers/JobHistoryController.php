<?php
namespace App\Http\Controllers;
use App\Models\JobHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobHistoryController extends Controller
{
    public function index()
    {
        return JobHistory::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        $data = $request->validate([
            'position' => 'required|string',
            'department' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'type' => 'nullable|string',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return JobHistory::create($data);
    }
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        // Jika ada parameter id, ambil JobHistory berdasarkan id
        if ($id) {
            $jobHistory = JobHistory::findOrFail($id);
        } else {
            // Jika tidak ada id, ambil JobHistory milik user yang sedang login
            $jobHistory = JobHistory::where('user_id', $user->id)->firstOrFail();
        }
        $data = $request->validate([
            'position' => 'required|string',
            'department' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'type' => 'nullable|string',
        ]);
        $jobHistory->update($data);
        return $jobHistory;
    }
    public function destroy(JobHistory $jobHistory)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        $jobHistory->delete();
        return response()->noContent();
    }
}
