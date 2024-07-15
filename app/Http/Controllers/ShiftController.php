<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with(['requirements.department', 'requirements.designation'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(10);
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function generateShifts(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
        ]);

        $date = Carbon::parse($validated['date']);
        $openingTime = Carbon::parse($validated['opening_time']);
        $closingTime = Carbon::parse($validated['closing_time']);

        $shifts = [];
        $currentTime = $openingTime->copy();

        while ($currentTime < $closingTime) {
            $shifts[] = [
                'date' => $date->toDateString(),
                'start_time' => $currentTime->format('H:i'),
                'end_time' => $currentTime->addHour()->format('H:i'),
            ];
        }

        $departments = Department::with('designations')->get();

        return view('shifts.requirements', compact('shifts', 'departments'));
    }

    public function storeShifts(Request $request)
    {
        $validated = $request->validate([
            'shifts' => 'required|array',
            'shifts.*.date' => 'required|date',
            'shifts.*.start_time' => 'required|date_format:H:i',
            'shifts.*.end_time' => 'required|date_format:H:i|after:shifts.*.start_time',
            'requirements' => 'required|array',
            'requirements.*.*.*' => 'required|integer|min:0',
        ]);

        foreach ($validated['shifts'] as $index => $shiftData) {
            // Calculate total required employees for this shift
            $totalRequiredEmployees = 0;
            foreach ($validated['requirements'][$index] as $departmentRequirements) {
                $totalRequiredEmployees += array_sum($departmentRequirements);
            }

            // Create the shift with the calculated required_employees
            $shift = Shift::create([
                'date' => $shiftData['date'],
                'start_time' => $shiftData['start_time'],
                'end_time' => $shiftData['end_time'],
                'required_employees' => $totalRequiredEmployees,
            ]);

            // Create shift requirements
            foreach ($validated['requirements'][$index] as $departmentId => $designations) {
                foreach ($designations as $designationId => $count) {
                    if ($count > 0) {
                        $shift->requirements()->create([
                            'department_id' => $departmentId,
                            'designation_id' => $designationId,
                            'employee_count' => $count,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('shifts.index')->with('success', 'Shifts created and published successfully.');
    }

    public function viewPublishedShifts()
    {
        $publishedShifts = Shift::where('is_published', true)
            ->with(['employees'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('shifts.published', compact('publishedShifts'));
    }
}