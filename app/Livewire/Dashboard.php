<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            $this->endDate = $this->startDate;
        }
        $this->dispatchChartsData();
    }

    public function updatedEndDate()
    {
        if ($this->startDate && $this->endDate && $this->endDate < $this->startDate) {
            $this->startDate = $this->endDate;
        }
        $this->dispatchChartsData();
    }

    public function setDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->dispatchChartsData();
    }

    private function dispatchChartsData()
    {
        $this->dispatch('chartsDataUpdated', [
            'monthlySales' => $this->monthlySalesData(),
            'itemSales' => $this->itemSalesData(),
            'widgets' => $this->widgetsData(),
        ]);
    }

    #[Computed(persist: false)]
    public function widgetsData()
    {
        $query = Sale::whereDate('sale_date', '>=', $this->startDate)
            ->whereDate('sale_date', '<=', $this->endDate);

        $totalSales = (float) (clone $query)->sum('total_price');
        $totalReceived = (float) (clone $query)->sum('total_received');
        $totalRemaining = $totalSales - $totalReceived;

        return [
            'total_transactions' => (int) (clone $query)->count(),
            'total_sales' => $totalSales,
            'total_received' => $totalReceived,
            'total_remaining' => $totalRemaining,
            'total_qty' => (int) SaleItem::whereHas('sale', function ($q) {
                $q->whereDate('sale_date', '>=', $this->startDate)
                    ->whereDate('sale_date', '<=', $this->endDate);
            })->sum('quantity'),
        ];
    }

    #[Computed(persist: false)]
    public function monthlySalesData()
    {
        $sales = Sale::selectRaw('
                DATE_FORMAT(sale_date, "%Y-%m") as period, 
                SUM(total_price) as total_sales,
                SUM(total_received) as total_received
            ')
            ->whereDate('sale_date', '>=', $this->startDate)
            ->whereDate('sale_date', '<=', $this->endDate)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        if ($sales->isEmpty()) {
            return [
                'labels' => [],
                'sales' => [],
                'received' => [],
                'remaining' => [],
                'period_type' => 'monthly',
                'count' => 0,
            ];
        }

        $labels = $sales->pluck('period')->map(function ($period) {
            return \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M Y');
        })->toArray();

        return [
            'labels' => $labels,
            'sales' => $sales->pluck('total_sales')->toArray(),
            'received' => $sales->pluck('total_received')->toArray(),
            'remaining' => $sales->map(function ($sale) {
                return $sale->total_sales - $sale->total_received;
            })->toArray(),
            'period_type' => 'monthly',
            'count' => $sales->count(),
        ];
    }

    #[Computed(persist: false)]
    public function itemSalesData()
    {
        $itemSales = SaleItem::select('items.name', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.sale_date', '>=', $this->startDate)
            ->whereDate('sales.sale_date', '<=', $this->endDate)
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return [
            'labels' => $itemSales->pluck('name')->toArray(),
            'data' => $itemSales->pluck('total_qty')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'widgets' => $this->widgetsData(),
            'monthlySales' => $this->monthlySalesData(),
            'itemSales' => $this->itemSalesData(),
        ]);
    }
}
