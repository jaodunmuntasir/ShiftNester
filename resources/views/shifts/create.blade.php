@extends('layouts.app')

@section('title', 'Create Shifts')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800"></h2>
        <a href="{{ route('shifts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
            Back to Shifts
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6">
        <form action="{{ route('shifts.generate') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="date" id="date" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="opening_time" class="block text-sm font-medium text-gray-700 mb-2">Opening Time</label>
                    <input type="time" name="opening_time" id="opening_time" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="closing_time" class="block text-sm font-medium text-gray-700 mb-2">Closing Time</label>
                    <input type="time" name="closing_time" id="closing_time" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:shadow-outline">
                    Generate Shifts
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#date", {
        dateFormat: "Y-m-d",
        minDate: "today",
    });

    flatpickr("#opening_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });

    flatpickr("#closing_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });
});
</script>
@endpush
@endsection