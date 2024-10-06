@extends('layouts.app')

@section('title', 'Open Shifts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Open Shifts Available for Claiming</h2>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div id="calendar" class="p-4"></div>
    </div>

    <div id="shiftsContainer" class="mt-8">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4" id="selectedDate"></h3>
        <div id="shiftsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Shifts will be dynamically inserted here -->
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .has-shifts { background-color: #FEF3C7 !important; }
    .shift-item { transition: all 0.3s ease; }
    .shift-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openShifts = @json($openShifts);
    const shiftsContainer = document.getElementById('shiftsContainer');
    const shiftsList = document.getElementById('shiftsList');
    const selectedDateElement = document.getElementById('selectedDate');

    const calendar = flatpickr("#calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const dateStr = dayElem.dateObj.toISOString().split('T')[0];
            if (openShifts[dateStr] && openShifts[dateStr].length > 0) {
                dayElem.classList.add('has-shifts');
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            showShiftsForDate(dateStr);
        }
    });

    function showShiftsForDate(date) {
        const shiftsForDate = openShifts[date] || [];
        selectedDateElement.textContent = `Open Shifts for ${date}`;
        
        if (shiftsForDate.length === 0) {
            shiftsList.innerHTML = '<p class="text-gray-600 col-span-full">No open shifts available for this date.</p>';
        } else {
            let shiftsHtml = '';
            shiftsForDate.forEach(shift => {
                shiftsHtml += `
                    <div class="shift-item bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-2">
                            <h5 class="font-semibold">${formatTime(shift.shift.start_time)} - ${formatTime(shift.shift.end_time)}</h5>
                            <span class="text-sm text-gray-600">${shift.department.name}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">${shift.designation.name}</p>
                        <form action="/shifts/${shift.id}/claim" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                Claim Shift
                            </button>
                        </form>
                    </div>
                `;
            });
            shiftsList.innerHTML = shiftsHtml;
        }
        
        shiftsContainer.classList.remove('hidden');
    }

    function formatTime(timeString) {
        const date = new Date(timeString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Show shifts for the current date on page load
    const today = new Date().toISOString().split('T')[0];
    showShiftsForDate(today);
});
</script>
@endpush
@endsection