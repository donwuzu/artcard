<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Noble Rides') }} – Auth</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="flex w-full max-w-6xl bg-white rounded-lg shadow-lg overflow-hidden">

        <!-- Left (Brand / Image) -->
        <div class="hidden md:block md:w-1/2 bg-green-600 text-white p-10">
            <div class="flex flex-col justify-center h-full">
                <h2 class="text-4xl font-bold mb-4">ArtCard Portraits</h2>
             
                <p class="text-lg">ADMIN</p>

               <img src="https://img.icons8.com/ios-filled/100/ffffff/picture.png" class="w-24 mt-10" alt="Picture Icon">
          
            </div>
        </div>

        <!-- Right (Form) -->
        <div class="w-full md:w-1/2 p-8 sm:p-10">
            @yield('form')
        </div>

    </div>

</body>
</html>