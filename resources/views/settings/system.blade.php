@extends('layouts.app')

@section('title', 'System Settings')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a></li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> Settings</li>
            <li class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i> System</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-5">System Settings</h3>
        <form action="{{ route('settings.system') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">App Version</label>
                <input type="text" name="app_version" value="{{ old('app_version', $settings->app_version ?? '1.0.0') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance Mode</label>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Disable the application for all users except admins</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings->maintenance_mode ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-700 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600"></div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Language</label>
                <select name="default_language" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="en" {{ ($settings->default_language ?? '') === 'en' ? 'selected' : '' }}>English</option>
                    <option value="id" {{ ($settings->default_language ?? '') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Currency</label>
                <select name="currency" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="IDR" {{ ($settings->currency ?? 'IDR') === 'IDR' ? 'selected' : '' }}>IDR (Indonesian Rupiah)</option>
                    <option value="USD" {{ ($settings->currency ?? '') === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Items Per Page</label>
                <input type="number" name="items_per_page" value="{{ old('items_per_page', $settings->items_per_page ?? 25) }}" min="5" max="100" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice Footer Text</label>
                <textarea name="invoice_footer" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2 focus:ring-primary-500 focus:border-primary-500">{{ old('invoice_footer', $settings->invoice_footer ?? '') }}</textarea>
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
