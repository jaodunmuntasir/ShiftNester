@extends('layouts.app')

@section('title', 'Open Shifts')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">Open Shifts Available for Claiming</h2>

    @if($openShifts->isEmpty())
        <p class="text-gray-600">There are no open shifts available for claiming at this time.</p>
    @else
        @foreach($openShifts as $date => $shifts)
            <div class="mb-6 p-4 bg-white shadow rounded">
                <h3 class="text-xl font-semibold mb-2">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h3>
                <ul class="list-disc pl-5">
                    @foreach($shifts as $openShift)
                        <li>
                            {{ $openShift->shift->start_time->format('H:i') }} - {{ $openShift->shift->end_time->format('H:i') }}
                            ({{ $openShift->department->name }} - {{ $openShift->designation->name }})
                            <form action="{{ route('shifts.claim', $openShift) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                    Claim
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif
</div>
@endsection