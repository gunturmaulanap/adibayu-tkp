<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate;
    public $endDate;

    // public function boot()
    // {
    //     $this->queryExecuted = false;
    // }

    public function render()
    {
        $query = Payment::with(['sale' => function ($q) {
            $q->select('id', 'sale_code');
        }]);

        // Filter by date range
        if ($this->startDate) {
            $query->whereDate('payment_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('payment_date', '<=', $this->endDate);
        }

        // Search by payment code or sale code
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('payment_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('sale', function ($saleQuery) {
                        $saleQuery->where('sale_code', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $payments = $query->latest('id')->paginate(5);

        return view('livewire.payments.payment-index', compact('payments'));
    }

    public function clearFilters()
    {
        $this->reset(['search', 'startDate', 'endDate']);
        $this->resetPage();
    }
}
