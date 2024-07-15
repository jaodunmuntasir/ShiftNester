@extends('layouts.app')

@section('title', 'Employee Shift Preferences')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Employee Shift Preferences</h2>

    @foreach($shifts as $shift)
        <div class="mb-6 p-4 bg-white shadow rounded">
            <h3 class="text-xl font-semibold mb-2">
                Shift: {{ $shift->date->format('Y-m-d') }} {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
            </h3>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Employee</th>
                        <th class="text-left">Preference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shift->preferences as $preference)
                        <tr>
                            <td>{{ $preference->employee->name }}</td>
                            <td>
                                @switch($preference->preference_level)
                                    @case(1)
                                        High
                                        @break
                                    @case(2)
                                        Medium
                                        @break
                                    @case(3)
                                        Low
                                        @break
                                    @default
                                        No Preference
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="mt-6">
        <a href="{{ route('admin.generate_roster') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Generate Automatic Roster
        </a>
    </div>
</div>
@endsection