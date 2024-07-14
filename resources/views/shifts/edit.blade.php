@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Shift</h2>
        <a href="{{ route('shifts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to Shifts
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('shifts.update', $shift) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date:</label>
                <input type="date" name="date" id="date" value="{{ $shift->date->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
                <input type="time" name="start_time" id="start_time" value="{{ $shift->start_time->format('H:i') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
                <input type="time" name="end_time" id="end_time" value="{{ $shift->end_time->format('H:i') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="required_employees" class="block text-gray-700 text-sm font-bold mb-2">Required Employees:</label>
                <input type="number" name="required_employees" id="required_employees" value="{{ $shift->required_employees }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
            </div>
            <div class="mb-4">
                <label for="department_id" class="block text-gray-700 text-sm font-bold mb-2">Department:</label>
                <select name="department_id" id="department_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ $shift->department_id == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="designation_id" class="block text-gray-700 text-sm font-bold mb-2">Designation:</label>
                <select name="designation_id" id="designation_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ $shift->designation_id == $designation->id ? 'selected' : '' }}>
                            {{ $designation->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Shift
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('department_id').addEventListener('change', function() {
            const departmentId = this.value;
            const designationSelect = document.getElementById('designation_id');
            
            // Clear current options
            designationSelect.innerHTML = '<option value="">Select Designation</option>';
            
            if (departmentId) {
                // Fetch designations for the selected department
                fetch(`/designations/by-department/${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(designation => {
                            const option = document.createElement('option');
                            option.value = designation.id;
                            option.textContent = designation.name;
                            designationSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
@endsection