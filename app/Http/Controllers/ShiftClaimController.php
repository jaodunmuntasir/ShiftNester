<?php

namespace App\Http\Controllers;

use App\Models\PublishedShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShiftClaimController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        $openShifts = PublishedShift::where('is_open', true)
            ->where('department_id', $employee->department_id)
            ->where('designation_id', $employee->designation_id)
            ->whereHas('shift', function ($query) {
                $query->where('date', '>=', now()->toDateString());
            })
            ->with(['shift', 'department', 'designation'])
            ->get()
            ->groupBy(function($publishedShift) {
                return $publishedShift->shift->date->format('Y-m-d');
            });

        Log::info('Open Shifts:', ['count' => $openShifts->count(), 'data' => $openShifts->toArray()]);

        return view('shifts.claim', compact('openShifts'));
    }

    public function claim(Request $request, PublishedShift $publishedShift)
    {
        $employee = Auth::user()->employee;

        if ($publishedShift->is_open &&
            $publishedShift->department_id == $employee->department_id &&
            $publishedShift->designation_id == $employee->designation_id) {
            
            $publishedShift->update([
                'employee_id' => $employee->id,
                'is_open' => false
            ]);

            return redirect()->route('shifts.claim.index')->with('success', 'Shift claimed successfully.');
        }

        return redirect()->route('shifts.claim.index')->with('error', 'This shift is no longer available.');
    }
}