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
        // We'll implement this method next
    }

    public function publishShifts()
    {
        // We'll implement this method later
    }
}