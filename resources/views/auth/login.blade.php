@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-[1000px]">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden flex flex-col lg:flex-row">
        {{-- Left panel --}}
        <div class="lg:w-1/2 bg-gradient-to-br from-primary-600 to-primary-800 p-8 sm:p-12 flex flex-col justify-center text-white">
            <div class="mb-6">
                <i class="fa-solid fa-store text-5xl"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">SmartPOS</h1>
            <p class="mt-3 text-primary-100 text-lg">Sistem Point of Sale Modern</p>
            <p class="mt-6 text-primary-200 text-sm leading-relaxed">Kelola penjualan, pembelian, stok, dan laporan bisnis Anda dalam satu platform yang mudah digunakan.</p>
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-sm text-primary-100">
                    <i class="fa-solid fa-circle-check text-primary-300"></i> Manajemen inventori real-time
                </div>
                <div class="flex items-center gap-3 text-sm text-primary-100">
                    <i class="fa-solid fa-circle-check text-primary-300"></i> Laporan keuangan lengkap
                </div>
                <div class="flex items-center gap-3 text-sm text-primary-100">
                    <i class="fa-solid fa-circle-check text-primary-300"></i> Multi-user & role-based access
                </div>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="lg:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan login ke akun Anda</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-danger-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> Email atau password salah.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="email@bisnis.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" x-bind:type="show ? 'text' : 'password'" required autocomplete="current-password"
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="••••••••">
                        <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fa-solid" x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        Ingat saya
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-500 font-medium">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Login
                </button>
            </form>

            @if(Route::has('register'))
                <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-500 font-medium">Daftar sekarang</a>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
