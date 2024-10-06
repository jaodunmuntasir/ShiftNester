<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\PublishedShift;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $totalShifts = Shift::count();
        $publishedShifts = Shift::where('is_published', true)->count();
        $openShifts = PublishedShift::where('is_open', true)->count();

        $recentActivities = $this->getRecentActivities();

        $departmentShiftData = $this->getDepartmentShiftDistribution();

        return view('dashboard', compact(
            'totalEmployees',
            'totalShifts',
            'publishedShifts',
            'openShifts',
            'recentActivities',
            'departmentShiftData'
        ));
    }

    private function getRecentActivities()
    {
        // Implement this method based on your activity logging system
        // For now, we'll return some dummy data
        return [
            'New shift created for ' . now()->addDays(1)->format('Y-m-d'),
            'Employee preferences submitted for next week',
            'Roster generated for upcoming month',
        ];
    }

    private function getDepartmentShiftDistribution()
    {
        $departmentShifts = Department::withCount('shiftRequirements')->get();

        $labels = $departmentShifts->pluck('name')->toArray();
        $counts = $departmentShifts->pluck('shift_requirements_count')->toArray();

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}