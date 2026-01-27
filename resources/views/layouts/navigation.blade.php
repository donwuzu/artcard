<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div> --}}

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') || request()->routeIs('expenses.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <div>{{ __('Portraits') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                                    {{ __('Portrait Dashboard') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.expenses.index')" :active="request()->routeIs('expenses.index')">
                                    {{ __('Portraits Expenses') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.expenses.report')" :active="request()->routeIs('expenses.report')">
                                    {{ __('Portraits Reports') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('clocks.*') || request()->routeIs('clockExpenses.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <div>{{ __('3 Piece') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- <x-dropdown-link :href="route('admin.clocks.dashboard')" :active="request()->routeIs('clocks.dashboard')">
                                    {{ __('3 Piece Dashboard') }}
                                </x-dropdown-link> -->
                                <x-dropdown-link :href="route('admin.clockExpenses.index')" :active="request()->routeIs('clockExpenses.index')">
                                    {{ __('3 Piece Expenses') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.clockExpenses.report')" :active="request()->routeIs('clockExpenses.report')">
                                    {{ __('3 Piece Reports') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>



         <div class="hidden sm:flex sm:items-center sm:ms-6">
 <x-dropdown align="left" width="48">

    {{-- Trigger --}}
    <x-slot name="trigger">
        <button
            class="inline-flex items-center gap-1 px-1 pt-1 border-b-2 text-sm font-semibold
                   transition duration-150 ease-in-out
            {{ request()->routeIs('admin.sample-images.*')
                ? 'border-green-600 text-green-700'
                : 'border-transparent text-gray-500 hover:text-green-600 hover:border-green-400' }}">

            <span>{{ __('Sample Images') }}</span>

            <svg class="fill-current h-4 w-4 opacity-70"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </button>
    </x-slot>

    {{-- Content --}}
    <x-slot name="content">
        <x-dropdown-link
            :href="route('admin.sample-images.dashboard')"
            :active="request()->routeIs('admin.sample-images.*')">
            {{ __('Manage Sample Images') }}
        </x-dropdown-link>

           <x-dropdown-link
            :href="route('admin.sample-clocks.dashboard')"
            :active="request()->routeIs('admin.sample-clocks.*')">
            {{ __('Manage Sample Clocks') }}
        </x-dropdown-link>
    </x-slot>

</x-dropdown>



</div>





                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('admin.profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>



<!-- MOBILE SCREEN MENU BUTTON -->

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>




<!-- MOBILE SCREEN MENU -->




   <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white">

    {{-- Home --}}
    <div class="pt-2 pb-3 space-y-1 px-3">
        <x-responsive-nav-link
            :href="route('home')"
            :active="request()->routeIs('home')">
            🏠 {{ __('Home') }}
        </x-responsive-nav-link>
    </div>

    {{-- Portraits --}}
    <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="px-4 text-xs uppercase tracking-wider text-gray-500 font-semibold">
            Portraits
        </div>

        <div class="mt-2 space-y-1 px-3">
            <x-responsive-nav-link
                :href="route('admin.dashboard')"
                :active="request()->routeIs('admin.dashboard')">
                📸 Portrait Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('admin.expenses.index')"
                :active="request()->routeIs('admin.expenses.index')">
                💸 Portrait Expenses
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('admin.expenses.report')"
                :active="request()->routeIs('admin.expenses.report')">
                📊 Portrait Reports
            </x-responsive-nav-link>
        </div>
    </div>

    {{-- Clocks --}}
    <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="px-4 text-xs uppercase tracking-wider text-gray-500 font-semibold">
            Clocks
        </div>

        <div class="mt-2 space-y-1 px-3">
            <x-responsive-nav-link
                :href="route('admin.clocks.dashboard')"
                :active="request()->routeIs('admin.clocks.*')">
                🕰️ Clock Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('admin.clockExpenses.index')"
                :active="request()->routeIs('admin.clockExpenses.index')">
                💸 Clock Expenses
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('admin.clockExpenses.report')"
                :active="request()->routeIs('admin.clockExpenses.report')">
                📊 Clock Reports
            </x-responsive-nav-link>
        </div>
    </div>

    {{-- Samples --}}
    <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="px-4 text-xs uppercase tracking-wider text-gray-500 font-semibold">
            Samples
        </div>

        <div class="mt-2 space-y-1 px-3">
            <x-responsive-nav-link
                :href="route('admin.sample-images.dashboard')"
                :active="request()->routeIs('admin.sample-images.*')">
                🖼️ Sample Images
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('admin.sample-clocks.dashboard')"
                :active="request()->routeIs('admin.sample-clocks.*')">
                🕰️ Sample Clocks
            </x-responsive-nav-link>
        </div>
    </div>

    {{-- User --}}
    <div class="pt-4 pb-4 border-t border-gray-200">
        <div class="px-4">
            <div class="font-medium text-gray-800">
                {{ Auth::user()->name }}
            </div>
            <div class="text-sm text-gray-500">
                {{ Auth::user()->email }}
            </div>
        </div>

        <div class="mt-3 space-y-1 px-3">
            <x-responsive-nav-link :href="route('admin.profile.edit')">
                👤 Profile
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link
                    :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    🚪 Log Out
                </x-responsive-nav-link>
            </form>
        </div>
    </div>

</div>

</nav>