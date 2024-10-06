@extends('layouts.app')

@section('title', 'Generated Roster')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Generated Roster</h2>
        <div class="mt-8">
            <button id="generateRosterBtn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
                    Generate Roster
                </button>
            <a href="{{ route('admin.publish_shifts') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
                Proceed to Publish Shifts
            </a>
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
                <div id="shifts-container" class="space-y-4">
                    <!-- Shifts will be dynamically loaded here -->
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
        selectedDateElement.textContent = `Generated Roster for ${date}`;
        
        const shiftsForDate = shiftsData[date] || [];
        
        if (shiftsForDate.length === 0) {
            shiftsContainer.innerHTML = '<p class="text-gray-600">No shifts found for this date.</p>';
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
                        <p class="font-semibold mt-2">Allocations:</p>
                        <ul class="list-disc list-inside pl-4">
                            ${shift.generated_shifts.map(gs => `
                                <li>
                                    Department: ${gs.department.name}, 
                                    Designation: ${gs.designation.name}, 
                                    Employee: ${gs.employee ? gs.employee.name : 'OPEN'},
                                    Is Open: ${gs.is_open ? 'Yes' : 'No'}
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
});
</script>
@endpush
@endsection