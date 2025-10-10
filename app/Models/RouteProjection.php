<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RouteProjection extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'route_id',
        'projection_type',
        'period',
        'projection_date',
        'projected_value',
        'actual_value',
        'variance',
        'notes',
    ];

    protected $casts = [
        'projection_date' => 'date',
        'projected_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['projection_type', 'period', 'projected_value', 'actual_value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Calculate variance between projected and actual values
     */
    public function calculateVariance(): float
    {
        if ($this->actual_value === null) {
            return 0;
        }

        return $this->actual_value - $this->projected_value;
    }

    /**
     * Get variance percentage
     */
    public function getVariancePercentageAttribute(): float
    {
        if ($this->projected_value == 0) {
            return 0;
        }

        return ($this->variance / $this->projected_value) * 100;
    }

    /**
     * Get available projection types
     */
    public static function getProjectionTypes(): array
    {
        return [
            'revenue' => 'Revenue',
            'shipments' => 'Shipments',
            'expenses' => 'Expenses',
            'profit' => 'Profit',
        ];
    }

    /**
     * Get available periods
     */
    public static function getPeriods(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
}