@extends('layouts.app')

@section('title', 'Draft Roster')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Generated Roster</h2>

    @foreach($shifts as $shift)
        <div class="mb-6 p-4 bg-white shadow rounded">
            <h3 class="text-xl font-semibold mb-2">
                Shift: {{ $shift->date->format('Y-m-d') }} {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
            </h3>
            @foreach($shift->requirements as $requirement)
                <h4 class="font-semibold mt-2">{{ $requirement->department->name }} - {{ $requirement->designation->name }}</h4>
                <ul>
                    @foreach($generatedRoster[$shift->id] as $allocation)
                        @if(is_string($allocation) && str_contains($allocation, "{$requirement->department->name} - {$requirement->designation->name}"))
                            <li class="text-red-500">{{ $allocation }}</li>
                        @elseif(is_object($allocation) && $allocation->department_id == $requirement->department_id && $allocation->designation_id == $requirement->designation_id)
                            <li>{{ $allocation->name }}</li>
                        @endif
                    @endforeach
                </ul>
            @endforeach
        </div>
    @endforeach

    <div class="mt-6">
        <a href="{{ route('admin.publish_shifts') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Proceed to Publish Shifts
        </a>
    </div>
</div>
@endsection