@extends('layouts.app')

@section('title', 'Generated Roster')

@section('content')
<div class="container">
    <h2 class="mb-4">Generated Roster</h2>

    <div class="row">
        <div class="col-md-8">
            <div id="calendar"></div>
        </div>
        <div class="col-md-4">
            <div id="shiftDetails" class="card">
                <div class="card-body">
                    <h5 class="card-title">Shift Details</h5>
                    <div id="shiftList"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.publish_shifts') }}" class="btn btn-primary">Proceed to Publish Shifts</a>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: @json($calendarEvents),
        eventClick: function(info) {
            fetchShiftDetails(info.event.start);
        }
    });
    calendar.render();

    function fetchShiftDetails(date) {
        fetch(`/admin/shifts/${date.toISOString().split('T')[0]}`)
            .then(response => response.json())
            .then(data => {
                displayShiftDetails(data);
            });
    }

    function displayShiftDetails(shifts) {
        let html = '';
        if (shifts.length === 0) {
            html = '<p>No shifts for this date.</p>';
        } else {
            html = '<ul class="list-group">';
            shifts.forEach(shift => {
                html += `
                    <li class="list-group-item">
                        <strong>${shift.start_time} - ${shift.end_time}</strong>
                        <ul>
                `;
                shift.allocations.forEach(allocation => {
                    html += `
                        <li>${allocation.department} - ${allocation.designation}: ${allocation.employee}</li>
                    `;
                });
                html += `
                        </ul>
                    </li>
                `;
            });
            html += '</ul>';
        }
        document.getElementById('shiftList').innerHTML = html;
    }
});
</script>
@endpush
@endsection