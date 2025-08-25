<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'category',
        'description',
        'amount_usd',
        'paid_at',
        'vendor',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
