@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('breadcrumb')
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('products.index') }}" class="hover:text-primary-600">Produk</a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <span class="text-gray-700 dark:text-gray-200">Tambah</span>
    </nav>
@endsection

@section('content')
<div x-data="{ type: 'single', enableStock: true, purchasePrice: 0, profitPercent: 0 }">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Tambah Produk Baru</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi data produk berikut dengan lengkap.</p>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Produk <span class="text-danger-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('name') border-danger-500 @enderror">
                @error('name')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- SKU --}}
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU <span class="text-danger-500">*</span></label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('sku') border-danger-500 @enderror">
                @error('sku')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Barcode Number --}}
            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Barcode</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Masukkan nomor barcode produk" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('barcode') border-danger-500 @enderror">
                @error('barcode')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Barcode Type --}}
            <div>
                <label for="barcode_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Barcode</label>
                <select name="barcode_type" id="barcode_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('barcode_type') border-danger-500 @enderror">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="C128" {{ old('barcode_type', 'C128') == 'C128' ? 'selected' : '' }}>Code 128</option>
                    <option value="C39" {{ old('barcode_type') == 'C39' ? 'selected' : '' }}>Code 39</option>
                    <option value="EAN13" {{ old('barcode_type') == 'EAN13' ? 'selected' : '' }}>EAN-13</option>
                    <option value="EAN8" {{ old('barcode_type') == 'EAN8' ? 'selected' : '' }}>EAN-8</option>
                    <option value="UPCA" {{ old('barcode_type') == 'UPCA' ? 'selected' : '' }}>UPC-A</option>
                    <option value="UPCE" {{ old('barcode_type') == 'UPCE' ? 'selected' : '' }}>UPC-E</option>
                </select>
                @error('barcode_type')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Produk <span class="text-danger-500">*</span></label>
                <select name="type" id="type" x-model="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('type') border-danger-500 @enderror">
                    <option value="single">Produk Satuan (Single)</option>
                    <option value="variable">Produk Variasi (Variable)</option>
                </select>
                @error('type')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Unit --}}
            <div>
                <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Satuan <span class="text-danger-500">*</span></label>
                <select name="unit_id" id="unit_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('unit_id') border-danger-500 @enderror">
                    <option value="">-- Pilih Satuan --</option>
                    @foreach($units ?? [] as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->actual_name }} ({{ $unit->short_name }})</option>
                    @endforeach
                </select>
                @error('unit_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Brand --}}
            <div>
                <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Merek</label>
                <select name="brand_id" id="brand_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('brand_id') border-danger-500 @enderror">
                    <option value="">-- Pilih Merek --</option>
                    @foreach($brands ?? [] as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
                @error('brand_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Category --}}
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('category_id') border-danger-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Image --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gambar Produk</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                @error('image')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Tax --}}
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pajak</label>
                <select name="tax_id" id="tax_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('tax_id') border-danger-500 @enderror">
                    <option value="">-- Tidak Ada --</option>
                    @foreach($taxRates ?? [] as $tax)
                        <option value="{{ $tax->id }}" {{ old('tax_id') == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ $tax->amount }}%)</option>
                    @endforeach
                </select>
                @error('tax_id')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>

            {{-- Tax Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Pajak</label>
                <div class="flex gap-4 mt-2">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer">
                        <input type="radio" name="tax_type" value="inclusive" {{ old('tax_type', 'inclusive') == 'inclusive' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                        Inklusif (Termasuk)
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer">
                        <input type="radio" name="tax_type" value="exclusive" {{ old('tax_type') == 'exclusive' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                        Eksklusif (Tidak Termasuk)
                    </label>
                </div>
                @error('tax_type')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Enable Stock & Alert Quantity --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-3">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer pt-1">
                    <input type="checkbox" name="enable_stock" value="1" x-model="enableStock" {{ old('enable_stock') ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                    <span class="font-medium">Aktifkan Manajemen Stok</span>
                </label>
            </div>
            <div x-show="enableStock">
                <label for="alert_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notifikasi Stok Minimum</label>
                <input type="number" name="alert_quantity" id="alert_quantity" value="{{ old('alert_quantity', 0) }}" min="0" step="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('alert_quantity') border-danger-500 @enderror">
                @error('alert_quantity')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Pricing --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div>
                <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Beli</label>
                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" step="0.01" min="0" x-model="purchasePrice" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('purchase_price') border-danger-500 @enderror">
                @error('purchase_price')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="profit_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Margin Keuntungan (%)</label>
                <input type="number" name="profit_percent" id="profit_percent" value="{{ old('profit_percent', 0) }}" step="0.01" min="0" x-model="profitPercent" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('profit_percent') border-danger-500 @enderror">
                @error('profit_percent')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="sell_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Jual</label>
                <input type="number" name="sell_price" id="sell_price" value="{{ old('sell_price', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('sell_price') border-danger-500 @enderror">
                <p class="mt-1 text-xs text-gray-400" x-show="purchasePrice > 0 && profitPercent > 0">Estimasi: <span x-text="new Intl.NumberFormat('id-ID').format(Math.round(purchasePrice * (1 + profitPercent/100)))"></span></p>
                @error('sell_price')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Description --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <label for="product_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Produk</label>
            <textarea name="product_description" id="product_description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none @error('product_description') border-danger-500 @enderror">{{ old('product_description') }}</textarea>
            @error('product_description')<p class="mt-1 text-xs text-danger-500">{{ $message }}</p>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
