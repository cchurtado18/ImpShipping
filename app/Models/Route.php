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
        'status',
        'responsible',
        'route_start_date',
        'route_end_date',
        'states',
        'is_active',
    ];

    protected $casts = [
        'route_start_date' => 'date',
        'route_end_date' => 'date',
        'states' => 'array',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'responsible', 'route_start_date', 'route_end_date', 'states', 'is_active'])
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

    /**
     * Get available responsible persons
     */
    public static function getResponsibleOptions(): array
    {
        return [
            'Francisco' => 'Francisco',
            'Elmer' => 'Elmer',
            'Geovanni' => 'Geovanni',
        ];
    }

    /**
     * Get responsible person name
     */
    public function getResponsibleNameAttribute(): string
    {
        return $this->responsible ?? 'Not assigned';
    }

    /**
     * Get available US states
     */
    public static function getUSStates(): array
    {
        return [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];
    }

    /**
     * Get clients that match this route's states
     */
    public function getEligibleClients()
    {
        if (empty($this->states)) {
            return collect();
        }

        return \App\Models\Client::whereIn('us_state', $this->states)->get();
    }

    /**
     * Get formatted states list
     */
    public function getFormattedStatesAttribute(): string
    {
        if (empty($this->states)) {
            return 'No states assigned';
        }

        $stateNames = [];
        $allStates = self::getUSStates();
        
        foreach ($this->states as $stateCode) {
            $stateNames[] = $allStates[$stateCode] ?? $stateCode;
        }

        return implode(', ', $stateNames);
    }

    /**
     * Check if route is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate route
     */
    public function activate()
    {
        // Deactivate all other routes first
        self::where('is_active', true)->update(['is_active' => false]);
        
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate route
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }
}
