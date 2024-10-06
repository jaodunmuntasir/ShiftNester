<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShiftNester | Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <div class="text-center mb-8">
                <img src="https://shorturl.at/lC0oN" alt="KFC Logo" class="mx-auto h-16 mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ShiftNester</h1>
                <p class="text-gray-600">Efficient shift management for your employees</p>
            </div>
            
            <div class="space-y-4">
                <a href="{{ route('login') }}" class="block w-full bg-red-600 text-white text-center py-3 rounded-md font-semibold hover:bg-red-700 transition duration-300">
                    Employee Login
                </a>
                <a href="{{ route('register') }}" class="block w-full bg-gray-200 text-gray-800 text-center py-3 rounded-md font-semibold hover:bg-gray-300 transition duration-300">
                    New Employee Registration
                </a>
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; 2024 ShiftNester. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>