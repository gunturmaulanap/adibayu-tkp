<?php

namespace App\Livewire\Sales;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SaleCreate extends Component
{
    public $sale_date;
    public $sale_code;
    public $saleItems = [];
    public $allItems;
    public $grand_total = 0;
    public $searchItem = '';
    public $currentItemIndex = null;
    public $showItemModal = false;

    protected $rules = [
        'sale_date' => 'required|date',
        'saleItems' => 'required|array|min:1',
        'saleItems.*.item_id' => 'required|exists:items,id',
        'saleItems.*.quantity' => 'required|numeric|min:1',
    ];

    protected $messages = [
        'saleItems.required' => 'Minimal harus ada satu item dalam penjualan.',
        'saleItems.min' => 'Minimal harus ada satu item dalam penjualan.',
        'saleItems.*.item_id.required' => 'Item harus dipilih.',
        'saleItems.*.item_id.exists' => 'Item yang dipilih tidak valid.',
        'saleItems.*.quantity.required' => 'Qty tidak boleh kosong.',
        'saleItems.*.quantity.numeric' => 'Qty harus berupa angka.',
        'saleItems.*.quantity.min' => 'Qty minimal 1.',
        'duplicate_items' => 'Item :item telah ditambahkan sebelumnya. Silakan perbarui jumlah pada item yang sudah ada.',
    ];



    protected $cachedSaleCode = null;

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d');

        $this->allItems = collect();
        $this->saleItems = [];
    }

    protected $filteredItemsCache;
    protected $lastSearchTerm = '';
    protected $itemsLoaded = false;

    protected function loadItemsIfNeeded()
    {
        if (!$this->itemsLoaded) {
            $this->allItems = Item::select('id', 'name', 'code', 'price')
                ->orderBy('name')
                ->limit(100)
                ->get();
            $this->itemsLoaded = true;
        }
    }

    public function getFilteredItemsProperty()
    {
        $this->loadItemsIfNeeded();

        if ($this->lastSearchTerm !== $this->searchItem || $this->filteredItemsCache === null) {
            $this->lastSearchTerm = $this->searchItem;

            if (empty($this->searchItem)) {
                $this->filteredItemsCache = $this->allItems->take(100);
            } else {
                if (strlen($this->searchItem) >= 2) {
                    $searchTerm = $this->searchItem;
                    $this->filteredItemsCache = Item::select('id', 'name', 'code', 'price')
                        ->where(function ($query) use ($searchTerm) {
                            $query->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('code', 'like', "%{$searchTerm}%");
                        })
                        ->limit(50)
                        ->get();
                } else {
                    $searchTerm = strtolower($this->searchItem);
                    $this->filteredItemsCache = $this->allItems->filter(function ($item) use ($searchTerm) {
                        return str_contains(strtolower($item->name), $searchTerm) ||
                            str_contains(strtolower($item->code), $searchTerm);
                    })->take(50);
                }
            }
        }

        return $this->filteredItemsCache;
    }

    public function openItemModal($index = null)
    {
        $this->currentItemIndex = $index;
        $this->searchItem = '';
        $this->dispatch('toggle-item-modal');
    }

    public function closeItemModal()
    {
        $this->searchItem = '';
        $this->currentItemIndex = null;
        $this->dispatch('toggle-item-modal');
    }

    public function toggleModal()
    {
        $this->dispatch('toggle-item-modal');
    }

    protected function findDuplicateItem($itemId, $excludeIndex = null)
    {
        foreach ($this->saleItems as $index => $saleItem) {
            if ($excludeIndex !== null && $index === $excludeIndex) {
                continue;
            }

            if (isset($saleItem['item_id']) && $saleItem['item_id'] == $itemId) {
                return $index;
            }
        }

        return false;
    }

    public function selectItem($itemId)
    {
        $item = $this->allItems->firstWhere('id', $itemId);

        if (!$item) {
            $item = Item::select('id', 'name', 'code', 'price')
                ->find($itemId);

            if ($item) {
                $this->allItems->push($item);
            }
        }

        if (!$item) {
            return;
        }

        $duplicateIndex = $this->findDuplicateItem($itemId, $this->currentItemIndex);

        if ($duplicateIndex !== false) {
            session()->flash('error', "Item '{$item->name}' telah ditambahkan sebelumnya. Silakan perbarui jumlah pada item yang sudah ada.");
            $this->closeItemModal();
            return;
        }

        if ($this->currentItemIndex !== null) {
            $this->saleItems[$this->currentItemIndex]['item_id'] = $itemId;
            $this->saleItems[$this->currentItemIndex]['price'] = $item->price;
            $this->saleItems[$this->currentItemIndex]['total_price'] =
                $item->price * ($this->saleItems[$this->currentItemIndex]['quantity'] ?? 1);
        } else {
            $this->saleItems[] = [
                'item_id' => $itemId,
                'quantity' => 1,
                'price' => $item->price,
                'total_price' => $item->price,
            ];
        }

        $this->updateGrandTotal();
        $this->closeItemModal();
    }


    public function removeItemRow($index)
    {
        unset($this->saleItems[$index]);
        $this->saleItems = array_values($this->saleItems);
        $this->updateGrandTotal();
    }

    public function incrementQty($index)
    {
        $this->saleItems[$index]['quantity']++;
        $this->updateRow($index);
    }

    public function decrementQty($index)
    {
        if ($this->saleItems[$index]['quantity'] > 1) {
            $this->saleItems[$index]['quantity']--;
            $this->updateRow($index);
        }
    }


    public function updateRow($index)
    {
        $itemId = $this->saleItems[$index]['item_id'] ?? null;
        $quantity = $this->saleItems[$index]['quantity'] ?? 0;

        if ($itemId && is_numeric($quantity) && $quantity > 0) {
            $item = $this->allItems->firstWhere('id', $itemId);

            if (!$item) {
                $item = Item::select('id', 'name', 'code', 'price')
                    ->find($itemId);

                if ($item) {
                    $this->allItems->push($item);
                }
            }

            if ($item) {
                $this->saleItems[$index]['price'] = $item->price;
                $this->saleItems[$index]['total_price'] = $item->price * $quantity;
            }
        } else {
            $this->saleItems[$index]['price'] = 0;
            $this->saleItems[$index]['total_price'] = 0;
        }
        $this->updateGrandTotal();
    }

    public function updateGrandTotal()
    {
        $this->grand_total = collect($this->saleItems)->sum('total_price');
    }

    public function save()
    {
        Log::info('SaleCreate: save called', [
            'item_count' => count($this->saleItems),
            'grand_total' => $this->grand_total,
        ]);

        $this->validateNoDuplicates();

        try {
            $this->validate();
        } catch (ValidationException $e) {
            Log::warning('SaleCreate: validation failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        try {
            $createdSale = DB::transaction(function () {
                $sale = Sale::create([
                    'sale_code' => $this->sale_code ?? $this->generateSaleCode(),
                    'sale_date' => $this->sale_date,
                    'total_price' => $this->grand_total,
                    'total_received' => 0,
                    'status' => 0,
                    'user_id' => Auth::id(),
                ]);

                foreach ($this->saleItems as $itemData) {
                    $sale->saleItems()->create([
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'total_price' => $itemData['total_price'],
                    ]);
                }

                return $sale;
            });

            Log::info('SaleCreate: sale created', [
                'sale_id' => $createdSale->id ?? null,
                'sale_code' => $createdSale->sale_code ?? null,
                'total' => $this->grand_total,
            ]);

            session()->flash('success', 'Penjualan berhasil dibuat.');
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            Log::error('SaleCreate: exception when saving sale', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => ['sale_date' => $this->sale_date, 'saleItems' => $this->saleItems],
            ]);

            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function validateNoDuplicates()
    {
        $itemIds = [];
        $errors = [];

        foreach ($this->saleItems as $index => $item) {
            $itemId = $item['item_id'] ?? null;
            if ($itemId) {
                if (in_array($itemId, $itemIds)) {
                    // Find item name
                    $itemModel = $this->allItems->firstWhere('id', $itemId);
                    $itemName = $itemModel ? $itemModel->name : "Item #{$itemId}";

                    $errors["saleItems.{$index}.item_id"] = str_replace(':item', $itemName, $this->messages['duplicate_items']);
                } else {
                    $itemIds[] = $itemId;
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function generateSaleCode()
    {
        if ($this->cachedSaleCode) {
            return $this->cachedSaleCode;
        }

        $prefix = 'INV/' . date('Y') . '/' . date('m') . '/';

        $lastSale = Sale::select('id', 'sale_code')
            ->where('sale_code', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = $lastSale ? (int)substr($lastSale->sale_code, -4) + 1 : 1;
        $this->cachedSaleCode = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);

        return $this->cachedSaleCode;
    }

    public $pageLoaded = false;

    public function render()
    {
        if (empty($this->sale_code)) {
            $this->sale_code = $this->generateSaleCode();
        }

        if (!$this->pageLoaded) {
            $this->pageLoaded = true;
        }

        return view('livewire.sales.sale-create');
    }
}
