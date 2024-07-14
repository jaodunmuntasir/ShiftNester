@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Employee</h2>
        <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to Employees
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <input type="text" name="name" id="name" value="{{ $employee->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" value="{{ $employee->email }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>
            <div class="mb-4">
                <label for="department_id" class="block text-gray-700 text-sm font-bold mb-2">Department:</label>
                <select name="department_id" id="department_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="designation_id" class="block text-gray-700 text-sm font-bold mb-2">Designation:</label>
                <select name="designation_id" id="designation_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ $employee->designation_id == $designation->id ? 'selected' : '' }}>
                            {{ $designation->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="hire_date" class="block text-gray-700 text-sm font-bold mb-2">Hire Date:</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ $employee->hire_date->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Skills</h3>
                @foreach ($skills as $skill)
                    <div class="mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" class="form-checkbox" {{ $employee->skills->contains($skill) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $skill->name }}</span>
                        </label>
                        @if (!$skill->is_boolean)
                            <input type="number" name="skill_ratings[{{ $skill->id }}]" min="1" max="5" value="{{ $employee->skills->where('id', $skill->id)->first()->pivot->rating ?? '' }}" class="ml-2 w-16 shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Rating">
                        @else
                            <input type="hidden" name="skill_has[{{ $skill->id }}]" value="{{ $employee->skills->contains($skill) ? '1' : '0' }}">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Employee
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