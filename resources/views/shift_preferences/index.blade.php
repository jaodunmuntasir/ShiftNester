@extends('layouts.app')

@section('title', 'Shift Preferences')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Shift Preferences</h2>
        <button type="submit" form="preferencesForm" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:shadow-outline">
            Save Preferences
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form id="preferencesForm" action="{{ route('shift_preferences.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($availableShifts as $shift)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
                    <div class="bg-gray-100 px-4 py-2">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $shift->date->format('M d, Y') }}</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600 mb-4">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</p>
                        <div class="mb-4">
                            <label for="preference_{{ $shift->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                Preference Level
                            </label>
                            <input type="range" id="preference_{{ $shift->id }}" name="preferences[{{ $shift->id }}]" 
                                   min="0" max="3" step="1" 
                                   value="{{ $preferences[$shift->id] ?? 0 }}"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>No Preference</span>
                            <span>Highest</span>
                        </div>
                        <p class="text-center mt-2 font-semibold preference-label" id="label_{{ $shift->id }}"></p>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preferenceLabels = ['No Preference', 'Highest', 'Medium', 'Low'];
    const sliders = document.querySelectorAll('input[type="range"]');
    
    sliders.forEach(slider => {
        const label = document.getElementById('label_' + slider.id.split('_')[1]);
        updateLabel(slider, label);
        
        slider.addEventListener('input', function() {
            updateLabel(this, label);
        });
    });
    
    function updateLabel(slider, label) {
        label.textContent = preferenceLabels[slider.value];
    }
});
</script>
@endpush
@endsection