<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class ItemIndex extends Component
{
    use WithPagination;
    public $search = '';
    public $startDate;
    public $endDate;

    public function clearFilters()
    {
        $this->search = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }


    public function render()
    {


        $query = Item::query();
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }
        $items = $query->latest()->paginate(5);


        return view('livewire.items.item-index', compact('items'));
    }
    public function delete($id)
    {
        $item = Item::find($id);
        $item->delete();

        session()->flash('success', 'Item deleted successfully.');
        return to_route('items.index');
    }
}
