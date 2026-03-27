<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    // ✅ FIX: was completely empty — AdminController does Payment::create([...])
    //    and $order->payment->update([...]) which silently failed
    //    because nothing was in $fillable

    protected $fillable = [
        'order_id',
        'method',            // credit_card | cheque | vpp | dd
        'clearance_status',  // pending | cleared | refunded
        'cleared_at',
    ];

    protected $casts = [
        'cleared_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Human-readable method name for Blade
    // {{ $order->payment->methodLabel() }}
    public function methodLabel(): string
    {
        return match($this->method) {
            'credit_card' => 'Credit Card',
            'cheque'      => 'Cheque',
            'vpp'         => 'Cash on Delivery (VPP)',
            'dd'          => 'Direct Debit',
            default       => ucfirst($this->method),
        };
    }

    // VPP (cash on delivery) doesn't need pre-clearance before dispatch
    public function requiresClearance(): bool
    {
        return $this->method !== 'vpp';
    }

    public function isCleared(): bool
    {
        return $this->clearance_status === 'cleared';
    }

    public function isPending(): bool
    {
        return $this->clearance_status === 'pending';
    }

    // Tailwind badge colour — use in admin order views
    // <span class="{{ $order->payment->statusColor() }}">...</span>
    public function statusColor(): string
    {
        return match($this->clearance_status) {
            'pending'  => 'bg-yellow-100 text-yellow-800',
            'cleared'  => 'bg-green-100 text-green-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }
}