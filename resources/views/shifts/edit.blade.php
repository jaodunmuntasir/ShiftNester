@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Edit Shift</h2>
        <a href="{{ route('shifts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
            Back to Shifts
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6">
        <form action="{{ route('shifts.update', $shift) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="date" id="date" value="{{ $shift->date->format('Y-m-d') }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="time" name="start_time" id="start_time" value="{{ $shift->start_time->format('H:i') }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ $shift->end_time->format('H:i') }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="required_employees" class="block text-sm font-medium text-gray-700 mb-2">Required Employees</label>
                    <input type="number" name="required_employees" id="required_employees" value="{{ $shift->required_employees }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1" required>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Shift Requirements</h3>
                <div id="requirements-container">
                    @foreach($shift->requirements as $index => $requirement)
                        <div class="requirement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="department_id_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select name="requirements[{{ $index }}][department_id]" id="department_id_{{ $index }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                                @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ $requirement->department_id == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="designation_id_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                                <select name="requirements[{{ $index }}][designation_id]" id="designation_id_{{ $index }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}" {{ $requirement->designation_id == $designation->id ? 'selected' : '' }}>
                                            {{ $designation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="employee_count_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">Employee Count</label>
                                <input type="number" name="requirements[{{ $index }}][employee_count]" id="employee_count_{{ $index }}" value="{{ $requirement->employee_count }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1" required>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-requirement" class="mt-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Add Requirement
                </button>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:shadow-outline">
                    Update Shift
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#date", {
        dateFormat: "Y-m-d",
        minDate: "today",
    });

    flatpickr("#start_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });

    flatpickr("#end_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });

    const requirementsContainer = document.getElementById('requirements-container');
    const addRequirementButton = document.getElementById('add-requirement');
    let requirementIndex = {{ count($shift->requirements) }};

    addRequirementButton.addEventListener('click', function() {
        const newRequirement = document.createElement('div');
        newRequirement.className = 'requirement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4';
        newRequirement.innerHTML = `
            <div>
                <label for="department_id_${requirementIndex}" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="requirements[${requirementIndex}][department_id]" id="department_id_${requirementIndex}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="designation_id_${requirementIndex}" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                <select name="requirements[${requirementIndex}][designation_id]" id="designation_id_${requirementIndex}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                    <option value="">Select Designation</option>
                </select>
            </div>
            <div>
                <label for="employee_count_${requirementIndex}" class="block text-sm font-medium text-gray-700 mb-2">Employee Count</label>
                <input type="number" name="requirements[${requirementIndex}][employee_count]" id="employee_count_${requirementIndex}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1" required>
            </div>
        `;
        requirementsContainer.appendChild(newRequirement);
        requirementIndex++;

        // Add event listener for department change
        const newDepartmentSelect = newRequirement.querySelector(`#department_id_${requirementIndex - 1}`);
        newDepartmentSelect.addEventListener('change', function() {
            updateDesignations(this);
        });
    });

    // Function to update designations based on selected department
    function updateDesignations(departmentSelect) {
        const departmentId = departmentSelect.value;
        const designationSelect = departmentSelect.closest('.requirement-row').querySelector('select[id^="designation_id_"]');
        
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
    }

    // Add event listeners for existing department selects
    document.querySelectorAll('select[id^="department_id_"]').forEach(select => {
        select.addEventListener('change', function() {
            updateDesignations(this);
        });
    });
});
</script>
@endpush
@endsection