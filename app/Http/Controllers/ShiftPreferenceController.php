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
        $availableShifts = Shift::where('date', '>', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        $preferences = ShiftPreference::where('employee_id', $employee->id)
            ->pluck('preference_level', 'shift_id')
            ->toArray();

        return view('shift_preferences.index', compact('availableShifts', 'preferences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*' => 'required|integer|min:1|max:3',
        ]);

        $employee = Auth::user()->employee;

        foreach ($validated['preferences'] as $shiftId => $preferenceLevel) {
            ShiftPreference::updateOrCreate(
                ['employee_id' => $employee->id, 'shift_id' => $shiftId],
                ['preference_level' => $preferenceLevel]
            );
        }

        return redirect()->route('shift_preferences.index')->with('success', 'Shift preferences updated successfully.');
    }
}