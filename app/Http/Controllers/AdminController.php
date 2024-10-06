<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftPreference;
use App\Models\Employee;
use App\Models\ShiftRequirement;
use App\Models\PublishedShift;
use App\Models\Department;
use App\Models\Designation;
use App\Models\GeneratedShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function viewShiftPreferences()
    {
        $shifts = Shift::with(['preferences' => function($query) {
            $query->with(['employee.designation']);
        }])
        ->whereDate('date', '>=', now())
        ->orderBy('date')
        ->orderBy('start_time')
        ->get()
        ->groupBy(function($shift) {
            return $shift->date->format('Y-m-d');
        });

        return view('admin.shift_preferences', compact('shifts'));
    }

    public function generateRoster()
    {
        $rosterGenerator = new RosterGenerator();
        if ($rosterGenerator->generate()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 500);
        }
    }

    public function viewGeneratedRoster()
    {
        $shifts = Shift::with(['generatedShifts' => function($query) {
            $query->with(['employee', 'department', 'designation']);
        }])
        ->whereHas('generatedShifts')
        ->orderBy('date')
        ->orderBy('start_time')
        ->get()
        ->groupBy(function($shift) {
            return $shift->date->format('Y-m-d');
        });

        return view('admin.generated_roster', compact('shifts'));
    }

    public function publishShifts()
    {
        $publisher = new RosterPublisher();
        if ($publisher->publish()) {
            return redirect()->route('shifts.published')->with('success', 'Shifts have been published successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to publish shifts.');
        }
    }

    public function viewPublishedShifts()
    {
        $publishedShifts = Shift::with(['publishedShifts' => function($query) {
            $query->with(['employee', 'department', 'designation']);
        }])
        ->whereHas('publishedShifts')
        ->orderBy('date')
        ->orderBy('start_time')
        ->get()
        ->groupBy(function($shift) {
            return $shift->date->format('Y-m-d');
        });

        return view('shifts.published', compact('publishedShifts'));
    }

    public function getShiftsForDate($date)
    {
        $shifts = Shift::with(['generatedShifts.employee', 'generatedShifts.department', 'generatedShifts.designation', 'preferences.employee'])
            ->whereDate('date', $date)
            ->orderBy('start_time')
            ->get();

        $formattedShifts = $shifts->map(function ($shift) {
            return [
                'id' => $shift->id,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'allocations' => $shift->generatedShifts->map(function ($generatedShift) {
                    return [
                        'department' => $generatedShift->department->name,
                        'designation' => $generatedShift->designation->name,
                        'employee' => $generatedShift->is_open ? 'OPEN' : $generatedShift->employee->name,
                    ];
                }),
                'preferences' => $shift->preferences->map(function ($preference) {
                    return [
                        'employee' => [
                            'name' => $preference->employee->name,
                        ],
                        'preference_level' => $preference->preference_level,
                    ];
                }),
            ];
        });

        return response()->json($formattedShifts);
    }
}

class RosterGenerator
{
    private $employeeShiftCounts = [];

    public function generate()
    {
        DB::beginTransaction();
        try {
            // Clear any previously generated shifts
            GeneratedShift::truncate();

            $shifts = Shift::with(['requirements.department', 'requirements.designation', 'preferences.employee'])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            foreach ($shifts as $shift) {
                $this->allocateEmployeesForShift($shift);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating roster: ' . $e->getMessage());
            throw $e;
        }
    }

    private function allocateEmployeesForShift($shift)
    {
        foreach ($shift->requirements as $requirement) {
            $this->allocateEmployeesForRequirement($shift, $requirement);
        }
    }

    private function allocateEmployeesForRequirement($shift, $requirement)
    {
        $allocatedCount = 0;
        $requiredCount = $requirement->employee_count;
        $allocatedEmployees = [];

        for ($preferenceLevel = 1; $preferenceLevel <= 3 && $allocatedCount < $requiredCount; $preferenceLevel++) {
            $preferences = $this->getPreferences($shift, $requirement, $preferenceLevel);
            
            foreach ($preferences as $preference) {
                if ($allocatedCount >= $requiredCount) break;

                $employee = $preference->employee;
                if ($this->canAllocateEmployee($employee, $requirement)) {
                    $allocatedEmployees[] = $employee;
                    $this->employeeShiftCounts[$employee->id] = ($this->employeeShiftCounts[$employee->id] ?? 0) + 1;
                    $allocatedCount++;
                }
            }
        }

        // Store allocated employees
        foreach ($allocatedEmployees as $employee) {
            GeneratedShift::create([
                'shift_id' => $shift->id,
                'employee_id' => $employee->id,
                'department_id' => $requirement->department_id,
                'designation_id' => $requirement->designation_id,
                'is_open' => false
            ]);
        }

        // Fill remaining positions with "OPEN"
        for ($i = $allocatedCount; $i < $requiredCount; $i++) {
            GeneratedShift::create([
                'shift_id' => $shift->id,
                'employee_id' => null,
                'department_id' => $requirement->department_id,
                'designation_id' => $requirement->designation_id,
                'is_open' => true
            ]);
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
    public function publish()
    {
        DB::beginTransaction();
        try {
            // Clear any previously published shifts
            PublishedShift::truncate();

            // Copy all generated shifts to published shifts
            $generatedShifts = \App\Models\GeneratedShift::all();
            foreach ($generatedShifts as $generatedShift) {
                PublishedShift::create($generatedShift->toArray());
            }

            // Mark all shifts as published
            Shift::whereIn('id', $generatedShifts->pluck('shift_id')->unique())->update(['is_published' => true]);

            // Clear generated shifts
            \App\Models\GeneratedShift::truncate();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error publishing roster: ' . $e->getMessage());
            throw $e;
        }
    }
}