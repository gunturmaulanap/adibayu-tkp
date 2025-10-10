<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Penjualan') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Lihat detail penjualan dan riwayat pembayaran') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <!-- Sale Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Kode Penjualan</label>
                    <input type="text" value="{{ $sale->sale_code }}" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Penjualan</label>
                    <td class="px-4 py-3">{{ optional($sale->sale_date)->format('d M Y') ?? '-' }}</td>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Pelanggan</label>
                    <input type="text" value="{{ $sale->user->name ?? '-' }}" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <input type="text" value="{{ $sale->status_label }}" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
                </div>
            </div>

            <!-- Sale Items -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Penjualan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3">Harga Satuan</th>
                                <th class="px-4 py-3">Kuantitas</th>
                                <th class="px-4 py-3">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sale->saleItems as $item)
                                <tr class="border-b bg-white">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">
                                            {{ $item->item->name ?? 'Item tidak ditemukan' }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->item->code ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $item->quantity ?? 0 }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada item penjualan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-900">Total:</td>
                                <td class="px-4 py-3 font-bold text-gray-900">Rp
                                    {{ number_format($sale->total_price ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment History -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h3>
                @if ($sale->payments && $sale->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Kode Pembayaran</th>
                                    <th class="px-4 py-3">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->payments as $payment)
                                    <tr class="border-b bg-white">
                                        <td class="px-4 py-3">
                                            {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 font-medium">{{ $payment->payment_code ?? '-' }}</td>
                                        <td class="px-4 py-3">Rp
                                            {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-right font-semibold text-gray-900">Total
                                        Dibayar:</td>
                                    <td class="px-4 py-3 font-bold text-gray-900">Rp
                                        {{ number_format($sale->payments->sum('amount'), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-right font-semibold text-gray-900">Sisa
                                        Tagihan:</td>
                                    <td
                                        class="px-4 py-3 font-bold {{ $sale->getRemainingBalance() > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($sale->getRemainingBalance(), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <svg class="flex-shrink-0 w-5 h-5 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Belum ada pembayaran untuk penjualan ini.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('sales.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                    Kembali
                </a>

                <!-- Edit button - only shown if not fully paid -->
                @if ($sale->status != App\Models\Sale::STATUS_PAID)
                    @can('sale.edit')
                        <a href="{{ url('sales/' . $sale->id . '/edit') }}"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit Penjualan
                        </a>
                    @endcan
                @endif

                <!-- Add Payment button - only shown if not fully paid -->
                @if ($sale->status != App\Models\Sale::STATUS_PAID)
                    @can('payment.create')
                        <a href="{{ route('payments.create') }}"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Tambah Pembayaran
                        </a>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>
