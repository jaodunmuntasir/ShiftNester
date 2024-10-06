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
                    {{ $shift->date->format('l, F j, Y') }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})
                </h3>
                
                @foreach($shift->publishedShifts->groupBy('department.name') as $departmentName => $departmentShifts)
                    <div class="mt-3">
                        <h4 class="font-semibold">{{ $departmentName }}</h4>
                        <ul class="list-disc pl-5">
                            @foreach($departmentShifts->groupBy('designation.name') as $designationName => $designationShifts)
                                <li>
                                    {{ $designationName }}:
                                    <ul class="list-none pl-3">
                                        @foreach($designationShifts as $publishedShift)
                                            <li>
                                                @if($publishedShift->is_open)
                                                    <span class="text-orange-600">OPEN</span>
                                                @else
                                                    {{ $publishedShift->employee->name }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection