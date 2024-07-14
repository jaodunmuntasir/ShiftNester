@extends('layouts.app')

@section('title', 'Set Shift Requirements')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Set Shift Requirements</h2>
    </div>

    <form action="{{ route('shifts.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            @foreach($shifts as $index => $shift)
                <div class="mb-6 p-4 border rounded">
                    <h3 class="text-lg font-semibold mb-2">{{ $shift['start_time'] }} - {{ $shift['end_time'] }}</h3>
                    <input type="hidden" name="shifts[{{ $index }}][date]" value="{{ $shift['date'] }}">
                    <input type="hidden" name="shifts[{{ $index }}][start_time]" value="{{ $shift['start_time'] }}">
                    <input type="hidden" name="shifts[{{ $index }}][end_time]" value="{{ $shift['end_time'] }}">
                    @foreach($departments as $department)
                        <div class="mb-4">
                            <h4 class="text-md font-semibold mb-2">{{ $department->name }}</h4>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($department->designations as $designation)
                                    <div>
                                        <label for="req_{{ $index }}_{{ $department->id }}_{{ $designation->id }}" class="block text-gray-700 text-sm font-bold mb-2">{{ $designation->name }}:</label>
                                        <input type="number" name="requirements[{{ $index }}][{{ $department->id }}][{{ $designation->id }}]" id="req_{{ $index }}_{{ $department->id }}_{{ $designation->id }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0" value="0" required>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Publish Shifts
            </button>
        </div>
    </form>
@endsection