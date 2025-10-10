<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class PaymentCreate extends Component
{
    public $sale_id;
    public $payment_date;
    public $amount;
    public $payment_code;
    public Collection|null $sales = null;
    public $remaining = 0;
    public $selectedSalePayments = [];
    public $selectedSaleTotalPaid = 0;
    public $saleTotalPrice = 0;

    protected function rules()
    {
        return [
            'sale_id' => 'required|exists:sales,id',
            'payment_date' => 'required|date',
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:999999999',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    protected $messages = [
        'amount.min' => 'Jumlah pembayaran harus lebih besar dari 0.',
        'sale_id.required' => 'Silakan pilih penjualan terlebih dahulu.',
        'sale_id.exists' => 'Penjualan yang dipilih tidak valid.',
        'payment_date.required' => 'Tanggal pembayaran harus diisi.',
        'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
    ];

    /**
     * Component initialization
     */
    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
        // Only show unpaid or partially paid sales
        $this->sales = Sale::with('payments')
            ->whereIn('status', [Sale::STATUS_UNPAID, Sale::STATUS_PARTIAL])
            ->latest()
            ->get();
        $this->payment_code = $this->generatePaymentCode();

        // Set default selection to empty to ensure user must explicitly choose
        $this->sale_id = '';
        $this->resetPaymentDetails();
    }

    /**
     * Handle sale selection changes
     */
    public function updatedSaleId($value)
    {
        if (empty($value)) {
            $this->resetPaymentDetails();
            return;
        }

        $sale = $this->getSaleWithPayments($value);

        if ($sale) {
            $this->calculatePaymentDetails($sale);
        } else {
            $this->resetPaymentDetails();
        }
    }

    /**
     * Handle amount input changes
     */
    public function updatedAmount($value)
    {
        if (empty($this->sale_id)) {
            $this->amount = null;
            $this->addError('amount', 'Silakan pilih penjualan terlebih dahulu.');
            return;
        }

        $cleanValue = preg_replace('/[^0-9]/', '', $value);

        if (empty($cleanValue)) {
            $this->amount = null;
            $this->addError('amount', 'Jumlah pembayaran harus berupa angka.');
            return;
        }

        $numeric = (int) $cleanValue;

        if ($numeric <= 0) {
            $this->amount = null;
            $this->addError('amount', 'Jumlah pembayaran harus lebih besar dari 0.');
            return;
        }

        if ($numeric > $this->remaining) {
            $this->amount = $this->remaining;
            $this->dispatch('payment-capped', ['remaining' => $this->remaining]);
            return;
        }

        $this->amount = $numeric;

        if ($this->remaining > 0 && $numeric > $this->remaining) {
            $this->amount = $this->remaining;
            $this->dispatch('payment-amount-updated', [
                'message' => 'Jumlah disesuaikan dengan sisa tagihan: Rp ' . number_format($this->remaining, 0, ',', '.'),
                'amount' => $this->remaining
            ]);
        }
    }

    /**
     * Create payment record
     */
    public function save()
    {
        if (empty($this->sale_id)) {
            session()->flash('error', 'Silakan pilih penjualan terlebih dahulu.');
            $this->addError('sale_id', 'Penjualan harus dipilih.');
            return;
        }

        if (empty($this->amount) || $this->amount <= 0) {
            session()->flash('error', 'Silakan masukkan jumlah pembayaran yang valid.');
            $this->addError('amount', 'Jumlah pembayaran harus diisi dan lebih besar dari 0.');
            return;
        }

        $this->validate();



        $remaining = $this->getRemainingBalance($this->sale_id);


        DB::transaction(function () use ($remaining) {
            $payment = Payment::create([
                'payment_code' => $this->payment_code,
                'sale_id' => $this->sale_id,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
                'user_id' => Auth::id(),
            ]);

            // Update sale status based on payments
            $sale = Sale::find($this->sale_id);
            $sale->updateStatusBasedOnPayments();

            session()->flash('success', 'Pembayaran berhasil disimpan.');
            return redirect()->route('payments.index');
        });
    }

    /**
     * Generate unique payment code
     */
    public function generatePaymentCode()
    {
        $prefix = 'PAY/' . date('Y') . '/' . date('m') . '/';
        $last = Payment::where('payment_code', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = $last ? (int)substr($last->payment_code, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }


    private function calculatePaymentDetails($sale)
    {
        $paid = (int) $sale->payments()->sum('amount');
        $this->remaining = max(0, $sale->total_price - $paid);
        $this->selectedSalePayments = $sale->payments()->latest()->get()->toArray();
        $this->selectedSaleTotalPaid = $paid;
        $this->saleTotalPrice = $sale->total_price;

        if ($this->remaining <= 0) {
            $this->amount = 0;
        }
    }

    private function resetPaymentDetails()
    {
        $this->remaining = 0;
        $this->selectedSalePayments = [];
        $this->selectedSaleTotalPaid = 0;
        $this->saleTotalPrice = 0;
        $this->amount = null;
    }


    private function getSaleWithPayments($id)
    {
        return Sale::with(['payments' => function ($query) {
            $query->latest();
        }])->find($id);
    }

    private function getRemainingBalance($saleId)
    {
        $sale = Sale::find($saleId);
        if (!$sale) return 0;

        $paid = (int) $sale->payments()->sum('amount');
        return max(0, $sale->total_price - $paid);
    }

    public function render()
    {
        return view('livewire.payments.payment-create');
    }
}
