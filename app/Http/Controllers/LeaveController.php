<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class LeaveController extends Controller
{
    /**
     * Menampilkan cuti bawahan supervisor
     */
    public function subordinate(Request $request)
    {
        $user = $request->user();
        // Ambil employee_id supervisor
        $supervisorEmployeeId = $user->employee_id ?? $user->id;

        // Query cuti dari bawahan
        $query = \App\Models\Leave::whereHas('employee', function($q) use ($supervisorEmployeeId) {
            $q->where('supervisor_id', $supervisorEmployeeId);
        });

        // Search
        if ($request->searchField && $request->searchValue) {
            $query->where($request->searchField, 'like', '%' . $request->searchValue . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('approved_at')->whereNull('rejected_at');
            } elseif ($request->status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->status === 'rejected') {
                $query->whereNotNull('rejected_at');
            }
        }

        // Sorting
        $sortField = $request->get('sortField', 'created_at');
        $sortOrder = $request->get('sortOrder', -1) == -1 ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $leaves = $query->paginate($request->get('rows', 10), ['*'], 'page', $request->get('page', 1));

        return Inertia::render('leave/manageCuti', [
            'leaves' => $leaves,
        ]);
    }
    public function index(Request $request)
    {
        $user = $request->user();
        $query = DB::table('leaves')
            ->select('*')
            ->where('employee_id', $user->id);


        // Search
        if ($request->searchField && $request->searchValue) {
            $query->where($request->searchField, 'like', '%' . $request->searchValue . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('approved_at')->whereNull('rejected_at');
            } elseif ($request->status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->status === 'rejected') {
                $query->whereNotNull('rejected_at');
            }
        }

        // Sorting
        $sortField = $request->get('sortField', 'created_at');
        $sortOrder = $request->get('sortOrder', -1) == -1 ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $leaves = $query->paginate($request->get('rows', 10), ['*'], 'page', $request->get('page', 1));

        return Inertia::render('leave/index', [
            'leaves' => $leaves,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string|max:255',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);
        $validator->validate();

        $user = $request->user();
        $id = DB::table('leaves')->insertGetId([
            'employee_id' => $user->id,
            'employee_name' => $request->employee_name,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }
    
        public function approve($id)
        {
        $leave = \App\Models\Leave::findOrFail($id);
        $leave->approved_at = now();
        $leave->rejected_at = null;
        $leave->approval_note = request('approval_note');
        $leave->save();

        return redirect()->back()->with('success', 'Cuti berhasil disetujui.');
        }

        public function reject($id)
        {
        $leave = \App\Models\Leave::findOrFail($id);
        $leave->rejected_at = now();
        $leave->approved_at = null;
        $leave->approval_note = request('approval_note');
        $leave->save();

        return redirect()->back()->with('success', 'Cuti berhasil ditolak.');
        }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string|max:255',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);
        $validator->validate();

        $user = $request->user();
        DB::table('leaves')->where('id', $id)->where('employee_id', $user->id)->update([
            'employee_name' => $request->employee_name,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
    $user = request()->user();
    DB::table('leaves')->where('id', $id)->where('employee_id', $user->id)->delete();
        return redirect()->back();
    }
}
