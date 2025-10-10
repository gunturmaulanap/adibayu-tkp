<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'image',
        'price',
        'stock',
    ];

    // Accessor to get price formatted as Indonesian Rupiah (e.g. Rp 1.000.000)
    public function getPriceFormattedAttribute()
    {
        if ($this->price === null) return null;
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
