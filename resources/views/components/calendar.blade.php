<div class="calendar-container">
    <div class="calendar-header d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-outline-primary" id="prevMonth">&lt; Previous</button>
        <h3 id="currentMonth" class="mb-0"></h3>
        <button class="btn btn-outline-primary" id="nextMonth">Next &gt;</button>
    </div>
    <div class="calendar-grid">
        <!-- Calendar days will be inserted here by JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        selectable: true,
        select: function(info) {
            fetchShiftDetails(info.startStr);
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        }
    });
    calendar.render();

    function fetchShiftDetails(date) {
        fetch(`/admin/shifts/${date}`)
            .then(response => response.json())
            .then(data => {
                displayShiftDetails(data, date);
            });
    }

    function displayShiftDetails(shifts, date) {
        let html = `<h3 class="text-xl font-semibold mb-4">Shifts for ${formatDate(date)}</h3>`;
        if (shifts.length === 0) {
            html += '<p class="text-gray-600">No shifts for this date.</p>';
        } else {
            shifts.forEach(shift => {
                html += `
                    <div class="bg-white shadow-md rounded-lg p-4 mb-4">
                        <h4 class="text-lg font-semibold mb-2">${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}</h4>
                        <ul class="space-y-2">
                `;
                shift.allocations.forEach(allocation => {
                    html += `
                        <li class="flex justify-between items-center">
                            <span>${allocation.department} - ${allocation.designation}</span>
                            <span class="font-medium ${allocation.employee === 'OPEN' ? 'text-red-600' : ''}">${allocation.employee}</span>
                        </li>
                    `;
                });
                html += `
                        </ul>
                    </div>
                `;
            });
        }
        document.getElementById('shiftDetails').innerHTML = html;
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatTime(timeString) {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }
});
</script>

<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}
.calendar-day:hover {
    background-color: #f0f0f0;
}
</style>