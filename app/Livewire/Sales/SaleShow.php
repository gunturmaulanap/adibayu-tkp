<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;

class SaleShow extends Component
{
    public Sale $sale;

    public function mount(Sale $sale)
    {
        $this->sale = $sale->load(['user', 'saleItems.item', 'payments']);
    }

    public function render()
    {
        return view('livewire.sales.sale-show');
    }
}