<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_code')->unique(); // Kode penjualan unik
            $table->foreignId('user_id')->constrained('users'); // Penjualan dilakukan oleh user siapa
            $table->unsignedBigInteger('total_price'); // Total akhir dari semua item
            $table->unsignedBigInteger('total_received')->default(0); // Total yang sudah dibayar (untuk fitur cicilan)

            // Status penjualan: 0=Belum Dibayar, 1=Belum Lunas, 2=Lunas
            $table->unsignedTinyInteger('status')->default(0);

            $table->date('sale_date'); // Tanggal transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
