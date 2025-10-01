<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'terms',
        'sender_name',
        'sender_phone',
        'sender_address',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'service_description',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'tax_amount',
        'total',
        'total_amount',
        'status',
        'invoice_status',
        'notes',
        'client_id',
        'shipment_id',
        'user_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate next invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 3) + 1 : 1;
        return '14-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'overdue' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid';
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update(['status' => 'sent']);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Mark as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Get formatted invoice status
     */
    public function getFormattedInvoiceStatusAttribute(): string
    {
        return match($this->invoice_status) {
            'pending' => 'Pending',
            'cancelled_by_cash' => 'Cancelled by Cash',
            'cancelled_by_transfer' => 'Cancelled by Transfer',
            default => 'Unknown'
        };
    }

    /**
     * Get invoice status badge color
     */
    public function getInvoiceStatusColorAttribute(): string
    {
        return match($this->invoice_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled_by_cash' => 'bg-green-100 text-green-800',
            'cancelled_by_transfer' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get invoice status icon
     */
    public function getInvoiceStatusIconAttribute(): string
    {
        return match($this->invoice_status) {
            'pending' => '⏳',
            'cancelled_by_cash' => '💰',
            'cancelled_by_transfer' => '🏦',
            default => '❓'
        };
    }
}
