<?php
namespace App\Http\Controllers;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceReviewController extends Controller
{
    public function index()
    {
        return PerformanceReview::where('user_id', Auth::id())->get();
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        $data = $request->validate([
            'result' => 'required|string',
            'date' => 'nullable|date',
        ]);
        $data['user_id'] = $user->id;
        return PerformanceReview::create($data);
    }
    public function update(Request $request, PerformanceReview $performanceReview)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        $this->authorize('update', $performanceReview);
        $data = $request->validate([
            'result' => 'required|string',
            'date' => 'nullable|date',
        ]);
        $performanceReview->update($data);
        return $performanceReview;
    }
    public function destroy(PerformanceReview $performanceReview)
    {
        $user = Auth::user();
        if ($user->role === 'karyawan') {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk karyawan'], 422);
        }
        $this->authorize('delete', $performanceReview);
        $performanceReview->delete();
        return response()->noContent();
    }
}
