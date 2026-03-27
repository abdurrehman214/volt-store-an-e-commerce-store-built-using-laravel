<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    // ✅ FIX: was completely empty — AdminController does:
    //    Delivery::updateOrCreate(['order_id' => $order->id], [...])
    //    which silently failed because $fillable was missing

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'status',          // processing | shipped | out_for_delivery | delivered | returned
        'delivered_at',    // ✅ needed for 7-day return policy check in AdminController
        'delivery_report',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Human-readable status for Blade
    // {{ $order->delivery->statusLabel() }}
    public function statusLabel(): string
    {
        return match($this->status) {
            'processing'       => 'Processing',
            'shipped'          => 'Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered'        => 'Delivered',
            'returned'         => 'Returned',
            default            => ucfirst($this->status),
        };
    }

    // Tailwind badge colour for delivery status
    public function statusColor(): string
    {
        return match($this->status) {
            'processing'       => 'bg-gray-100 text-gray-800',
            'shipped'          => 'bg-blue-100 text-blue-800',
            'out_for_delivery' => 'bg-purple-100 text-purple-800',
            'delivered'        => 'bg-green-100 text-green-800',
            'returned'         => 'bg-red-100 text-red-800',
            default            => 'bg-gray-100 text-gray-800',
        };
    }

    // Has this delivery been received by the customer?
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    // How many days since delivery — used by return policy check
    // AdminController uses:  $daysSinceDelivery = $return->order->delivery->daysSinceDelivery()
    public function daysSinceDelivery(): int
    {
        if (! $this->delivered_at) {
            return 0;
        }
        return (int) now()->diffInDays($this->delivered_at);
    }
}