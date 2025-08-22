<?php
namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();
        $searchField = $request->input('searchField', 'name');
        $searchValue = $request->input('searchValue', '');
        if ($searchValue) {
            $query->where($searchField, 'like', "%$searchValue%");
        }
        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortOrder);
        $rows = $request->input('rows', 10);
        $departments = $query->paginate($rows);
        return inertia('masterdata/department/index', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Department::create($data);
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $department = Department::findOrFail($id);
        $department->update($data);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        return redirect()->back();
    }
}
