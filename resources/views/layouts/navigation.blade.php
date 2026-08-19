<!-- Added Google Fonts for Fahkwang -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* --- Brand Variables & Fonts --- */
    :root {
        --soma-primary: #BE9676; /* Perfect Beige */
        --soma-secondary: #8D7E71; /* Desert Taupe */
        --soma-bg: #FFF7E9; /* Soft Cream */
    }

    .soma-navigation, .soma-navigation * {
        font-family: 'Fahkwang', sans-serif !important;
    }

    /* Apply Brand Background and Border */
    .soma-navigation {
        background-color: var(--soma-bg) !important;
        border-bottom: 1px solid rgba(141, 126, 113, 0.2) !important;
    }

    /* Override Tailwind gray colors to match Soma brand */
    .soma-navigation .text-gray-800,
    .soma-navigation .text-gray-700 {
        color: var(--soma-secondary) !important;
    }

    .soma-navigation .text-gray-500,
    .soma-navigation .text-gray-400 {
        color: rgba(141, 126, 113, 0.8) !important;
    }

    .soma-navigation .bg-white {
        background-color: transparent !important; /* Allow Soft Cream to show through */
    }

    .soma-navigation .hover\:text-gray-700:hover,
    .soma-navigation .hover\:text-gray-500:hover {
        color: var(--soma-primary) !important;
    }

    .soma-navigation .hover\:bg-gray-100:hover,
    .soma-navigation .focus\:bg-gray-100:focus {
        background-color: rgba(190, 150, 118, 0.1) !important;
    }

    .soma-navigation .border-gray-200 {
        border-color: rgba(141, 126, 113, 0.2) !important;
    }

    /* Override default Laravel Breeze Indigo active states */
    .soma-navigation .border-indigo-400,
    .soma-navigation .border-indigo-500 {
        border-color: var(--soma-primary) !important;
    }
    
    .soma-navigation .text-indigo-700,
    .soma-navigation .text-indigo-600 {
        color: var(--soma-secondary) !important;
    }

    .soma-navigation .bg-indigo-50 {
        background-color: rgba(190, 150, 118, 0.1) !important;
    }
</style>

<nav x-data="{ open: false }" class="soma-navigation bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>