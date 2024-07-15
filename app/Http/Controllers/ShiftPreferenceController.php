<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftPreference;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftPreferenceController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as an employee.');
        }

        $availableShifts = Shift::where('date', '>', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        $preferences = ShiftPreference::where('employee_id', $employee->id)
            ->pluck('preference_level', 'shift_id')
            ->toArray();

        // Debug information
        \Log::info('Available Shifts: ' . $availableShifts->count());
        \Log::info('Employee ID: ' . $employee->id);

        return view('shift_preferences.index', compact('availableShifts', 'preferences'));
    }

    public function store(Request $request)
    {
        \Log::info('Entered store method');
        \Log::info('Request data: ' . json_encode($request->all()));

        try {
            $validated = $request->validate([
                'preferences' => 'required|array',
                'preferences.*' => 'nullable|integer|min:1|max:3',
            ]);

            \Log::info('Validation passed');

            $employee = Auth::user()->employee;

            if (!$employee) {
                \Log::error('No employee record found for user: ' . Auth::id());
                return redirect()->route('shift_preferences.index')->with('error', 'Employee record not found.');
            }

            \Log::info('Employee found: ' . $employee->id);

            $updatedCount = 0;

            foreach ($validated['preferences'] as $shiftId => $preferenceLevel) {
                if ($preferenceLevel !== null) {
                    \Log::info("Attempting to update/create preference for shift $shiftId with level $preferenceLevel");

                    $result = ShiftPreference::updateOrCreate(
                        ['employee_id' => $employee->id, 'shift_id' => $shiftId],
                        ['preference_level' => $preferenceLevel]
                    );

                    \Log::info("Result of updateOrCreate: " . json_encode($result->toArray()));
                    
                    $updatedCount++;
                } else {
                    // If preferenceLevel is null, delete any existing preference
                    ShiftPreference::where('employee_id', $employee->id)
                                ->where('shift_id', $shiftId)
                                ->delete();
                    
                    \Log::info("Deleted preference for shift $shiftId");
                }
            }

            return redirect()->route('shift_preferences.index')
                ->with('success', "$updatedCount shift preference(s) updated successfully.");
        } catch (\Exception $e) {
            \Log::error('Error in shift preference store: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('shift_preferences.index')
                ->with('error', 'An error occurred while saving preferences. Please try again.');
        }
    }
}