@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Generated Roster</h2>

    @foreach($shifts as $shift)
        <div class="mb-6 p-4 bg-white shadow rounded">
            <h3 class="text-xl font-semibold mb-2">
                Shift: {{ $shift->date->format('Y-m-d') }} {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
            </h3>
            <ul>
                @foreach($roster[$shift->id] as $employee)
                    <li>{{ $employee->name }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <div class="mt-6">
        <a href="{{ route('admin.publish_shifts') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Publish Shifts
        </a>
    </div>
</div>
@endsection