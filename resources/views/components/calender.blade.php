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
    const calendarGrid = document.querySelector('.calendar-grid');
    const currentMonthElement = document.getElementById('currentMonth');
    let currentDate = new Date();

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        currentMonthElement.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;
        
        calendarGrid.innerHTML = '';
        
        for (let i = 0; i < firstDay.getDay(); i++) {
            calendarGrid.innerHTML += '<div></div>';
        }
        
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            calendarGrid.innerHTML += `
                <div class="calendar-day" data-date="${dateString}">
                    <span>${day}</span>
                </div>
            `;
        }
    }

    renderCalendar(currentDate);

    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
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