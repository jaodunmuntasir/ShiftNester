@extends('layouts.app')

@section('title', 'Shift Preferences')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Shift Preferences</h2>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('shift_preferences.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Time
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Preference
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($availableShifts as $shift)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $shift->date->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <select name="preferences[{{ $shift->id }}]" class="form-select mt-1 block w-full">
                                    <option value="">No Preference</option>
                                    <option value="1" {{ isset($preferences[$shift->id]) && $preferences[$shift->id] == 1 ? 'selected' : '' }}>1 (Highest)</option>
                                    <option value="2" {{ isset($preferences[$shift->id]) && $preferences[$shift->id] == 2 ? 'selected' : '' }}>2 (Medium)</option>
                                    <option value="3" {{ isset($preferences[$shift->id]) && $preferences[$shift->id] == 3 ? 'selected' : '' }}>3 (Lowest)</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save Preferences
            </button>
        </div>
    </form>
@endsection