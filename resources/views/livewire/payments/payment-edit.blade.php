<div>
    <h2 class="text-xl font-semibold mb-4">Edit Pembayaran</h2>
    <form wire:submit.prevent="save">
        <div class="p-4 bg-white rounded shadow">
            <div class="mb-4">
                <label class="block mb-2">Tanggal</label>
                <input type="date" wire:model="payment_date" class="p-2 border rounded w-full" />
            </div>
            <div class="mb-4">
                <label class="block mb-2">Jumlah</label>
                <input type="number" wire:model="amount" class="p-2 border rounded w-full" />
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
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded">Simpan</button>
            </div>
        </div>
    </form>
</div>
