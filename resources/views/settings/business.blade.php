@extends('layouts.app')

@section('title', 'Business Settings')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Settings</li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Business</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-5">Business Settings</h3>
        <form action="{{ route('settings.business.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Name</label>
                <input type="text" name="name" value="{{ old('name', $settings->name ?? '') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Number</label>
                <input type="text" name="tax_number_1" value="{{ old('tax_number_1', $settings->tax_number_1 ?? '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Zone</label>
                <select name="time_zone" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select timezone</option>
                    <option value="Asia/Jakarta" {{ ($settings->time_zone ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar" {{ ($settings->time_zone ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura" {{ ($settings->time_zone ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                    <option value="Asia/Singapore" {{ ($settings->time_zone ?? '') === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore</option>
                    <option value="UTC" {{ ($settings->time_zone ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Format</label>
                <select name="date_format" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="d-m-Y" {{ ($settings->date_format ?? '') === 'd-m-Y' ? 'selected' : '' }}>{{ date('d-m-Y') }} (dd-mm-yyyy)</option>
                    <option value="m-d-Y" {{ ($settings->date_format ?? '') === 'm-d-Y' ? 'selected' : '' }}>{{ date('m-d-Y') }} (mm-dd-yyyy)</option>
                    <option value="Y-m-d" {{ ($settings->date_format ?? '') === 'Y-m-d' ? 'selected' : '' }}>{{ date('Y-m-d') }} (yyyy-mm-dd)</option>
                    <option value="d/m/Y" {{ ($settings->date_format ?? '') === 'd/m/Y' ? 'selected' : '' }}>{{ date('d/m/Y') }} (dd/mm/yyyy)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Format</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="time_format" value="12" {{ ($settings->time_format ?? '') === '12' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">12-hour</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="time_format" value="24" {{ ($settings->time_format ?? '24') === '24' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">24-hour</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sell Price Tax</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="sell_price_tax" value="includes" {{ ($settings->sell_price_tax ?? '') === 'includes' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Includes Tax</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="sell_price_tax" value="excludes" {{ ($settings->sell_price_tax ?? '') === 'excludes' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Excludes Tax</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Theme Color</label>
                <select name="theme_color" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="blue" {{ ($settings->theme_color ?? '') === 'blue' ? 'selected' : '' }}>Blue (Default)</option>
                    <option value="green" {{ ($settings->theme_color ?? '') === 'green' ? 'selected' : '' }}>Green</option>
                    <option value="purple" {{ ($settings->theme_color ?? '') === 'purple' ? 'selected' : '' }}>Purple</option>
                    <option value="orange" {{ ($settings->theme_color ?? '') === 'orange' ? 'selected' : '' }}>Orange</option>
                    <option value="teal" {{ ($settings->theme_color ?? '') === 'teal' ? 'selected' : '' }}>Teal</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
