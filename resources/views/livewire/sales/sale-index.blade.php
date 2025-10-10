<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Daftar Penjualan') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola semua penjualan Anda di sini') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    @session('success')
        <div class="flex items-center p-2 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300 dark:border-green-800"
            role="alert">
            <svg class="flex-shrink-0 w-8 h-8 mr-1 text-green-700 dark:text-green-300" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
            </svg>
            <span class="font-medium"> {{ $value }} </span>
        </div>
    @endsession
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Filter Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Cari</label>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari berdasarkan kode penjualan atau nama pelanggan..."
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Mulai</label>
                    <input type="date" wire:model.live="startDate"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Akhir</label>
                    <input type="date" wire:model.live="endDate"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button wire:click="clearFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                    Bersihkan Filter
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Author</th>
                        <th class="px-6 py-3">Grand Total</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $sale->sale_code }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $sale->sale_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $sale->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($sale->status == App\Models\Sale::STATUS_PAID)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $sale->status_label }}
                                    </span>
                                @elseif ($sale->status == App\Models\Sale::STATUS_PARTIAL)
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $sale->status_label }}
                                    </span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $sale->status_label }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    @can('sale.view')
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                    @endcan

                                    <!-- Edit button - only shown if not fully paid -->
                                    @if ($sale->status != App\Models\Sale::STATUS_PAID)
                                        @can('sale.edit')
                                            <a href="{{ route('sales.edit', $sale->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endcan
                                    @endif

                                    <!-- Delete button - only shown if not fully paid -->
                                    @if ($sale->status != App\Models\Sale::STATUS_PAID)
                                        @can('sale.delete')
                                            <button wire:click="delete({{ $sale->id }})"
                                                onclick="confirm('Apakah Anda yakin ingin menghapus penjualan ini?') || event.stopImmediatePropagation()"
                                                class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data penjualan yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 border-t border-gray-200">
            {{ $sales->links() }}
        </div>
    </div>
    <!-- Action Button -->
    <div class="fixed bottom-8 right-8">
        @can('sale.create')
            <a href="{{ route('sales.create') }}"
                class="flex items-center justify-center w-14 h-14 bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 transition">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
            </a>
        @endcan
    </div>
</div>
