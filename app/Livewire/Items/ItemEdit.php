<?php

namespace App\Livewire\Items;

use Livewire\Component;
use App\Models\Item;
use Livewire\WithFileUploads;

class ItemEdit extends Component
{
    use WithFileUploads;

    public $item, $code, $name, $image, $price, $stock;
    public $existingImage;

    public function mount($id)
    {

        $item = Item::find($id);
        $this->item = $item;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->existingImage = $item->image;
        $this->image = null;
        $this->price = $item->price;
        $this->stock = $item->stock;
    }

    public function render()
    {
        return view('livewire.items.item-edit');
    }
    public function submit()
    {
        $this->validate([
            'code' => 'required|unique:items,code,' . $this->item->id,
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:1024',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $imagePath = null;
        if ($this->image && method_exists($this->image, 'store')) {
            $imagePath = $this->image->store('items', 'public');
        }
        $this->item->code = $this->code;
        $this->item->name = $this->name;
        if ($imagePath) {
            $this->item->image = $imagePath;
        } else {
            $this->item->image = $this->existingImage;
        }
        $this->item->price = $this->price;
        $this->item->stock = $this->stock;
        $this->item->save();

        return to_route('items.index')->with('success', 'Item updated successfully.');
    }
}
