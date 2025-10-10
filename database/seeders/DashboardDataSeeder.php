<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if user ID 2 exists
        $user = User::find(2);
        if (!$user) {
            $this->command->error('User with ID 2 not found!');
            return;
        }

        // Get all items
        $items = Item::all();
        if ($items->isEmpty()) {
            $this->command->error('No items found! Please seed items first.');
            return;
        }

        $this->command->info('Creating sales data for year 2025...');

        // Generate sales for each month from January to October 2025
        $months = [
            ['month' => 1, 'sales_count' => rand(3, 6)],   // January
            ['month' => 2, 'sales_count' => rand(3, 6)],   // February
            ['month' => 3, 'sales_count' => rand(3, 6)],   // March
            ['month' => 4, 'sales_count' => rand(3, 6)],   // April
            ['month' => 5, 'sales_count' => rand(3, 6)],   // May
            ['month' => 6, 'sales_count' => rand(3, 6)],   // June
            ['month' => 7, 'sales_count' => rand(3, 6)],   // July
            ['month' => 8, 'sales_count' => rand(3, 6)],   // August
            ['month' => 9, 'sales_count' => rand(3, 6)],   // September
            ['month' => 10, 'sales_count' => rand(3, 6)],  // October (current)
        ];

        $totalSales = 0;

        foreach ($months as $monthData) {
            $month = $monthData['month'];
            $salesCount = $monthData['sales_count'];

            for ($i = 0; $i < $salesCount; $i++) {
                // Random day in the month
                $day = rand(1, Carbon::create(2025, $month)->daysInMonth);
                $saleDate = Carbon::create(2025, $month, $day);

                // Generate sale code
                $saleCode = 'INV-' . $saleDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

                // Calculate total price (random between 1-5 items)
                $itemCount = rand(1, 5);
                $totalPrice = 0;
                $saleItemsData = [];

                for ($j = 0; $j < $itemCount; $j++) {
                    $item = $items->random();
                    $quantity = rand(1, 3);
                    $price = $item->price;
                    $itemTotal = $price * $quantity;
                    $totalPrice += $itemTotal;

                    $saleItemsData[] = [
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_price' => $itemTotal,
                    ];
                }

                // Random payment status (70% full paid, 20% partial, 10% unpaid)
                $rand = rand(1, 100);
                if ($rand <= 70) {
                    // Full payment
                    $totalReceived = $totalPrice;
                    $status = Sale::STATUS_PAID;
                } elseif ($rand <= 90) {
                    // Partial payment (50-90% paid)
                    $totalReceived = (int) ($totalPrice * (rand(50, 90) / 100));
                    $status = Sale::STATUS_PARTIAL;
                } else {
                    // Unpaid
                    $totalReceived = 0;
                    $status = Sale::STATUS_UNPAID;
                }

                // Create sale
                $sale = Sale::create([
                    'sale_code' => $saleCode,
                    'user_id' => 2,
                    'total_price' => $totalPrice,
                    'total_received' => $totalReceived,
                    'status' => $status,
                    'sale_date' => $saleDate,
                ]);

                // Create sale items
                foreach ($saleItemsData as $itemData) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'total_price' => $itemData['total_price'],
                    ]);
                }

                // Create payment(s)
                if ($totalReceived > 0) {
                    if ($status == Sale::STATUS_PAID) {
                        // Single full payment
                        $paymentCode = 'PAY-' . $saleDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-1';
                        Payment::create([
                            'payment_code' => $paymentCode,
                            'sale_id' => $sale->id,
                            'user_id' => 2,
                            'amount' => $totalReceived,
                            'payment_date' => $saleDate->copy()->addHours(rand(1, 5)),
                        ]);
                    } else {
                        // Partial payment(s)
                        $remainingAmount = $totalReceived;
                        $paymentCount = rand(1, 2);

                        for ($k = 0; $k < $paymentCount; $k++) {
                            if ($k == $paymentCount - 1) {
                                // Last payment gets remaining amount
                                $paymentAmount = $remainingAmount;
                            } else {
                                // Random split
                                $paymentAmount = (int) ($remainingAmount * rand(30, 70) / 100);
                                $remainingAmount -= $paymentAmount;
                            }

                            if ($paymentAmount > 0) {
                                $paymentCode = 'PAY-' . $saleDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . ($k + 1);
                                Payment::create([
                                    'payment_code' => $paymentCode,
                                    'sale_id' => $sale->id,
                                    'user_id' => 2,
                                    'amount' => $paymentAmount,
                                    'payment_date' => $saleDate->copy()->addDays($k * rand(1, 3)),
                                ]);
                            }
                        }
                    }
                }

                $totalSales++;
            }

            $this->command->info("Created {$salesCount} sales for " . Carbon::create(2025, $month)->format('F Y'));
        }

        $this->command->info("✅ Successfully created {$totalSales} sales with items and payments!");
    }
}
