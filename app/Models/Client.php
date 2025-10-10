<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'next_followup_at',
        'followup_note',
        'client_type',
        'maritime_pound_cost',
        'air_pound_cost',
        'cubic_foot_cost',
        'is_followup_enabled',
        'followup_label',
        'followup_reminder_hours',
        'last_contacted_at',
        'followup_notes',
    ];

    protected $casts = [
        'status' => 'string',
        'next_followup_at' => 'datetime',
        'maritime_pound_cost' => 'decimal:2',
        'air_pound_cost' => 'decimal:2',
        'cubic_foot_cost' => 'decimal:2',
        'is_followup_enabled' => 'boolean',
        'last_contacted_at' => 'datetime',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Shipment::class);
    }

    public function routeLeads(): HasMany
    {
        return $this->hasMany(RouteLead::class);
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

    public function getFollowupStatusAttribute(): string
    {
        if (!$this->is_followup_enabled || !$this->next_followup_at) {
            return 'disabled';
        }

        $now = now();
        $followupTime = $this->next_followup_at;

        if ($followupTime->isPast()) {
            return 'overdue';
        } elseif ($followupTime->isToday()) {
            return 'today';
        } elseif ($followupTime->isFuture() && $followupTime->diffInDays($now) <= 7) {
            return 'upcoming';
        }

        return 'scheduled';
    }

    public function getFollowupStatusBadgeAttribute(): string
    {
        return match($this->followup_status) {
            'overdue' => 'bg-red-100 text-red-800',
            'today' => 'bg-yellow-100 text-yellow-800',
            'upcoming' => 'bg-green-100 text-green-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFollowupStatusLabelAttribute(): string
    {
        return match($this->followup_status) {
            'overdue' => 'Overdue',
            'today' => 'Today',
            'upcoming' => 'Upcoming',
            'scheduled' => 'Scheduled',
            default => 'Disabled',
        };
    }

    public function scheduleFollowup($hours = 24, $label = null, $notes = null)
    {
        // Ensure hours is an integer
        $hours = (int) $hours;
        
        $this->update([
            'is_followup_enabled' => true,
            'next_followup_at' => now()->addHours($hours),
            'followup_reminder_hours' => $hours,
            'followup_label' => $label,
            'followup_notes' => $notes,
        ]);
    }

    public function markAsContacted($notes = null)
    {
        $this->update([
            'last_contacted_at' => now(),
            'followup_notes' => $notes,
            'next_followup_at' => null,
            'is_followup_enabled' => false,
        ]);
    }
}
