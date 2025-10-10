<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteLead extends Model
{
    protected $fillable = [
        'client_id',
        'box_height',
        'box_width',
        'box_length',
        'nicaragua_address',
        'nicaragua_phone',
        'box_quantity',
        'notes',
        'status',
    ];

    protected $casts = [
        'box_quantity' => 'integer',
        'box_height' => 'decimal:2',
        'box_width' => 'decimal:2',
        'box_length' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            default => 'Unknown',
        };
    }

    public function getBoxDimensionsAttribute(): string
    {
        if ($this->box_height && $this->box_width && $this->box_length) {
            return "{$this->box_height}\" × {$this->box_width}\" × {$this->box_length}\"";
        }
        return 'Not specified';
    }
}
