<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_code',
        'sale_id',
        'amount',
        'payment_date',
        'user_id', // Opsional: untuk melacak siapa yang input pembayaran
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Mendefinisikan relasi ke model Sale.
     * Sebuah pembayaran adalah bagian dari satu penjualan.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Mendefinisikan relasi ke model User.
     * Opsional: untuk mengetahui user mana yang mencatat pembayaran.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
