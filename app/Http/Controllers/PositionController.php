<?php
namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 10);
        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 1) == 1 ? 'asc' : 'desc';
        $searchField = $request->input('searchField', 'name');
        $searchValue = $request->input('searchValue', '');

        $query = Position::query();
        if ($searchValue && $searchField) {
            $query->where($searchField, 'like', "%$searchValue%");
        }
        $query->orderBy($sortField, $sortOrder);
        $positions = $query->paginate($rows, ['*'], 'page', $page);

        return Inertia::render('masterdata/position/index', [
            'positions' => $positions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        Position::create($request->only(['name', 'description']));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        $position = Position::findOrFail($id);
        $position->update($request->only(['name', 'description']));
        return redirect()->back();
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return redirect()->back();
    }
}
