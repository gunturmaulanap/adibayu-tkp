<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleEdit extends Component
{
    public Sale $sale;
    public $sale_code;
    public $sale_date;
    public $items = [];
    public $allItems;
    public $totalPrice = 0;

    protected $rules = [
        'sale_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.qty' => 'required|integer|min:1',
    ];

    protected $messages = [
        'sale_date.required' => 'Tanggal penjualan harus diisi.',
        'sale_date.date' => 'Format tanggal tidak valid.',
        'items.required' => 'Minimal harus ada satu item dalam penjualan.',
        'items.*.item_id.required' => 'Silakan pilih item.',
        'items.*.item_id.exists' => 'Item yang dipilih tidak valid.',
        'items.*.price.required' => 'Harga satuan harus diisi.',
        'items.*.price.numeric' => 'Harga satuan harus berupa angka.',
        'items.*.price.min' => 'Harga satuan tidak boleh negatif.',
        'items.*.qty.required' => 'Kuantitas harus diisi.',
        'items.*.qty.integer' => 'Kuantitas harus berupa angka bulat.',
        'items.*.qty.min' => 'Kuantitas minimal 1.',
        'duplicate_items' => 'Item :item telah ditambahkan sebelumnya. Silakan perbarui jumlah pada item yang sudah ada.',
    ];

    public function mount($id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status == Sale::STATUS_PAID) {
            session()->flash('error', 'Penjualan yang sudah dibayar tidak dapat diedit.');
            return redirect()->route('sales.index');
        }

        $this->sale = $sale;
        $this->sale_code = $sale->sale_code;
        $this->sale_date = optional($sale->sale_date)->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->allItems = Item::all();

        $this->items = [];

        foreach ($sale->saleItems as $saleItem) {
            $this->items[] = [
                'id' => $saleItem->id,
                'item_id' => $saleItem->item_id,
                'price' => (float) $saleItem->price,
                'qty' => (int) $saleItem->quantity,
                'total_price' => (float) $saleItem->total_price,
            ];
        }

        $this->calculateTotal();
    }

    public $searchItem = '';
    public $currentItemIndex = null;
    public $isNewItem = false;

    protected function findDuplicateItem($itemId, $excludeIndex = null)
    {
        foreach ($this->items as $index => $item) {
            if ($excludeIndex !== null && $index === $excludeIndex) {
                continue;
            }

            if (isset($item['item_id']) && $item['item_id'] == $itemId) {
                return $index;
            }
        }

        return false;
    }

    public function addItemRow()
    {
        $this->isNewItem = true;
        $this->currentItemIndex = null;
        $this->searchItem = '';
        $this->dispatch('toggle-item-modal');
    }

    public function removeItemRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function openItemModal($index)
    {
        $this->isNewItem = false;
        $this->currentItemIndex = $index;
        $this->searchItem = '';
        $this->dispatch('toggle-item-modal');
    }

    public function selectItem($itemId)
    {
        $item = Item::find($itemId);
        if (!$item) return;

        $duplicateIndex = $this->findDuplicateItem($itemId, $this->currentItemIndex);

        if ($duplicateIndex !== false) {
            session()->flash('error', "Item '{$item->name}' telah ditambahkan sebelumnya. Silakan perbarui jumlah pada item yang sudah ada.");
            $this->dispatch('toggle-item-modal');
            return;
        }

        if ($this->isNewItem) {
            $this->items[] = [
                'id' => null,
                'item_id' => $item->id,
                'price' => $item->price,
                'qty' => 1,
                'total_price' => $item->price,
            ];
            $this->isNewItem = false;
        } elseif ($this->currentItemIndex !== null) {
            $this->items[$this->currentItemIndex]['item_id'] = $item->id;
            $this->items[$this->currentItemIndex]['price'] = $item->price;
            $this->items[$this->currentItemIndex]['total_price'] =
                $item->price * ($this->items[$this->currentItemIndex]['qty'] ?? 1);
        }

        $this->calculateTotal();
        $this->dispatch('toggle-item-modal');
    }

    public function incrementQty($index)
    {
        if (isset($this->items[$index])) {
            if (!isset($this->items[$index]['qty']) || !is_numeric($this->items[$index]['qty'])) {
                $this->items[$index]['qty'] = 1;
            }
            $this->items[$index]['qty'] = (int)$this->items[$index]['qty'] + 1;
            $this->items[$index]['total_price'] =
                ($this->items[$index]['price'] ?? 0) * $this->items[$index]['qty'];
            $this->calculateTotal();
        }
    }

    public function decrementQty($index)
    {
        if (isset($this->items[$index])) {
            if (!isset($this->items[$index]['qty']) || !is_numeric($this->items[$index]['qty'])) {
                $this->items[$index]['qty'] = 1;
            } else if ($this->items[$index]['qty'] > 1) {
                $this->items[$index]['qty'] = (int)$this->items[$index]['qty'] - 1;
                $this->items[$index]['total_price'] =
                    ($this->items[$index]['price'] ?? 0) * $this->items[$index]['qty'];
                $this->calculateTotal();
            }
        }
    }

    public function getFilteredItemsProperty()
    {
        if (empty($this->searchItem)) {
            return $this->allItems;
        }

        return $this->allItems->filter(function ($item) {
            return str_contains(strtolower($item->name), strtolower($this->searchItem)) ||
                str_contains(strtolower($item->code), strtolower($this->searchItem));
        });
    }

    protected function calculateTotal()
    {
        $this->totalPrice = collect($this->items)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });
    }

    public function updatedItems($value, $key)
    {
        if (strpos($key, '.') !== false) {
            list($index, $field) = explode('.', $key);
        } else {
            // If called directly from template with separated values
            $index = $key;
            $field = 'qty';
        }

        if ($field === 'item_id' && !empty($value)) {
            $item = Item::find($value);
            if ($item) {
                $this->items[$index]['price'] = $item->price;
            }
        }

        if ($field === 'qty') {
            if (!is_numeric($value) || $value < 1) {
                $this->items[$index]['qty'] = 1;
            } else {
                $this->items[$index]['qty'] = (int)$value;
            }
        }

        if (isset($this->items[$index])) {
            $qty = $this->items[$index]['qty'] ?? 1;
            $price = $this->items[$index]['price'] ?? 0;
            $this->items[$index]['total_price'] = $qty * $price;
        }
    }

    protected function validateNoDuplicates()
    {
        $itemIds = [];
        $errors = [];

        foreach ($this->items as $index => $item) {
            $itemId = $item['item_id'] ?? null;
            if ($itemId) {
                if (in_array($itemId, $itemIds)) {
                    $itemModel = $this->allItems->firstWhere('id', $itemId);
                    $itemName = $itemModel ? $itemModel->name : "Item #{$itemId}";

                    $errors["items.{$index}.item_id"] = str_replace(':item', $itemName, $this->messages['duplicate_items']);
                } else {
                    $itemIds[] = $itemId;
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
    public function save()
    {
        $this->validate();

        $this->validateNoDuplicates();

        if ($this->sale->status == Sale::STATUS_PAID) {
            $this->addError('sale_date', 'Penjualan yang sudah dibayar tidak dapat diedit.');
            return;
        }

        DB::transaction(function () {
            $this->sale->update([
                'sale_date' => $this->sale_date,
                'total_price' => $this->totalPrice,
                'user_id' => Auth::id(),
            ]);

            $this->sale->saleItems()->delete();

            foreach ($this->items as $item) {
                $this->sale->saleItems()->create([
                    'item_id' => $item['item_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'total_price' => $item['total_price'],
                ]);
            }

            $this->sale->updateStatusBasedOnPayments();
        });

        session()->flash('success', 'Penjualan berhasil diperbarui.');
        return redirect()->route('sales.index');
    }

    public function render()
    {
        if (empty($this->items)) {
            $this->items = [];
        }

        foreach ($this->items as $index => $item) {
            if (!isset($item['qty']) || !is_numeric($item['qty'])) {
                $this->items[$index]['qty'] = 1;
            }
            if (!isset($item['total_price']) || !is_numeric($item['total_price'])) {
                $this->items[$index]['total_price'] = ($item['price'] ?? 0) * $this->items[$index]['qty'];
            }
        }

        $grand_total = collect($this->items)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });

        return view('livewire.sales.sale-edit', [
            'grand_total' => $grand_total,
            'items' => $this->items,
            'allItems' => $this->allItems,
        ]);
    }
}
