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
        $shifts = Shift::with(['preferences.employee'])->get();
        $roster = [];

        foreach ($shifts as $shift) {
            $requiredEmployees = $shift->required_employees;
            $assignedEmployees = [];

            // Sort preferences by level (1 being highest)
            $sortedPreferences = $shift->preferences->sortBy('preference_level');

            foreach ($sortedPreferences as $preference) {
                if (count($assignedEmployees) < $requiredEmployees) {
                    $assignedEmployees[] = $preference->employee;
                } else {
                    break;
                }
            }

            // If we still need more employees, assign randomly from remaining employees
            if (count($assignedEmployees) < $requiredEmployees) {
                $remainingEmployees = Employee::whereNotIn('id', $assignedEmployees->pluck('id'))->get();
                $remainingEmployees = $remainingEmployees->shuffle();

                foreach ($remainingEmployees as $employee) {
                    if (count($assignedEmployees) < $requiredEmployees) {
                        $assignedEmployees[] = $employee;
                    } else {
                        break;
                    }
                }
            }

            $roster[$shift->id] = $assignedEmployees;
        }

        // Store the generated roster in the session for now
        session(['generated_roster' => $roster]);

        return view('admin.generated_roster', compact('roster', 'shifts'));
    }

    public function publishShifts()
    {
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