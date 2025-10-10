<?php

namespace App\Livewire\Items;

use App\Models\Item;

use Livewire\Component;

class ItemShow extends Component
{
    public $item;
    public function render()
    {
        return view('livewire.items.item-show');
    }

    public function mount($id)
    {
        $this->item = Item::find($id);
    }
}
