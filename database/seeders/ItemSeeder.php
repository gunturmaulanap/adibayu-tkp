<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['code' => 'KBD-001', 'name' => 'Mechanical Keyboard', 'image' => 'https://source.unsplash.com/400x300/?mechanical,keyboard', 'price' => 750000, 'stock' => 25],
            ['code' => 'MSE-002', 'name' => 'Wireless Mouse', 'image' => 'https://source.unsplash.com/400x300/?wireless,mouse', 'price' => 150000, 'stock' => 60],
            ['code' => 'SPK-003', 'name' => 'Bluetooth Speaker', 'image' => 'https://source.unsplash.com/400x300/?bluetooth,speaker', 'price' => 300000, 'stock' => 40],
            ['code' => 'DCH-004', 'name' => 'Car Dashcam', 'image' => 'https://source.unsplash.com/400x300/?dashcam', 'price' => 850000, 'stock' => 18],
            ['code' => 'MON-005', 'name' => '24" Monitor', 'image' => 'https://source.unsplash.com/400x300/?computer,monitor', 'price' => 1800000, 'stock' => 12],
            ['code' => 'WCB-006', 'name' => 'HD Webcam', 'image' => 'https://source.unsplash.com/400x300/?webcam', 'price' => 250000, 'stock' => 30],
            ['code' => 'MIC-007', 'name' => 'USB Microphone', 'image' => 'https://source.unsplash.com/400x300/?microphone', 'price' => 350000, 'stock' => 22],
            ['code' => 'HSD-008', 'name' => 'Gaming Headset', 'image' => 'https://source.unsplash.com/400x300/?gaming,headset', 'price' => 450000, 'stock' => 28],
            ['code' => 'LST-009', 'name' => 'Laptop Stand', 'image' => 'https://source.unsplash.com/400x300/?laptop,stand', 'price' => 120000, 'stock' => 50],
            ['code' => 'HDD-010', 'name' => 'External HDD 1TB', 'image' => 'https://source.unsplash.com/400x300/?external,hard,drive', 'price' => 600000, 'stock' => 33],
            ['code' => 'SSD-011', 'name' => 'SSD 512GB', 'image' => 'https://source.unsplash.com/400x300/?ssd,drive', 'price' => 900000, 'stock' => 20],
            ['code' => 'PSU-012', 'name' => 'Power Supply 650W', 'image' => 'https://source.unsplash.com/400x300/?computer,power,supply', 'price' => 800000, 'stock' => 15],
            ['code' => 'MB-013', 'name' => 'Motherboard ATX', 'image' => 'https://source.unsplash.com/400x300/?motherboard', 'price' => 1400000, 'stock' => 10],
            ['code' => 'CPU-014', 'name' => 'Processor (CPU)', 'image' => 'https://source.unsplash.com/400x300/?cpu,processor', 'price' => 3200000, 'stock' => 8],
            ['code' => 'CL-015', 'name' => 'CPU Cooler', 'image' => 'https://source.unsplash.com/400x300/?cpu,cooler', 'price' => 250000, 'stock' => 26],
            ['code' => 'RAM-016', 'name' => 'RAM 16GB (2x8GB)', 'image' => 'https://source.unsplash.com/400x300/?computer,ram', 'price' => 700000, 'stock' => 35],
            ['code' => 'GPU-017', 'name' => 'Graphics Card', 'image' => 'https://source.unsplash.com/400x300/?graphics,card', 'price' => 4500000, 'stock' => 5],
            ['code' => 'RTR-018', 'name' => 'WiFi Router', 'image' => 'https://source.unsplash.com/400x300/?wifi,router', 'price' => 600000, 'stock' => 27],
            ['code' => 'SWT-019', 'name' => 'Network Switch 8-port', 'image' => 'https://source.unsplash.com/400x300/?network,switch', 'price' => 950000, 'stock' => 14],
            ['code' => 'UPS-020', 'name' => 'UPS 600VA', 'image' => 'https://source.unsplash.com/400x300/?ups,power', 'price' => 1100000, 'stock' => 9],
        ];

        foreach ($items as $data) {
            Item::updateOrCreate([
                'code' => $data['code'],
            ], $data);
        }
    }
}
