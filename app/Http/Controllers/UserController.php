<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\SupervisorEmployee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::with([
            'department',
            'position',
            'supervisor',
            'emergencyContacts',
            'jobHistories',
            'workGoals',
            'trainings',
            'certifications',
            'careerPlans',
        ])->findOrFail($id);
        return response()->json($user);
    }

    public function index(Request $request)
    {
        $query = User::query()->with(['department', 'position', 'supervisor']);
        // Search
        if ($request->searchField && $request->searchValue) {
            $field = $request->searchField;
            $value = $request->searchValue;
            if (in_array($field, ['department_id', 'position_id', 'supervisor_id'])) {
                $query->where($field, $value);
            } else {
                $query->where($field, 'like', "%$value%");
            }
        }
        // Sorting
        if ($request->sortField) {
            $query->orderBy($request->sortField, $request->sortOrder == -1 ? 'desc' : 'asc');
        }
        // Pagination
        $rows = $request->rows ?? 10;
        $users = $query->paginate($rows);
        // Relasi
        $departments = Department::all();
        $positions = Position::all();
        $supervisors = User::where('role', '!=', 'karyawan')->get();
        return Inertia::render('userManagement/index', [
            'users' => $users,
            'departments' => $departments,
            'positions' => $positions,
            'supervisors' => $supervisors,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (empty($data['employee_id'])) {
            $data['employee_id'] = User::generateEmployeeId();
        }
        // Validasi konfirmasi password
        if (empty($data['password']) || $data['password'] !== $data['password_confirmation']) {
            return redirect()->back()->with('error', 'Password dan konfirmasi password harus sama dan tidak boleh kosong.');
        }
        $data['password'] = bcrypt($data['password']);
        unset($data['password_confirmation']);
        $user = User::create($data);
        // TODO: Simpan emergency_contacts, job_histories, work_goals
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();
        // Jika password diisi, hash dan validasi konfirmasi
        if (!empty($data['password'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                return redirect()->back()->with('error', 'Password dan konfirmasi password harus sama.');
            }
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation']);
        $user->update($data);
        // TODO: Update emergency_contacts, job_histories, work_goals
        return redirect()->back();
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back();
    }
}
