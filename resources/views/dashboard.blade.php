<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Employees</h3>
            <p class="text-3xl font-bold text-red-600">0</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Shifts</h3>
            <p class="text-3xl font-bold text-red-600">0</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pending Preferences</h3>
            <p class="text-3xl font-bold text-red-600">0</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Unassigned Shifts</h3>
            <p class="text-3xl font-bold text-red-600">0</p>
        </div>
    </div>
@endsection