<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Route extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'month',
        'collection_start_at',
        'cutoff_at',
        'departure_at',
        'arrival_at',
        'status',
    ];

    protected $casts = [
        'collection_start_at' => 'datetime',
        'cutoff_at' => 'datetime',
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'collection_start_at', 'cutoff_at', 'departure_at', 'arrival_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function routeExpenses(): HasMany
    {
        return $this->hasMany(RouteExpense::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Shipment::class);
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isCollecting(): bool
    {
        return $this->status === 'collecting';
    }
}
