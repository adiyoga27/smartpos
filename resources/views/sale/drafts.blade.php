@extends('layouts.app')

@section('title', 'Transaksi Tunda')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('dashboard') }}" class="hover:text-primary-600"><i class="fa-solid fa-home mr-1"></i> Dashboard</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-700 dark:text-gray-200 font-medium">Transaksi Tunda</li>
        </ol>
    </nav>
@endsection

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-pause mr-2 text-primary-500"></i> Transaksi Tunda
        </h2>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Pelanggan</th>
                        <th class="text-center px-4 py-3">Jumlah Item</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($drafts as $draft)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $draft->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $draft->contact?->full_name ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $draft->items_count ?? $draft->items?->count() ?? 0 }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($draft->final_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('pos.store', $draft) }}" class="p-1.5 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors" title="Lanjutkan">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                    <form action="{{ route('sales.drafts.destroy', $draft) }}" method="POST" onsubmit="return confirm('Hapus transaksi tunda ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-pause text-3xl mb-2 block"></i>
                                <p>Tidak ada transaksi tunda</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($drafts->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $drafts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
