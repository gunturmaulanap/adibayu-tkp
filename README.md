# Laravel Sales & Payment Management System

Sistem manajemen penjualan dan pembayaran berbasis web menggunakan Laravel 12, Livewire 3, dan Chart.js untuk visualisasi data.

## 🚀 Features

### 📊 Dashboard

-   **Widget Statistik**: Jumlah transaksi, total penjualan, pembayaran diterima, dan sisa pembayaran
-   **Grafik Interaktif**:
    -   Bar Chart: Penjualan & Pembayaran per Bulan
    -   Horizontal Bar Chart: Top 10 Item Terjual
    -   Doughnut Chart: Status Pembayaran dengan persentase
-   **Filter Periode Dinamis**: Bulan Ini, Bulan Lalu, Tahun Ini, 7/30 Hari Terakhir, Custom Range
-   **Real-time Update**: Chart dan widget update otomatis saat filter berubah

### 💰 Sales Management

-   CRUD Sales dengan multiple items
-   Validasi duplicate items
-   Auto-generate invoice code (INV/YYYY/MM/XXXX)
-   Status tracking: Unpaid, Partial, Paid
-   Edit restrictions untuk sales yang sudah dibayar penuh
-   Session flash messages untuk feedback

### 💳 Payment Management

-   CRUD Payments dengan relasi ke Sales
-   Auto-generate payment code (PAY/YYYY/MM/XXXX)
-   Filter by date range dan search
-   Payment history per sale
-   Progress bar pembayaran
-   Quick payment amount buttons (25%, 50%, 75%, Full)
-   Validasi maximum payment sesuai sisa tagihan
-   Auto-update sale status setelah pembayaran

### 🏷️ Items Management

-   CRUD Items (products)
-   Search dan pagination
-   Item code auto-generation
-   Price management

### 👥 User Management

-   Role-based access control
-   User CRUD dengan permissions
-   Authentication dengan Laravel Fortify

## 🛠️ Tech Stack

-   **Framework**: Laravel 12.33.0
-   **PHP**: 8.4.1
-   **Frontend**: Livewire 3, Alpine.js, TailwindCSS
-   **UI Components**: Flux UI
-   **Charts**: Chart.js
-   **Database**: SQLite (development), MySQL (production ready)
-   **Testing**: Pest PHP

## 📋 Requirements

-   PHP >= 8.4
-   Composer
-   Node.js & NPM
-   SQLite/MySQL

## ⚙️ Installation

1. **Clone Repository**

```bash
git clone <repository-url>
cd test-kemampuan-programming
```

2. **Install Dependencies**

```bash
composer install
npm install
```

3. **Environment Setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**

```bash
# For SQLite (default)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

5. **Build Assets**

```bash
npm run build
# atau untuk development
npm run dev
```

6. **Run Application**

```bash
php artisan serve
```

Akses aplikasi di `http://127.0.0.1:8000`

## 🗄️ Database Structure

### Tables

#### `sales`

-   id (PK)
-   sale_code (unique)
-   sale_date
-   total_price
-   total_received
-   status (0: Unpaid, 1: Partial, 2: Paid)
-   user_id (FK)

#### `sale_items`

-   id (PK)
-   sale_id (FK)
-   item_id (FK)
-   quantity
-   price
-   total_price

#### `items`

-   id (PK)
-   name
-   code (unique)
-   price

#### `payments`

-   id (PK)
-   payment_code (unique)
-   sale_id (FK)
-   amount
-   payment_date
-   user_id (FK)

#### `users`

-   id (PK)
-   name
-   email (unique)
-   password
-   role

## 🎯 Key Features Implementation

### Dashboard Charts with Livewire

Dashboard menggunakan kombinasi Livewire untuk data management dan Chart.js untuk visualisasi:

```php
// Backend: Dashboard.php
#[Computed(persist: false)]
public function widgetsData() {
    // Query data dengan filter periode
    // Return array untuk widgets dan charts
}

private function dispatchChartsData() {
    // Dispatch event ke JavaScript saat filter berubah
    $this->dispatch('chartsDataUpdated', [...]);
}
```

```javascript
// Frontend: dashboard.blade.php
Livewire.on("chartsDataUpdated", (data) => {
    chartsData = data[0];
    initCharts(); // Re-render charts
});
```

### Sales dengan Multiple Items

```php
// SaleCreate.php
public function save() {
    DB::transaction(function () {
        $sale = Sale::create([...]);

        foreach ($this->saleItems as $item) {
            $sale->saleItems()->create([...]); // Eloquent relationship
        }
    });
}
```

### Payment Validation

```php
// PaymentCreate.php
public function updatedAmount($value) {
    // Auto-cap amount to remaining balance
    if ($numeric > $this->remaining) {
        $this->amount = $this->remaining;
        $this->dispatch('payment-capped', [...]);
    }
}
```

## 📱 Screenshots

### Dashboard

-   Widget cards dengan statistik real-time
-   Bar chart penjualan bulanan dengan 3 dataset
-   Top 10 items horizontal bar chart
-   Doughnut chart dengan center text plugin

### Sales Management

-   Form create/edit dengan modal item selection
-   Dynamic rows dengan increment/decrement quantity
-   Duplicate item validation
-   Grand total calculation

### Payment Management

-   Payment form dengan sale selection
-   Payment summary dengan progress bar
-   Quick amount buttons
-   Payment history table

## 🧪 Testing

Run tests dengan Pest:

```bash
php artisan test
```

## 🔐 Default Login

Setelah seeding:

```
Email: admin@example.com
Password: password
```

## 📝 Code Structure

```
app/
├── Livewire/
│   ├── Dashboard.php           # Dashboard dengan charts
│   ├── Items/
│   │   ├── ItemIndex.php
│   │   ├── ItemCreate.php
│   │   └── ItemEdit.php
│   ├── Sales/
│   │   ├── SaleIndex.php
│   │   ├── SaleCreate.php      # Multiple items, validation
│   │   └── SaleEdit.php
│   ├── Payments/
│   │   ├── PaymentIndex.php
│   │   ├── PaymentCreate.php   # Auto-cap, quick buttons
│   │   └── PaymentEdit.php
│   └── Users/
│       └── UserIndex.php
├── Models/
│   ├── Sale.php                # Has many saleItems, payments
│   ├── SaleItem.php            # Belongs to sale, item
│   ├── Item.php
│   ├── Payment.php             # Belongs to sale
│   └── User.php

resources/
├── views/
│   └── livewire/
│       ├── dashboard.blade.php  # Chart.js integration
│       ├── sales/
│       ├── payments/
│       └── items/
└── js/
    └── app.js

database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php      # 39 sales, 10 months data
```

## 🐛 Known Issues & Solutions

### Issue: Charts tidak update saat filter berubah

**Solution**: Gunakan Livewire event dispatch dan direct `initCharts()` call tanpa setTimeout.

### Issue: Computed property di-cache

**Solution**: Gunakan `#[Computed(persist: false)]` attribute untuk disable caching.

### Issue: Canvas elements not ready loop

**Solution**: Tambahkan max retry counter dan check dashboard page sebelum initialize.

### Issue: Chart script berjalan di semua halaman

**Solution**: Check canvas existence sebelum initialize, early return jika tidak ada.

## 🔧 Configuration

### Chart.js Configuration

Charts dikonfigurasi dengan custom plugins dan options:

-   Bar thickness adaptif berdasarkan jumlah data
-   Custom tooltip dengan multiple info
-   Responsive dengan maintain aspect ratio
-   Center text plugin untuk doughnut chart

### Livewire Configuration

-   Lazy loading untuk performance
-   Event dispatching untuk chart updates
-   Wire:key untuk force re-render
-   Debounce pada search input

## 📚 Resources

-   [Laravel Documentation](https://laravel.com/docs)
-   [Livewire Documentation](https://livewire.laravel.com)
-   [Chart.js Documentation](https://www.chartjs.org)
-   [Flux UI Documentation](https://flux.laravel.com)

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Developer

Developed as a programming skill test project.

## 🙏 Acknowledgments

-   Laravel Team for the amazing framework
-   Livewire Team for reactive components
-   Chart.js for beautiful charts
-   Community contributors

---

**Version**: 1.0.0  
**Last Updated**: October 11, 2025
