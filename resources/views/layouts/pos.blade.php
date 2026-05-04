<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir POS') - SmartPOS</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏪</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {'50':'#eff6ff','100':'#dbeafe','200':'#bfdbfe','300':'#93c5fd','400':'#60a5fa','500':'#3b82f6','600':'#2563eb','700':'#1d4ed8','800':'#1e40af','900':'#1e3a8a'},
                        success: {'50':'#f0fdf4','500':'#22c55e','600':'#16a34a'},
                        danger: {'50':'#fef2f2','500':'#ef4444','600':'#dc2626'},
                        warning: {'50':'#fffbeb','500':'#f59e0b','600':'#d97706'},
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(darkMode) document.documentElement.classList.add('dark')">
    <div class="flex flex-col h-full">
        <header class="flex-shrink-0 flex items-center justify-between h-14 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-bold text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-store"></i>
                    <span>SmartPOS</span>
                </a>
                <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">@yield('title', 'Kasir POS')</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                    <i class="fa-solid fa-list mr-1"></i> <span class="hidden sm:inline">Daftar Penjualan</span>
                </a>
                <button x-on:click="darkMode = !darkMode" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fa-solid" x-bind:class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button x-on:click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div x-show="open" x-on:click.outside="open = false" x-transition class="absolute right-0 w-48 mt-2 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-50">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <i class="fa-solid fa-user mr-2"></i> Profile
                        </a>
                        <a href="{{ route('settings.business') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <i class="fa-solid fa-gear mr-2"></i> Settings
                        </a>
                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-hidden">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="m-4 p-4 bg-success-50 border border-green-200 text-green-700 rounded-lg flex items-center justify-between">
                    <span><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
                    <button x-on:click="show = false" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="m-4 p-4 bg-danger-50 border border-red-200 text-red-700 rounded-lg flex items-center justify-between">
                    <span><i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}</span>
                    <button x-on:click="show = false" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
