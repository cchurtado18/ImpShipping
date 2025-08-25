<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'valid_from',
        'valid_to',
        'price_usd',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'price_usd' => 'decimal:2',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }
}
