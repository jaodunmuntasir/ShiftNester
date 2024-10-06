<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Skill;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('sort')) {
            $direction = $request->direction == 'asc' ? 'asc' : 'desc';
            
            switch ($request->sort) {
                case 'name':
                    $query->orderBy('name', $direction);
                    break;
                case 'department':
                    $query->join('departments', 'employees.department_id', '=', 'departments.id')
                        ->orderBy('departments.name', $direction)
                        ->select('employees.*');
                    break;
                case 'designation':
                    $query->join('designations', 'employees.designation_id', '=', 'designations.id')
                        ->orderBy('designations.name', $direction)
                        ->select('employees.*');
                    break;
                case 'hire_date':
                    $query->orderBy('hire_date', $direction);
                    break;
            }
        }

        $employees = $query->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::doesntHave('employee')->get();
        $skills = Skill::all();
        $departments = Department::all();
        return view('employees.create', compact('users', 'skills', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'hire_date' => 'required|date',
            'skills' => 'array',
            'skills.*' => 'exists:skills,id',
            'skill_ratings.*' => 'nullable|integer|min:1|max:5',
            'skill_has.*' => 'nullable|boolean',
        ]);

        $user = User::findOrFail($validated['user_id']);
        
        $employee = new Employee($validated);
        $employee->name = $user->name;
        $employee->email = $user->email;
        $employee->save();

        if ($request->has('skills')) {
            foreach ($request->skills as $skillId) {
                $rating = $request->input("skill_ratings.$skillId");
                $has_skill = $request->input("skill_has.$skillId", false);
                $employee->skills()->attach($skillId, [
                    'rating' => $rating,
                    'has_skill' => $has_skill,
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $skills = Skill::all();
        $departments = Department::all();
        $designations = Designation::where('department_id', $employee->department_id)->get();
        return view('employees.edit', compact('employee', 'skills', 'departments', 'designations'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'hire_date' => 'required|date',
            'skills' => 'array',
            'skills.*' => 'exists:skills,id',
            'skill_ratings.*' => 'nullable|integer|min:1|max:5',
            'skill_has.*' => 'nullable|boolean',
        ]);

        $employee->update($validated);

        $employee->skills()->detach();

        if ($request->has('skills')) {
            foreach ($request->skills as $skillId) {
                $rating = $request->input("skill_ratings.$skillId");
                $has_skill = $request->input("skill_has.$skillId", false);
                $employee->skills()->attach($skillId, [
                    'rating' => $rating,
                    'has_skill' => $has_skill,
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}