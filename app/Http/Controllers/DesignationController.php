<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $query = Designation::with('department');

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhereHas('department', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
        }

        if ($request->has('sort')) {
            $direction = $request->direction == 'asc' ? 'asc' : 'desc';
            
            if ($request->sort == 'name') {
                $query->orderBy('name', $direction);
            } elseif ($request->sort == 'department') {
                $query->join('departments', 'designations.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', $direction)
                    ->select('designations.*');
            }
        }

        $designations = $query->paginate(10);

        return view('designations.index', compact('designations'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('designations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        Designation::create($validated);

        return redirect()->route('designations.index')->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        $departments = Department::all();
        return view('designations.edit', compact('designation', 'departments'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation->update($validated);

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }

    public function getByDepartment($departmentId)
    {
        $designations = Designation::where('department_id', $departmentId)->get();
        return response()->json($designations);
    }
}