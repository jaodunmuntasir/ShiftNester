@extends('layouts.app')

@section('title', 'Set Shift Requirements')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Set Shift Requirements</h2>
    </div>

    <form action="{{ route('shifts.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6">
            @foreach($shifts as $index => $shift)
                <div class="mb-8 p-6 border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ \Carbon\Carbon::parse($shift['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($shift['end_time'])->format('H:i') }}</h3>
                    <input type="hidden" name="shifts[{{ $index }}][date]" value="{{ $shift['date'] }}">
                    <input type="hidden" name="shifts[{{ $index }}][start_time]" value="{{ $shift['start_time'] }}">
                    <input type="hidden" name="shifts[{{ $index }}][end_time]" value="{{ $shift['end_time'] }}">
                    @foreach($departments as $department)
                        <div class="mb-6">
                            <h4 class="text-lg font-medium mb-3 text-gray-700">{{ $department->name }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($department->designations as $designation)
                                    <div class="relative">
                                        <label for="req_{{ $index }}_{{ $department->id }}_{{ $designation->id }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $designation->name }}</label>
                                        <input type="number" 
                                               name="requirements[{{ $index }}][{{ $department->id }}][{{ $designation->id }}]" 
                                               id="req_{{ $index }}_{{ $department->id }}_{{ $designation->id }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" 
                                               min="0" 
                                               value="0" 
                                               required>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                Publish Shifts
            </button>
        </div>
    </form>
</div>
@endsection