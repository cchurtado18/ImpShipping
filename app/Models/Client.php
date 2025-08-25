<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'us_address',
        'us_state',
        'us_phone',
        'email',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasManyThrough(Payment::class, Shipment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'en_seguimiento' => 'bg-yellow-100 text-yellow-800',
            'confirmado' => 'bg-green-100 text-green-800',
            'proxima_ruta' => 'bg-blue-100 text-blue-800',
            'ruta_cancelada' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_seguimiento' => 'En Seguimiento',
            'confirmado' => 'Confirmado',
            'proxima_ruta' => 'Próxima Ruta',
            'ruta_cancelada' => 'Ruta Cancelada',
            default => 'Desconocido',
        };
    }
}
