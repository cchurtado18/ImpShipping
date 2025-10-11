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
        'reception_status',
        'received_at',
        'loaded_at',
        'reception_photo_path',
        'reception_notes',
        'received_by',
        'loaded_by',
        // Dimensiones personalizadas
        'custom_length',
        'custom_width',
        'custom_height',
        'custom_weight',
        'custom_weight_rate',
        'calculated_price',
        'use_calculated_price',
        'manual_price',
        'price_mode',
    ];

    protected $casts = [
        'sale_price_usd' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'invoiced' => 'boolean',
        'received_at' => 'datetime',
        'loaded_at' => 'datetime',
        // Dimensiones personalizadas
        'custom_length' => 'decimal:2',
        'custom_width' => 'decimal:2',
        'custom_height' => 'decimal:2',
        'custom_weight' => 'decimal:2',
        'custom_weight_rate' => 'decimal:2',
        'calculated_price' => 'decimal:2',
        'use_calculated_price' => 'boolean',
        'manual_price' => 'decimal:2',
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

    // Relaciones para recepción
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function loadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'loaded_by');
    }

    // Métodos para estado de recepción
    public function getReceptionStatusLabelAttribute(): string
    {
        return match($this->reception_status) {
            'pending' => 'Pendiente',
            'received' => 'Recepcionado',
            'loaded' => 'Cargado',
            'in_transit' => 'En Tránsito',
            'delivered' => 'Entregado',
            default => ucfirst($this->reception_status)
        };
    }

    public function getReceptionStatusColorAttribute(): string
    {
        return match($this->reception_status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'received' => 'bg-blue-100 text-blue-800',
            'loaded' => 'bg-yellow-100 text-yellow-800',
            'in_transit' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function isReceived(): bool
    {
        return $this->reception_status === 'received';
    }

    public function isLoaded(): bool
    {
        return $this->reception_status === 'loaded';
    }

    public function canBeLoaded(): bool
    {
        return $this->reception_status === 'received' && !empty($this->reception_photo_path);
    }

    public function getAvailableReceptionStatuses(): array
    {
        return match($this->reception_status) {
            'pending' => ['received'],
            'received' => ['loaded'],
            'loaded' => ['in_transit'],
            'in_transit' => ['delivered'],
            'delivered' => [],
            default => ['received']
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

    // Métodos para dimensiones personalizadas
    public function hasCustomDimensions(): bool
    {
        return !empty($this->custom_length) || !empty($this->custom_width) || !empty($this->custom_height);
    }

    public function getCustomCubicFeetAttribute(): float
    {
        if (!$this->hasCustomDimensions()) {
            return 0;
        }
        
        $cubicInches = ($this->custom_length ?? 0) * ($this->custom_width ?? 0) * ($this->custom_height ?? 0);
        return round($cubicInches / 1728, 2);
    }

    public function getFormattedCustomDimensionsAttribute(): string
    {
        if (!$this->hasCustomDimensions()) {
            return 'N/A';
        }
        
        return "{$this->custom_length}\" × {$this->custom_width}\" × {$this->custom_height}\"";
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->use_calculated_price ? ($this->calculated_price ?? 0) : ($this->manual_price ?? 0);
    }

    public function getPriceModeLabelAttribute(): string
    {
        return $this->use_calculated_price ? 'Calculado' : 'Manual';
    }
}
