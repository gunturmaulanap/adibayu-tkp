<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'price',
        'total_price',
    ];

    /**
     * Mendefinisikan relasi ke model Sale.
     * Sebuah item penjualan adalah bagian dari satu penjualan.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Mendefinisikan relasi ke model Item.
     * Sebuah item penjualan mengacu pada satu master item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the qty attribute (alias for quantity)
     *
     * @return int
     */
    public function getQtyAttribute()
    {
        return $this->attributes['quantity'] ?? 0;
    }

    /**
     * Set the qty attribute (alias for quantity)
     *
     * @param  int  $value
     * @return void
     */
    public function setQtyAttribute($value)
    {
        $this->attributes['quantity'] = $value;
    }
}
