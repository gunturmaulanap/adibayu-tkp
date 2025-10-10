<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use Livewire\WithPagination;

class SaleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate;
    public $endDate;

    protected $queryString = ['search', 'startDate', 'endDate'];




    public function clearFilters()
    {
        $this->search = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function delete($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            session()->flash('error', 'Penjualan tidak ditemukan.');
            return;
        }

        if ($sale->status == Sale::STATUS_PAID) {
            session()->flash('error', 'Penjualan yang sudah dibayar tidak dapat dihapus.');
            return;
        }

        $sale->delete();

        session()->flash('success', 'Sale deleted successfully.');
        return to_route('sales.index');
    }


    protected function hasDuplicateItems(Sale $sale)
    {
        $itemIds = [];
        foreach ($sale->saleItems as $item) {
            if (in_array($item->item_id, $itemIds)) {
                return true;
            }
            $itemIds[] = $item->item_id;
        }
        return false;
    }

    public function render()
    {
        $query = Sale::with(['user', 'saleItems.item']);

        if ($this->startDate) {
            $query->where('sale_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('sale_date', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('sale_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $sales = $query->latest()->paginate(5);

        return view('livewire.sales.sale-index', ['sales' => $sales]);
    }
}
