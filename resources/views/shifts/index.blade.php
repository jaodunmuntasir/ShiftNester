@extends('layouts.app')

@section('title', 'Shifts Calendar')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-4 lg:mb-0">Shifts Calendar</h2>
        <a href="{{ route('shifts.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
            Create New Shifts
        </a>
    </div>

    <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6">
        <!-- Calendar -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Shifts for selected date -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4" id="selected-date">Select a date to view shifts</h3>
                <div id="shifts-container" class="space-y-4">
                    <!-- Shifts will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar { width: 100% !important; }
    .flatpickr-day.has-shifts { background-color: #FDE68A; border-color: #FDE68A; }
    .flatpickr-day.has-shifts:hover { background-color: #FCD34D; }
    .shift-card { transition: all 0.3s ease-in-out; }
    .shift-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
        selectedDateElement.textContent = `Shifts for ${formatDate(date)}`;
        
        const shiftsForDate = shiftsData[date] || [];
        
        if (shiftsForDate.length === 0) {
            shiftsContainer.innerHTML = '<p class="text-gray-600">No shifts found for this date.</p>';
            return;
        }

        let shiftsHtml = '';
        shiftsForDate.forEach(shift => {
            shiftsHtml += `
                <div class="shift-card bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">
                            ${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}
                        </h4>
                        <div class="space-x-2">
                            <a href="/shifts/${shift.id}/edit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Edit
                            </a>
                            <button onclick="deleteShift(${shift.id})" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Delete
                            </button>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p class="mb-2">Required Employees: ${shift.required_employees}</p>
                        <p class="font-semibold mb-2">Requirements:</p>
                        <ul class="list-disc list-inside pl-4">
                            ${shift.requirements.map(req => `
                                <li>${req.department.name} - ${req.designation.name}: ${req.employee_count}</li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
            `;
        });

        shiftsContainer.innerHTML = shiftsHtml;
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    function formatTime(timeString) {
        return new Date(timeString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    highlightShiftDates();

    window.deleteShift = function(shiftId) {
        if (confirm('Are you sure you want to delete this shift?')) {
            fetch(`/shifts/${shiftId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Shift deleted successfully');
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Failed to delete shift');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the shift');
            });
        }
    };
});
</script>
@endpush
@endsection