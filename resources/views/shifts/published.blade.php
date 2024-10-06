@extends('layouts.app')

@section('title', 'Published Shifts')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">Published Shifts</h2>

    <x-calendar />

    <div id="publishedShiftDetails" class="mt-4">
        <!-- Published shift details will be displayed here -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const publishedShiftDetails = document.getElementById('publishedShiftDetails');
    const publishedShifts = @json($publishedShifts);

    document.querySelector('.calendar-grid').addEventListener('click', function(e) {
        const day = e.target.closest('.calendar-day');
        if (day) {
            const date = day.dataset.date;
            const shiftsForDate = publishedShifts.filter(shift => shift.date === date);
            
            let html = `<h3 class="text-xl font-semibold mb-2">Published Shifts for ${date}</h3>`;
            if (shiftsForDate.length === 0) {
                html += '<p>No published shifts for this date.</p>';
            } else {
                shiftsForDate.forEach(shift => {
                    html += `
                        <div class="card mb-3">
                            <div class="card-header">
                                ${shift.start_time} - ${shift.end_time}
                            </div>
                            <div class="card-body">
                    `;
                    shift.publishedShifts.forEach(pubShift => {
                        html += `
                            <div class="mb-2">
                                <strong>${pubShift.department.name} - ${pubShift.designation.name}:</strong>
                                ${pubShift.is_open ? '<span class="text-danger">OPEN</span>' : pubShift.employee.name}
                            </div>
                        `;
                    });
                    html += `
                            </div>
                        </div>
                    `;
                });
            }
            publishedShiftDetails.innerHTML = html;
        }
    });
});
</script>
@endsection