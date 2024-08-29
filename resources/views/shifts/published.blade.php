@extends('layouts.app')

@section('title', 'Published Shifts')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">Published Shifts</h2>

    @if($publishedShifts->isEmpty())
        <p class="text-gray-600">No shifts have been published yet.</p>
    @else
        @foreach($publishedShifts as $shift)
            <div class="mb-6 p-4 bg-white shadow rounded">
                <h3 class="text-xl font-semibold mb-2">
                    {{ $shift->date->format('l, F j, Y') }}
                </h3>
                <p class="text-gray-600 mb-2">
                    {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
                </p>
                <h4 class="font-semibold mt-2 mb-1">Assigned Employees:</h4>
                <ul class="list-disc pl-5">
                    @foreach($shift->employees as $employee)
                        <li>{{ $employee->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif
</div>
@endsection