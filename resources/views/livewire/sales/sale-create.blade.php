<div>
    <div x-data="{ showModal: false }" x-on:toggle-item-modal.window="showModal = !showModal"
        x-on:keydown.escape.window="showModal = false">
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Tambah Penjualan Baru') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Isi detail transaksi di bawah ini') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        <form wire:submit.prevent="save">
            <div class="p-4 bg-white rounded-lg shadow-md">
                {{-- Bagian Atas: Tanggal & Kode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="sale_date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal
                            Penjualan</label>
                        <input type="date" id="sale_date" wire:model="sale_date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @error('sale_date')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="sale_code" class="block mb-2 text-sm font-medium text-gray-900">Kode
                            Penjualan</label>
                        <input type="text" id="sale_code" wire:model="sale_code" readonly
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                    </div>
                </div>

                {{-- Bagian Item Penjualan --}}
                <h3 class="text-lg font-semibold mb-2 text-gray-800">Detail Item</h3>
                @error('saleItems')
                    <p class="text-sm text-red-600 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>{{ $message }}
                    </p>
                @enderror

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3 w-32">Qty</th>
                                <th class="px-4 py-3">Harga</th>
                                <th class="px-4 py-3">Subtotal</th>
                                <th class="px-4 py-3 w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($saleItems as $index => $item)
                                <tr class="border-b" wire:key="sale-item-{{ $index }}">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            @if (!empty($item['item_id']) && isset($allItems->firstWhere('id', $item['item_id'])->name))
                                                {{ $allItems->firstWhere('id', $item['item_id'])->name }}
                                            @else
                                                <span class="text-gray-400">Pilih item...</span>
                                            @endif
                                            <button type="button" wire:click="openItemModal({{ $index }})"
                                                class="ml-2 text-blue-600 hover:text-blue-800 text-sm">
                                                Ubah
                                            </button>
                                        </div>
                                        @error('saleItems.' . $index . '.item_id')
                                            <span class="text-xs text-red-500">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            <button type="button" wire:click="decrementQty({{ $index }})"
                                                class="px-2 py-1 text-sm font-medium text-gray-600 bg-gray-200 rounded-l-md hover:bg-gray-300 focus:outline-none">-</button>
                                            <input type="number"
                                                wire:model.live="saleItems.{{ $index }}.quantity"
                                                class="text-center w-10 ml-3" min="1">
                                            <button type="button" wire:click="incrementQty({{ $index }})"
                                                class="px-2 py-1 text-sm font-medium text-gray-600 bg-gray-200 rounded-r-md hover:bg-gray-300 focus:outline-none">+</button>
                                        </div>
                                        @error('saleItems.' . $index . '.quantity')
                                            <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-2">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">Rp
                                        {{ number_format($item['total_price'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" wire:click="removeItemRow({{ $index }})"
                                            class="px-3 py-1 text-white bg-red-600 rounded-md hover:bg-red-700">&times;</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada item. Klik "Tambah Item" untuk menambahkan item.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="button" wire:click="$dispatch('toggle-item-modal')"
                    class="mt-4 px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800">
                    + Tambah Item
                </button>

                {{-- Bagian Bawah: Grand Total & Tombol Simpan --}}
                <div class="mt-6 flex justify-end items-center">

                    <span class="text-xl font-bold text-gray-800">Grand Total: Rp
                        {{ number_format($grand_total, 0, ',', '.') }}</span>
                </div>

                <div class="mt-6 flex justify-start border-t pt-4">
                    <a href="{{ route('sales.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 mr-2">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Simpan Penjualan</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Item Selection Modal -->
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="showModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        <!-- Modal Header -->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Pilih
                                        Item</h3>

                                    <div class="mt-4">
                                        <input type="text" wire:model.live.debounce.300ms="searchItem"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari item berdasarkan nama atau kode...">
                                    </div>

                                    <div class="mt-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50 sticky top-0">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Item</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Harga</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($this->filteredItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $item->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $item->code }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button type="button"
                                                                wire:click="selectItem({{ $item->id }})"
                                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                Pilih
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3"
                                                            class="px-6 py-4 text-sm text-center text-gray-500">
                                                            Tidak ada item yang ditemukan.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" @click="showModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
