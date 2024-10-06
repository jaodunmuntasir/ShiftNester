@extends('layouts.app')

@section('title', 'Draft Roster')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Generated Roster</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-calendar />

    <div id="shiftDetails" class="mt-4">
        <!-- Shift details will be displayed here -->
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.publish_shifts') }}" class="btn btn-success">
            Proceed to Publish Shifts
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shiftDetails = document.getElementById('shiftDetails');
    const generatedShifts = @json($generatedShifts);

    document.querySelector('.calendar-grid').addEventListener('click', function(e) {
        const day = e.target.closest('.calendar-day');
        if (day) {
            const date = day.dataset.date;
            const shiftsForDate = generatedShifts[date] || [];
            
            let html = `<h3 class="text-xl font-semibold mb-2">Shifts for ${date}</h3>`;
            if (shiftsForDate.length === 0) {
                html += '<p>No shifts generated for this date.</p>';
            } else {
                html += '<ul class="list-group">';
                shiftsForDate.forEach(shift => {
                    html += `
                        <li class="list-group-item">
                            <strong>${shift.department.name} - ${shift.designation.name}:</strong>
                            ${shift.is_open ? '<span class="text-danger">OPEN</span>' : shift.employee.name}
                        </li>
                    `;
                });
                html += '</ul>';
            }
            shiftDetails.innerHTML = html;
        }
    });
});
</script>
@endsection