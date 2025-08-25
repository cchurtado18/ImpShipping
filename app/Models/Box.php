<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'length_in',
        'width_in',
        'height_in',
        'base_price_usd',
        'active',
    ];

    protected $casts = [
        'length_in' => 'decimal:2',
        'width_in' => 'decimal:2',
        'height_in' => 'decimal:2',
        'base_price_usd' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function getCurrentPriceAttribute(): float
    {
        $currentMonth = now()->format('Y-m');
        $priceList = $this->priceLists()
            ->where('valid_from', '<=', $currentMonth)
            ->where(function ($query) use ($currentMonth) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $currentMonth);
            })
            ->first();

        return $priceList ? $priceList->price_usd : $this->base_price_usd;
    }

    public function getCubicFeetAttribute(): float
    {
        $cubicInches = $this->length_in * $this->width_in * $this->height_in;
        return round($cubicInches / 1728, 2);
    }

    public function getFormattedDimensionsAttribute(): string
    {
        return "{$this->length_in}\" × {$this->width_in}\" × {$this->height_in}\"";
    }
}
