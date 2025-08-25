<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate_usd_to_cordoba',
        'effective_date',
    ];

    protected $casts = [
        'rate_usd_to_cordoba' => 'decimal:4',
        'effective_date' => 'date',
    ];

    public static function getCurrentRate(): ?float
    {
        return static::where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->value('rate_usd_to_cordoba');
    }
}
