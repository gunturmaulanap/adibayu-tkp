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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade'); // Pembayaran untuk penjualan yang mana
            $table->unsignedBigInteger('amount'); // Jumlah yang dibayarkan
            $table->date('payment_date');
            $table->foreignId('user_id')->constrained('users'); // Pembayaran diterima oleh siapa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
