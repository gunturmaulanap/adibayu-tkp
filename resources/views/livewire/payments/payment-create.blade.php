<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Tambah Pembayaran') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Isi detail pembayaran di bawah ini') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Session Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="flex-shrink-0 w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <svg class="flex-shrink-0 w-5 h-5 text-red-700" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm font-medium text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <!-- Payment Information Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Kode Pembayaran</label>
                    <input type="text" wire:model="payment_code" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
                    @error('payment_code')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pembayaran</label>
                    <input type="date" wire:model="payment_date"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500" />
                    @error('payment_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Select Sale Section -->
            <div class="mb-8">
                <label class="block mb-2 text-sm font-medium text-gray-900">
                    Pilih Penjualan <span class="text-red-600">*</span>
                </label>
                <select wire:model.live="sale_id"
                    class="w-full bg-gray-50 border {{ $errors->has('sale_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Penjualan</option>
                    @foreach ($this->sales as $sale)
                        <option value="{{ $sale->id }}">
                            [{{ $sale->sale_code }}] {{ $sale->sale_date->format('d M Y') }} -
                            Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                            @if ($sale->status == App\Models\Sale::STATUS_PAID)
                                ({{ $sale->status_label }})
                            @elseif($sale->status == App\Models\Sale::STATUS_PARTIAL)
                                ({{ $sale->status_label }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('sale_id')
                    <p class="text-sm text-red-600 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror

                <!-- Info about available sales -->
                @if ($this->sales->isEmpty())
                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <svg class="flex-shrink-0 w-5 h-5 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Tidak ada penjualan yang tersedia untuk pembayaran. Pastikan ada penjualan dengan
                                    status "Belum Bayar" atau "Belum Dibayar Sepenuhnya".
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Summary Section (shown only when a sale is selected) -->
            @if ($sale_id)
                <div class="mb-8 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <h3 class="font-semibold text-lg text-blue-800 mb-3">Ringkasan Pembayaran</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white p-3 rounded border">
                            <p class="text-xs text-gray-500 uppercase">Total Tagihan</p>
                            <p class="text-lg font-bold text-gray-900">Rp
                                {{ number_format($saleTotalPrice, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <p class="text-xs text-gray-500 uppercase">Sudah Dibayar</p>
                            <p class="text-lg font-bold text-gray-900">Rp
                                {{ number_format($selectedSaleTotalPaid, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <p class="text-xs text-gray-500 uppercase">Sisa Tagihan</p>
                            <p class="text-lg font-bold text-{{ $remaining > 0 ? 'red' : 'green' }}-600">Rp
                                {{ number_format($remaining, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Payment Progress Bar -->
                    @if ($saleTotalPrice > 0)
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress Pembayaran</span>
                                <span>{{ round(($selectedSaleTotalPaid / $saleTotalPrice) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ min(100, ($selectedSaleTotalPaid / $saleTotalPrice) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment History -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Riwayat Pembayaran</h4>
                        @if (count($selectedSalePayments) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-700">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2">Tanggal</th>
                                            <th class="px-3 py-2">Kode Pembayaran</th>
                                            <th class="px-3 py-2">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedSalePayments as $payment)
                                            <tr class="border-b bg-white hover:bg-gray-50">
                                                <td class="px-3 py-2">
                                                    {{ \Carbon\Carbon::parse($payment['payment_date'])->format('d M Y') }}
                                                </td>
                                                <td class="px-3 py-2 font-medium">{{ $payment['payment_code'] }}</td>
                                                <td class="px-3 py-2">Rp
                                                    {{ number_format($payment['amount'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">Belum ada pembayaran untuk penjualan ini.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Amount Input Section -->
            <div class="mb-8">
                <label class="block mb-2 text-sm font-medium text-gray-900">
                    Jumlah Pembayaran (Rp) <span class="text-red-600">*</span>
                </label>
                <input type="text" x-data x-mask:dynamic="$money($input, ',', '.')" wire:model.blur="amount"
                    placeholder="Masukkan jumlah pembayaran"
                    class="w-full bg-gray-50 border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 {{ $remaining <= 0 ? 'bg-gray-100' : '' }}"
                    {{ $remaining <= 0 || empty($sale_id) ? 'disabled' : '' }} />

                @if (empty($sale_id))
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Pilih penjualan terlebih dahulu
                    </p>
                @endif

                @error('amount')
                    <p class="text-sm text-red-600 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="text-sm text-gray-500">
                        Sisa tagihan: <span class="font-semibold">Rp
                            {{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" wire:click="$set('amount', ceil($remaining * 0.25 / 1000) * 1000)"
                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                            25%
                        </button>
                        <button type="button" wire:click="$set('amount', ceil($remaining * 0.5 / 1000) * 1000)"
                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                            50%
                        </button>
                        <button type="button" wire:click="$set('amount', ceil($remaining * 0.75 / 1000) * 1000)"
                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                            75%
                        </button>
                        <button type="button" wire:click="$set('amount', $remaining)"
                            class="text-xs px-2 py-1 bg-blue-100 hover:bg-blue-200 rounded text-blue-700">
                            Full
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('payments.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white rounded-lg transition flex items-center
                    {{ $remaining <= 0 || empty($sale_id) ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-700 hover:bg-blue-800' }}"
                    {{ $remaining <= 0 || empty($sale_id) ? 'disabled' : '' }}>
                    @if (empty($sale_id))
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Pilih Penjualan Dahulu
                    @elseif ($remaining <= 0)
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tidak Ada Tagihan
                    @else
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Simpan Pembayaran
                    @endif
                </button>
            </div>
        </div>
    </form>
</div>
