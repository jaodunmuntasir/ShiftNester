@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Dashboard</h2>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="card-text display-4">{{ $totalEmployees ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Shifts</h5>
                    <p class="card-text display-4">{{ $totalShifts ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Preferences</h5>
                    <p class="card-text display-4">{{ $pendingPreferences ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Unassigned Shifts</h5>
                    <p class="card-text display-4">{{ $unassignedShifts ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Recent Activities
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">New employee added: John Doe</li>
                    <li class="list-group-item">Shift preferences submitted for next week</li>
                    <li class="list-group-item">Roster generated for upcoming month</li>
                </ul>
            </div>
        </div>