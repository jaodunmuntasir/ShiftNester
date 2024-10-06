@extends('layouts.app')

@section('title', 'Employee Shift Preferences')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Employee Shift Preferences</h2>

    <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6">
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4" id="selected-date">Select a date to view preferences</h3>
                <div id="shifts-container" class="space-y-6">
                    <!-- Shift preferences will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .preference-level {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    .preference-level-1 { background-color: #10B981; }
    .preference-level-2 { background-color: #F59E0B; }
    .preference-level-3 { background-color: #EF4444; }
    .preference-group {
        border-left: 4px solid;
        padding-left: 1rem;
    }
    .preference-group-1 { border-color: #10B981; }
    .preference-group-2 { border-color: #F59E0B; }
    .preference-group-3 { border-color: #EF4444; }
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
            loadShiftPreferences(dateStr);
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

    function loadShiftPreferences(date) {
        const shiftsContainer = document.getElementById('shifts-container');
        const selectedDateElement = document.getElementById('selected-date');
        selectedDateElement.textContent = `Shift Preferences for ${dayjs(date).format('MMMM D, YYYY')}`;
        
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
            const groupedPreferences = {1: {}, 2: {}, 3: {}};
            shift.preferences.forEach(pref => {
                const level = pref.preference_level;
                const designation = pref.employee.designation.name;
                if (!groupedPreferences[level][designation]) {
                    groupedPreferences[level][designation] = [];
                }
                groupedPreferences[level][designation].push(pref.employee);
            });

            shiftsHtml += `
                <div id="shift-${shift.id}" class="shift-content ${index !== 0 ? 'hidden' : ''}">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Shift ID: ${shift.id}</h4>
                        <span class="text-sm text-gray-600">${dayjs(shift.start_time).format('h:mm A')} - ${dayjs(shift.end_time).format('h:mm A')}</span>
                    </div>
                    <div class="space-y-4">
                        ${[1, 2, 3].map(level => `
                            <div class="preference-group preference-group-${level}">
                                <h5 class="font-semibold text-gray-700 mb-2">
                                    <span class="preference-level preference-level-${level}"></span>
                                    Preference Level ${level}
                                </h5>
                                ${Object.keys(groupedPreferences[level]).length > 0 ? `
                                    ${Object.entries(groupedPreferences[level]).map(([designation, employees]) => `
                                        <div class="mb-3">
                                            <h6 class="text-sm font-medium text-gray-600 mb-1">${designation}</h6>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                                ${employees.map(emp => `
                                                    <div class="text-sm text-gray-600 flex items-center">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        ${emp.name}
                                                    </div>
                                                `).join('')}
                                            </div>
                                        </div>
                                    `).join('')}
                                ` : '<p class="text-sm text-gray-500">No preferences at this level</p>'}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        });

        shiftsContainer.innerHTML = shiftsHtml;
    }

    highlightShiftDates();

    window.showShift = function(shiftId) {
        document.querySelectorAll('.shift-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`shift-${shiftId}`).classList.remove('hidden');
        document.querySelectorAll('.tab-active').forEach(el => el.classList.remove('tab-active'));
        event.target.classList.add('tab-active');
    }
});
</script>
@endpush
@endsection