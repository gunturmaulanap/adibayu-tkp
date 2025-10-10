<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Payment;

class PaymentShow extends Component
{
    public $paymentId;

    public function mount($id)
    {
        $this->paymentId = $id;
    }

    public function render()
    {
        $payment = Payment::findOrFail($this->paymentId);
        return view('livewire.payments.payment-show', ['payment' => $payment]);
    }
}
