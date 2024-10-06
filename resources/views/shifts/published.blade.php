@extends('layouts.app')

@section('title', 'Published Shifts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Published Shifts</h2>

    <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6">
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4" id="selected-date">Select a date to view published shifts</h3>
                <div id="shifts-container" class="space-y-4">
                    <!-- Published shifts will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shiftsData = @json($publishedShifts);
    const shiftDates = Object.keys(shiftsData);

    const calendar = flatpickr("#calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            loadPublishedShifts(dateStr);
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

    function loadPublishedShifts(date) {
        const shiftsContainer = document.getElementById('shifts-container');
        const selectedDateElement = document.getElementById('selected-date');
        selectedDateElement.textContent = `Published Shifts for ${date}`;
        
        const shiftsForDate = shiftsData[date] || [];
        
        if (shiftsForDate.length === 0) {
            shiftsContainer.innerHTML = '<p class="text-gray-600">No published shifts found for this date.</p>';
            return;
        }

        let shiftsHtml = '';
        shiftsForDate.forEach(shift => {
            shiftsHtml += `
                <div class="bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Shift ID: ${shift.id}</h4>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p class="font-semibold mt-2">Assignments:</p>
                        <ul class="list-disc list-inside pl-4">
                            ${shift.published_shifts.map(ps => `
                                <li>
                                    Department: ${ps.department.name}, 
                                    Designation: ${ps.designation.name}, 
                                    Employee: ${ps.employee ? ps.employee.name : 'OPEN'},
                                    Is Open: ${ps.is_open ? 'Yes' : 'No'}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
            `;
        });

        shiftsContainer.innerHTML = shiftsHtml;
    }

    highlightShiftDates();
});
</script>
@endpush
@endsection