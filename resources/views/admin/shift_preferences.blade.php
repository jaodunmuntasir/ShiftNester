@extends('layouts.app')

@section('title', 'Employee Shift Preferences')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Employee Shift Preferences</h2>

    <x-calendar />

    <div id="preferenceDetails" class="mt-4">
        <!-- Preference details will be displayed here -->
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.generate_roster') }}" class="btn btn-primary">
            Generate Automatic Roster
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const preferenceDetails = document.getElementById('preferenceDetails');
    const shifts = @json($shifts);

    document.querySelector('.calendar-grid').addEventListener('click', function(e) {
        const day = e.target.closest('.calendar-day');
        if (day) {
            const date = day.dataset.date;
            const shiftsForDate = shifts.filter(shift => shift.date === date);
            
            let html = `<h3 class="text-xl font-semibold mb-2">Preferences for ${date}</h3>`;
            if (shiftsForDate.length === 0) {
                html += '<p>No shifts scheduled for this date.</p>';
            } else {
                shiftsForDate.forEach(shift => {
                    html += `
                        <div class="card mb-3">
                            <div class="card-header">
                                ${shift.start_time} - ${shift.end_time}
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Preference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;
                    shift.preferences.forEach(pref => {
                        html += `
                            <tr>
                                <td>${pref.employee.name}</td>
                                <td>${['', 'High', 'Medium', 'Low'][pref.preference_level] || 'No Preference'}</td>
                            </tr>
                        `;
                    });
                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                });
            }
            preferenceDetails.innerHTML = html;
        }
    });
});
</script>
@endsection