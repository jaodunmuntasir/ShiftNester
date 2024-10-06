@extends('layouts.app')

@section('title', 'Open Shifts')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">Open Shifts Available for Claiming</h2>

    <x-calendar />

    <div id="openShiftDetails" class="mt-4">
        <!-- Open shift details will be displayed here -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openShiftDetails = document.getElementById('openShiftDetails');
    const openShifts = @json($openShifts);

    document.querySelector('.calendar-grid').addEventListener('click', function(e) {
        const day = e.target.closest('.calendar-day');
        if (day) {
            const date = day.dataset.date;
            const shiftsForDate = openShifts[date] || [];
            
            let html = `<h3 class="text-xl font-semibold mb-2">Open Shifts for ${date}</h3>`;
            if (shiftsForDate.length === 0) {
                html += '<p>No open shifts available for this date.</p>';
            } else {
                html += '<div class="list-group">';
                shiftsForDate.forEach(shift => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${shift.shift.start_time} - ${shift.shift.end_time}</h5>
                                <small>${shift.department.name} - ${shift.designation.name}</small>
                            </div>
                            <form action="${route('shifts.claim', shift.id)}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Claim</button>
                            </form>
                        </div>
                    `;
                });
                html += '</div>';
            }
            openShiftDetails.innerHTML = html;
        }
    });
});
</script>
@endsection