<div>
    <h2 class="text-xl font-semibold mb-4">Detail Pembayaran</h2>
    <div class="p-4 bg-white rounded shadow">
        <p><strong>Kode:</strong> {{ $payment->payment_code }}</p>
        <p><strong>Sale:</strong> {{ $payment->sale->sale_code ?? '-' }}</p>
        <p><strong>Jumlah:</strong> Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
        <p><strong>Tanggal:</strong> {{ $payment->payment_date->format('d M Y') }}</p>
    </div>
    <div class="mt-4">
        <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
    </div>
</div>
