<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentEdit extends Component
{
    public $paymentId;
    public $amount;
    public $payment_date;

    public function mount($id)
    {
        $this->paymentId = $id;
        $payment = Payment::findOrFail($id);
        $this->amount = $payment->amount;
        $this->payment_date = $payment->payment_date->format('Y-m-d');
    }


    public function save()
    {

        if (empty($this->amount) || $this->amount <= 0) {
            session()->flash('error', 'Silakan masukkan jumlah pembayaran yang valid.');
            $this->addError('amount', 'Jumlah pembayaran harus diisi dan lebih besar dari 0.');
            return;
        }


        $payment = Payment::find($this->paymentId);
        $payment->amount = $this->amount;
        $payment->payment_date = $this->payment_date;
        $payment->user_id = Auth::id();
        $payment->save();

        // update sale status
        $payment->sale->updateStatusBasedOnPayments();

        session()->flash('success', 'Pembayaran berhasil diperbarui.');
        return redirect()->route('payments.index');
    }

    public function render()
    {
        return view('livewire.payments.payment-edit');
    }
}
