<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        /* Smooth Mobile Menu Transition Classes */
        #mobile-menu {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out, visibility 0.3s;
        }
        .menu-hidden {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
            pointer-events: none; /* Prevents clicking when hidden */
        }
        .menu-visible {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            pointer-events: auto;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50">

    @php
        $isGuest = auth()->guest();
        $isAdmin = auth()->check() && auth()->user()->hasRole('admin');

        $homeRoute    = $isGuest ? 'home' : ($isAdmin ? 'admin.home' : 'client.home');
        $clocksRoute  = $isGuest ? 'clocks.index' : ($isAdmin ? 'admin.clocks.index' : 'client.clocks.index');
        $samplesRoute = $isGuest ? 'sample-images.index' : ($isAdmin ? 'admin.samples.index' : 'client.samples.index');
    @endphp

    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16 md:h-20 transition-all duration-300">
                
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2 px-3 py-1 rounded-lg
                              text-xl font-bold tracking-tight text-green-700
                              hover:bg-green-50 transition transform hover:scale-105">
                        <span>ARTCARD</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-2">
                    @php
                        $navLinks = [
                            ['label' => 'Portraits', 'route' => $homeRoute, 'active' => ['home', 'admin.home', 'client.home']],
                            ['label' => '3 Pieces', 'route' => $clocksRoute, 'active' => ['clocks.*', 'admin.clocks.*', 'client.clocks.*']],
                            ['label' => 'Samples', 'route' => $samplesRoute, 'active' => ['sample-images.*', 'sample-clocks.*', 'admin.samples.*', 'client.samples.*']]
                        ];
                    @endphp

                            @foreach($navLinks as $link)
                @if(\Illuminate\Support\Facades\Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200
                            {{ request()->routeIs(...$link['active'])
                                ? 'bg-green-600 text-white shadow-md shadow-green-200'
                                : 'text-slate-600 hover:bg-green-50 hover:text-green-700' }}">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach

                </div>

                <div class="flex items-center space-x-5">
                    @if(!auth()->check() || auth()->user()->hasRole('client'))
                        <a href="{{ auth()->check() ? route('client.cart.index') : route('cart.index') }}"
                           class="group relative flex items-center text-slate-600 hover:text-green-600 transition">
                            <div class="relative">
                                <i class="fas fa-shopping-cart text-xl group-hover:scale-110 transition-transform"></i>
                                </div>
                            <span class="ml-2 text-sm font-medium hidden sm:inline">Cart</span>
                        </a>
                    @endif

                    <button type="button" 
                            id="mobile-menu-button"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-lg 
                                   text-slate-600 hover:text-green-600 hover:bg-green-50 
                                   focus:outline-none transition-colors duration-200"
                            aria-controls="mobile-menu" 
                            aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6 transition-transform duration-300" id="menu-icon-hamburger" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="hidden h-6 w-6 transition-transform duration-300 rotate-90" id="menu-icon-close" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" 
             class="md:hidden absolute top-[64px] md:top-[80px] left-0 w-full 
                    bg-white border-b border-slate-100 shadow-xl 
                    menu-hidden">
            
            <div class="px-4 pt-2 pb-6 space-y-2">
            @foreach($navLinks as $link)
    @if(\Illuminate\Support\Facades\Route::has($link['route']))
        <a href="{{ route($link['route']) }}"
           class="block px-4 py-3 rounded-lg text-base font-semibold transition-all duration-200
                  {{ request()->routeIs(...$link['active'])
                     ? 'bg-green-50 text-green-700 border-l-4 border-green-600 pl-3'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-green-600 hover:pl-6' }}">
            {{ $link['label'] }}
        </a>
    @endif
@endforeach

            </div>
        </div>
    </nav>

    <main class="pt-[70px] md:pt-[90px] min-h-screen">
        @yield('content')
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('menu-icon-hamburger');
            const closeIcon = document.getElementById('menu-icon-close');
            let isMenuOpen = false;

            button.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevent immediate closing when clicking button
                toggleMenu();
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (isMenuOpen && !menu.contains(e.target) && !button.contains(e.target)) {
                    closeMenu();
                }
            });

            function toggleMenu() {
                isMenuOpen = !isMenuOpen;
                updateMenuState();
            }

            function closeMenu() {
                isMenuOpen = false;
                updateMenuState();
            }

            function updateMenuState() {
                if (isMenuOpen) {
                    // Open State
                    menu.classList.remove('menu-hidden');
                    menu.classList.add('menu-visible');
                    
                    // Icon Swap
                    hamburgerIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                    closeIcon.classList.add('block');
                    
                    button.setAttribute('aria-expanded', 'true');
                } else {
                    // Closed State
                    menu.classList.remove('menu-visible');
                    menu.classList.add('menu-hidden');
                    
                    // Icon Swap
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    closeIcon.classList.remove('block');
                    
                    button.setAttribute('aria-expanded', 'false');
                }
            }
        });
    </script>
</body>
</html>