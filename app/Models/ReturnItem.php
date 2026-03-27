<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;

    // ✅ FIX: was completely empty — AdminController does:
    //    ReturnItem::with(['order.user'])->where('status', 'requested')
    //    and $return->update(['status' => 'approved'])
    //    both silently failed because $fillable was missing

    // Note: the table is named 'returns' in migration
    // Laravel would guess 'return_items' — we set it explicitly
    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'type',            // return | replacement
        'reason',
        'status',          // requested | approved | received | processed
        'requested_date',
    ];

    protected $casts = [
        'requested_date' => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    // AdminController calls ReturnItem::with(['order.user'])
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Human-readable type label
    public function typeLabel(): string
    {
        return match($this->type) {
            'return'      => 'Return for Refund',
            'replacement' => 'Replacement Item',
            default       => ucfirst($this->type),
        };
    }

    // Tailwind badge colour for return status
    public function statusColor(): string
    {
        return match($this->status) {
            'requested' => 'bg-yellow-100 text-yellow-800',
            'approved'  => 'bg-blue-100 text-blue-800',
            'received'  => 'bg-purple-100 text-purple-800',
            'processed' => 'bg-green-100 text-green-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    // Is this return still within the 7-day policy window?
    // AdminController's showReturn() checks this — now clean and reusable
    public function isWithinPolicy(): bool
    {
        $delivery = $this->order?->delivery;
        if (! $delivery || ! $delivery->delivered_at) {
            return false;
        }
        return $delivery->daysSinceDelivery() <= 7;
    }
}