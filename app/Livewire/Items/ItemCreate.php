<?php

namespace App\Livewire\Items;

use Livewire\Component;
use App\Models\Item;
use Livewire\WithFileUploads;

class ItemCreate extends Component
{
    use WithFileUploads;

    public $code, $name, $image, $price, $stock;

    public function render()
    {
        return view('livewire.items.item-create');
    }

    public function submit()
    {
        $this->validate([
            'code' => 'required|unique:items,code',
            'name' => 'required|string|unique:items,name',
            'image' => 'required|image|max:2048',
            'price' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('items', 'public');
        }
        Item::create([
            'code' => $this->code,
            'name' => $this->name,
            'image' => $imagePath,
            'price' => $this->price,
            'stock' => $this->stock ?? 0,
        ]);

        session()->flash('success', 'Item created successfully.');
        return to_route('items.index');
    }
}
