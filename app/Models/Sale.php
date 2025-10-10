<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Sale extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_UNPAID = 0; // Belum Bayar
    public const STATUS_PARTIAL = 1; // Belum Dibayar Sepenuhnya
    public const STATUS_PAID = 2;    // Sudah Dibayar

    // Automatically append these accessors to arrays/json
    protected $appends = [
        'code',
        'status_label',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_code',
        'user_id',
        'total_price',
        'total_received',
        'status',
        'sale_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_date' => 'date',
    ];

    /**
     * Friendly code accessor (maps sale_code -> code)
     */
    public function getCodeAttribute()
    {
        return $this->sale_code;
    }

    /**
     * Return human friendly status label based on status integer
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_PARTIAL => 'Belum Dibayar Sepenuhnya',
            default => 'Belum Bayar',
        };
    }

    /**
     * Recalculate and update sale status based on payments sum.
     * - 0 => belum bayar
     * - 1 => partial
     * - 2 => paid
     */
    public function updateStatusBasedOnPayments(): void
    {
        $paid = (int) $this->payments()->sum('amount');
        if ($paid <= 0) {
            $new = self::STATUS_UNPAID;
        } elseif ($paid < $this->total_price) {
            $new = self::STATUS_PARTIAL;
        } else {
            $new = self::STATUS_PAID;
        }

        if ($this->status !== $new) {
            $this->status = $new;
            $this->total_received = $paid;
            $this->save();
        } else {
            // always keep total_received in sync
            if ($this->total_received !== $paid) {
                $this->total_received = $paid;
                $this->save();
            }
        }
    }

    /**
     * Get remaining balance for this sale
     */
    public function getRemainingBalance(): int
    {
        $paid = (int) $this->payments()->sum('amount');
        return max(0, $this->total_price - $paid);
    }

    public function getPriceFormattedAttribute()
    {
        if ($this->total_price === null) return null;
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
    /**
     * Mendefinisikan relasi ke model User.
     * Sebuah penjualan dibuat oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi ke model SaleItem.
     * Sebuah penjualan memiliki banyak item penjualan.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Mendefinisikan relasi ke model Payment.
     * Sebuah penjualan bisa memiliki banyak pembayaran (untuk fitur cicilan).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
