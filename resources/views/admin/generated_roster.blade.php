@extends('layouts.app')

@section('title', 'Generated Roster')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Generated Roster</h2>
        <div class="space-x-4">
            <button id="generateRosterBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                Generate Roster
            </button>
            <button id="publishShiftsBtn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                Publish Shifts
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6">
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4" id="selected-date">Select a date to view shifts</h3>
                <div id="shifts-container" class="space-y-6">
                    <!-- Shifts will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .tab-active {
        border-bottom: 2px solid #3B82F6;
        color: #3B82F6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/dayjs.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shiftsData = @json($shifts);
    const shiftDates = Object.keys(shiftsData);

    const calendar = flatpickr("#calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            loadShifts(dateStr);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
            highlightShiftDates();
        },
    });

    function highlightShiftDates() {
        const days = document.getElementsByClassName("flatpickr-day");
        for (let i = 0; i < days.length; i++) {
            const day = days[i];
            const date = day.dateObj;
            if (date && shiftDates.includes(date.toISOString().split('T')[0])) {
                day.classList.add("has-shifts");
            }
        }
    }

    function loadShifts(date) {
        const shiftsContainer = document.getElementById('shifts-container');
        const selectedDateElement = document.getElementById('selected-date');
        selectedDateElement.textContent = `Generated Roster for ${dayjs(date).format('MMMM D, YYYY')}`;
        
        const shiftsForDate = shiftsData[date] || [];
        
        if (shiftsForDate.length === 0) {
            shiftsContainer.innerHTML = '<p class="text-gray-600">No shifts found for this date.</p>';
            return;
        }

        let shiftsHtml = `
            <div class="mb-4">
                <div class="flex border-b">
                    ${shiftsForDate.map((shift, index) => `
                        <button class="px-4 py-2 text-sm font-medium ${index === 0 ? 'tab-active' : ''}" onclick="showShift(${shift.id})">
                            Shift ${index + 1}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;

        shiftsForDate.forEach((shift, index) => {
            const groupedAllocations = groupAllocationsByDepartment(shift.generated_shifts);

            shiftsHtml += `
                <div id="shift-${shift.id}" class="shift-content ${index !== 0 ? 'hidden' : ''}">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Shift ID: ${shift.id}</h4>
                        <span class="text-sm text-gray-600">${dayjs(shift.start_time).format('h:mm A')} - ${dayjs(shift.end_time).format('h:mm A')}</span>
                    </div>
                    <div class="space-y-4">
                        ${Object.entries(groupedAllocations).map(([department, designations]) => `
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <h5 class="font-semibold text-gray-700 mb-2">${department}</h5>
                                ${Object.entries(designations).map(([designation, employees]) => `
                                    <div class="mb-3">
                                        <h6 class="text-sm font-medium text-gray-600 mb-1">${designation}</h6>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                            ${employees.map(emp => `
                                                <div class="text-sm ${emp.is_open ? 'text-red-600 font-semibold' : 'text-gray-600'} flex items-center">
                                                    <svg class="w-4 h-4 mr-2 ${emp.is_open ? 'text-red-600' : 'text-gray-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    ${emp.is_open ? 'OPEN' : emp.employee.name}
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        });

        shiftsContainer.innerHTML = shiftsHtml;
    }

    function groupAllocationsByDepartment(allocations) {
        const grouped = {};
        allocations.forEach(allocation => {
            const dept = allocation.department.name;
            const desig = allocation.designation.name;
            if (!grouped[dept]) grouped[dept] = {};
            if (!grouped[dept][desig]) grouped[dept][desig] = [];
            grouped[dept][desig].push(allocation);
        });
        return grouped;
    }

    highlightShiftDates();

    window.showShift = function(shiftId) {
        document.querySelectorAll('.shift-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`shift-${shiftId}`).classList.remove('hidden');
        document.querySelectorAll('.tab-active').forEach(el => el.classList.remove('tab-active'));
        event.target.classList.add('tab-active');
    }

    document.getElementById('generateRosterBtn').addEventListener('click', function() {
        fetch('{{ route('admin.generate_roster') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Roster generated successfully!');
                location.reload();
            } else {
                alert('Failed to generate roster. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the roster.');
        });
    });
    
    document.getElementById('publishShiftsBtn').addEventListener('click', function() {
        fetch('{{ route('admin.publish_shifts') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Shifts published successfully!');
                window.location.href = '{{ route('shifts.published') }}';
            } else {
                alert('Failed to publish shifts. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while publishing the shifts.');
        });
    });
});
</script>
@endpush
@endsection