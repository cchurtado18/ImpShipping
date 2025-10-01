<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Shipment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id',
        'recipient_id',
        'route_id',
        'box_id',
        'code',
        'shipment_status',
        'payment_status',
        'sale_price_usd',
        'declared_value',
        'notes',
        'invoiced',
    ];

    protected $casts = [
        'sale_price_usd' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'invoiced' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            if (empty($shipment->code)) {
                $shipment->code = 'SG-' . now()->format('Ym') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['shipment_status', 'sale_price_usd', 'recipient_id', 'route_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount_usd');
    }

    public function getPaymentStatusAttribute(): string
    {
        $totalPaid = $this->total_paid;
        $salePrice = $this->sale_price_usd ?? 0;

        if ($totalPaid >= $salePrice) {
            return 'paid';
        } elseif ($totalPaid > 0) {
            return 'partial';
        } else {
            return 'pending';
        }
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, ($this->sale_price_usd ?? 0) - $this->total_paid);
    }

    public function isDelivered(): bool
    {
        return $this->shipment_status === 'entregado';
    }

    public function isCancelled(): bool
    {
        return $this->shipment_status === 'cancelled';
    }

    public function getShipmentStatusLabelAttribute(): string
    {
        return match($this->shipment_status) {
            'por_recepcionar' => 'Por Recepcionar',
            'recepcionado' => 'Recepcionado',
            'dejado_almacen' => 'Dejado en Almacén',
            'en_nicaragua' => 'En Nicaragua',
            'entregado' => 'Entregado',
            'cancelled' => 'Cancelado',
            default => ucfirst($this->shipment_status)
        };
    }

    public function getShipmentStatusColorAttribute(): string
    {
        return match($this->shipment_status) {
            'por_recepcionar' => 'bg-gray-100 text-gray-800',
            'recepcionado' => 'bg-blue-100 text-blue-800',
            'dejado_almacen' => 'bg-yellow-100 text-yellow-800',
            'en_nicaragua' => 'bg-purple-100 text-purple-800',
            'entregado' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getShipmentStatusIconAttribute(): string
    {
        return match($this->shipment_status) {
            'por_recepcionar' => '📦',
            'recepcionado' => '✅',
            'dejado_almacen' => '🏪',
            'en_nicaragua' => '🇳🇮',
            'entregado' => '🎉',
            'cancelled' => '❌',
            default => '📦'
        };
    }
}
