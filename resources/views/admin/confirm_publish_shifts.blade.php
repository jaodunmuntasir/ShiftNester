@extends('layouts.app')

@section('title', 'Confirm Publish Shifts')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">Confirm Publish Shifts</h2>
    
    <p class="mb-4">Are you sure you want to publish the generated shifts? This action cannot be undone.</p>
    
    <form action="{{ route('admin.publish_shifts') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Confirm and Publish Shifts
        </button>
        <a href="{{ route('admin.view_shift_preferences') }}" class="ml-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Cancel
        </a>
    </form>
</div>
@endsection