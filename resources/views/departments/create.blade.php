@extends('layouts.app')

@section('title', 'Add New Department')

@section('content')
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div class="flex flex-row mb-1 sm:mb-0 justify-between w-full">
            <h2 class="text-2xl font-semibold text-gray-800"></h2>
            <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
                Back to Departments
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6 p-6">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                    <input type="text" name="name" id="name" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110 focus:outline-none focus:shadow-outline">
                        Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection