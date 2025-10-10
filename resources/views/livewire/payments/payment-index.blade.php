<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Payments') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your payments') }}</flux:subheading>
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
                        placeholder="Cari berdasarkan kode pembayaran atau kode penjualan..."
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
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Sale</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                            <td class="px-6 py-2">{{ $payment->payment_code }}</td>
                            <td class="px-6 py-2">{{ $payment->sale->sale_code ?? '-' }}</td>
                            <td class="px-6 py-2">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-2">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="px-6 py-2 space-x-1">
                                <div class="flex space-x-2">

                                    @can('payment.view')
                                        <a href="{{ route('payments.show', $payment->id) }}"
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
                                    @can('payment.edit')
                                        <a href="{{ route('payments.edit', $payment->id) }}"
                                            class="text-yellow-600 hover:text-yellow-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-6 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
    </div>
    <div class="fixed bottom-8 right-8">
        @can('payment.create')
            <a href="{{ route('payments.create') }}"
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
