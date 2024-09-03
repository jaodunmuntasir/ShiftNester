<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftPreference;
use App\Models\Employee;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function viewShiftPreferences()
    {
        $shifts = Shift::with(['preferences.employee'])->get();
        return view('admin.shift_preferences', compact('shifts'));
    }

    public function generateRoster()
    {
        $shifts = Shift::with(['preferences.employee', 'requirements'])->orderBy('date')->orderBy('start_time')->get();
        $roster = [];
        $employeeShiftCounts = [];
        $unassignedPreferences = [];

        foreach ($shifts as $shift) {
            $requiredEmployees = $shift->required_employees;
            $assignedEmployees = [];

            // Get required skills for this shift
            $requiredSkills = $shift->requirements->pluck('skill_id')->toArray();

            // First, assign employees based on their 1st and 2nd preferences
            for ($preferenceLevel = 1; $preferenceLevel <= 2; $preferenceLevel++) {
                $preferences = $shift->preferences->where('preference_level', $preferenceLevel)->sortBy(function ($preference) use ($employeeShiftCounts, $requiredSkills) {
                    $employee = $preference->employee;
                    $shiftCount = $employeeShiftCounts[$employee->id] ?? 0;
                    $skillMatch = $employee->skills->whereIn('id', $requiredSkills)->count();
                    
                    // Lower score is better (prioritize fewer shifts and better skill match)
                    return $shiftCount - ($skillMatch * 0.5);
                });
                
                foreach ($preferences as $preference) {
                    if (count($assignedEmployees) < $requiredEmployees) {
                        $assignedEmployees[] = $preference->employee;
                        $employeeId = $preference->employee->id;
                        $employeeShiftCounts[$employeeId] = ($employeeShiftCounts[$employeeId] ?? 0) + 1;
                    } else {
                        // Store unassigned preferences for later consideration
                        $unassignedPreferences[] = $preference;
                    }
                }
            }

            // If we still need more employees, consider other factors
            if (count($assignedEmployees) < $requiredEmployees) {
                $remainingPreferences = $shift->preferences->whereNotIn('employee_id', collect($assignedEmployees)->pluck('id'));
                
                // Sort remaining preferences by level, skill match, and fair distribution
                $sortedPreferences = $remainingPreferences->sortBy(function ($preference) use ($requiredSkills, $employeeShiftCounts) {
                    $employee = $preference->employee;
                    $skillMatch = $employee->skills->whereIn('id', $requiredSkills)->count();
                    $shiftCount = $employeeShiftCounts[$employee->id] ?? 0;
                    
                    // Lower score is better
                    return ($preference->preference_level * 10) - ($skillMatch * 2) + $shiftCount;
                });

                foreach ($sortedPreferences as $preference) {
                    if (count($assignedEmployees) < $requiredEmployees) {
                        $assignedEmployees[] = $preference->employee;
                        $employeeId = $preference->employee->id;
                        $employeeShiftCounts[$employeeId] = ($employeeShiftCounts[$employeeId] ?? 0) + 1;
                    } else {
                        $unassignedPreferences[] = $preference;
                    }
                }
            }

            $roster[$shift->id] = $assignedEmployees;
        }

        // Try to accommodate unassigned preferences in other shifts
        foreach ($unassignedPreferences as $preference) {
            $employee = $preference->employee;
            $potentialShifts = $shifts->where('id', '!=', $preference->shift_id)
                                    ->where('date', '>=', $preference->shift->date)
                                    ->sortBy('date');

            foreach ($potentialShifts as $shift) {
                if (count($roster[$shift->id]) < $shift->required_employees && 
                    !in_array($employee, $roster[$shift->id])) {
                    $roster[$shift->id][] = $employee;
                    $employeeShiftCounts[$employee->id] = ($employeeShiftCounts[$employee->id] ?? 0) + 1;
                    break;
                }
            }
        }

        // Store the generated roster in the session
        session(['generated_roster' => $roster]);

        return view('admin.generated_roster', compact('roster', 'shifts'));
    }

    public function publishShifts(Request $request)
    {
        if ($request->isMethod('get')) {
            // Show confirmation page
            return view('admin.confirm_publish_shifts');
        }

        // Handle POST request (actual publishing)
        $roster = session('generated_roster');

        if (!$roster) {
            return redirect()->route('admin.view_shift_preferences')->with('error', 'No roster generated. Please generate a roster first.');
        }

        foreach ($roster as $shiftId => $employees) {
            $shift = Shift::find($shiftId);
            $shift->employees()->sync(collect($employees)->pluck('id'));
            $shift->is_published = true;
            $shift->save();
        }

        session()->forget('generated_roster');

        return redirect()->route('shifts.published')->with('success', 'Shifts have been published successfully.');
    }
}