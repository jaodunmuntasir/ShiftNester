<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftPreference;
use App\Models\Employee;
use App\Models\ShiftRequirement;
use App\Models\PublishedShift;
use App\Models\Department;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function viewShiftPreferences()
    {
        $shifts = Shift::with(['preferences.employee'])->get();
        return view('admin.shift_preferences', compact('shifts'));
    }

    public function generateRoster()
    {
        $rosterGenerator = new RosterGenerator();
        $generatedRoster = $rosterGenerator->generate();

        $shifts = Shift::with(['requirements.department', 'requirements.designation'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        session(['generated_roster' => $generatedRoster]);

        return view('admin.generated_roster', compact('generatedRoster', 'shifts'));
    }

    public function publishShifts(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.confirm_publish_shifts');
        }

        $roster = session('generated_roster');

        if (!$roster) {
            return redirect()->route('admin.view_shift_preferences')->with('error', 'No roster generated. Please generate a roster first.');
        }

        $publisher = new RosterPublisher();
        $publisher->publish($roster);

        session()->forget('generated_roster');

        return redirect()->route('shifts.published')->with('success', 'Shifts have been published successfully.');
    }

    public function viewPublishedShifts()
    {
        $publishedShifts = Shift::with(['publishedShifts.employee', 'publishedShifts.department', 'publishedShifts.designation'])
            ->whereHas('publishedShifts')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('shifts.published', compact('publishedShifts'));
    }
}

class RosterGenerator
{
    private $shifts;
    private $roster = [];
    private $employeeShiftCounts = [];

    public function generate()
    {
        $this->shifts = Shift::with(['requirements.department', 'requirements.designation', 'preferences.employee'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        foreach ($this->shifts as $shift) {
            $this->allocateEmployeesForShift($shift);
        }

        return $this->roster;
    }

    private function allocateEmployeesForShift($shift)
    {
        $this->roster[$shift->id] = [];

        foreach ($shift->requirements as $requirement) {
            $this->allocateEmployeesForRequirement($shift, $requirement);
        }
    }

    private function allocateEmployeesForRequirement($shift, $requirement)
    {
        $allocatedCount = 0;
        $requiredCount = $requirement->employee_count;

        for ($preferenceLevel = 1; $preferenceLevel <= 3 && $allocatedCount < $requiredCount; $preferenceLevel++) {
            $preferences = $this->getPreferences($shift, $requirement, $preferenceLevel);
            
            foreach ($preferences as $preference) {
                if ($allocatedCount >= $requiredCount) break;

                $employee = $preference->employee;
                if ($this->canAllocateEmployee($employee, $requirement)) {
                    $this->roster[$shift->id][] = $employee;
                    $this->employeeShiftCounts[$employee->id] = ($this->employeeShiftCounts[$employee->id] ?? 0) + 1;
                    $allocatedCount++;
                }
            }
        }

        // Fill remaining positions with "OPEN"
        while ($allocatedCount < $requiredCount) {
            $this->roster[$shift->id][] = "OPEN ({$requirement->department->name} - {$requirement->designation->name})";
            $allocatedCount++;
        }
    }

    private function getPreferences($shift, $requirement, $preferenceLevel)
    {
        return $shift->preferences()
            ->where('preference_level', $preferenceLevel)
            ->whereHas('employee', function ($query) use ($requirement) {
                $query->where('department_id', $requirement->department_id)
                      ->where('designation_id', $requirement->designation_id);
            })
            ->get()
            ->sortBy(function ($preference) {
                return $this->employeeShiftCounts[$preference->employee_id] ?? 0;
            });
    }

    private function canAllocateEmployee($employee, $requirement)
    {
        return $employee->department_id == $requirement->department_id &&
               $employee->designation_id == $requirement->designation_id;
    }
}

class RosterPublisher
{
    public function publish($roster)
    {
        DB::beginTransaction();
        
        try {
            foreach ($roster as $shiftId => $allocations) {
                $shift = Shift::findOrFail($shiftId);
                
                // Delete any existing published shifts for this shift
                PublishedShift::where('shift_id', $shiftId)->delete();
                
                foreach ($allocations as $allocation) {
                    if (is_object($allocation) && method_exists($allocation, 'id')) {
                        // This is an allocated employee
                        PublishedShift::create([
                            'shift_id' => $shiftId,
                            'employee_id' => $allocation->id,
                            'department_id' => $allocation->department_id,
                            'designation_id' => $allocation->designation_id,
                            'is_open' => false
                        ]);
                    } elseif (is_string($allocation) && strpos($allocation, 'OPEN') !== false) {
                        // This is an open position
                        preg_match('/OPEN \((.*?) - (.*?)\)/', $allocation, $matches);
                        $department = Department::where('name', $matches[1])->first();
                        $designation = Designation::where('name', $matches[2])->first();
                        
                        PublishedShift::create([
                            'shift_id' => $shiftId,
                            'employee_id' => null,
                            'department_id' => $department->id,
                            'designation_id' => $designation->id,
                            'is_open' => true
                        ]);
                    }
                }
                
                $shift->is_published = true;
                $shift->save();
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}