<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

         <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />



        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    </head>
    <body class="font-sans antialiased">

<nav class="fixed top-0 left-0 right-0 z-[999999] backdrop-blur bg-white shadow-sm">
  <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 py-3">

    <!-- Center Navigation -->
    <div class="flex-1 flex justify-center">
      <div class="flex items-center space-x-2 sm:space-x-4">



            
        @guest
            <a href="{{ route('sample-images.index') }}"
              class="px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-all
                      border border-green-300
                      {{ request()->routeIs('sample-images.*')
                        ? 'text-white bg-green-600 border-green-600'
                        : 'text-slate-700 bg-white hover:bg-green-50 hover:text-green-600' }}">
                Sample Images
            </a>

            <a href="{{ route('sample-clocks.index') }}"
              class="px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-all
                      border border-green-300
                      {{ request()->routeIs('sample-clocks.*')
                        ? 'text-white bg-green-600 border-green-600'
                        : 'text-slate-700 bg-white hover:bg-green-50 hover:text-green-600' }}">
                Sample Clocks
            </a>
        @endguest


        @auth
            <!-- Portraits -->
            <a href="{{ auth()->user()->hasRole('admin') 
                ? route('admin.home') 
                : route('client.home') }}"
               class="px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-all
                      border border-green-300
                      {{ request()->routeIs('admin.home', 'client.home')
                        ? 'text-white bg-green-600 border-green-600'
                        : 'text-slate-700 bg-white hover:bg-green-50 hover:text-green-600' }}">
                Portraits
            </a>

            <!-- Clocks -->
            <a href="{{ auth()->user()->hasRole('admin') 
                ? route('admin.clocks.index') 
                : route('client.clocks.index') }}"
               class="px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-all
                      border border-green-300
                      {{ request()->routeIs('admin.clocks.*', 'client.clocks.*')
                        ? 'text-white bg-green-600 border-green-600'
                        : 'text-slate-700 bg-white hover:bg-green-50 hover:text-green-600' }}">
                Clocks
            </a>
        @endauth

      </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center">
      @auth
        @if(auth()->user()->hasRole('client'))
          <a href="{{ route('client.cart.index') }}"
             class="relative flex items-center text-green-600 hover:text-green-800 transition">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span class="ml-1 text-sm hidden sm:inline">Cart</span>
          </a>
        @endif
      @endauth
    </div>

  </div>
</nav>


        

            <!-- Page Content -->
      <main>
             @yield('content')

      </main>






                     <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


                
      
  

    </body>
</html>
