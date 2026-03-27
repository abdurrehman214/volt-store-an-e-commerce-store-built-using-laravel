<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'delivery_type_code',
        'total_amount',
        'status',
        'dispatched_at',
    ];

    protected $casts = [
        'total_amount'  => 'decimal:2',
        'dispatched_at' => 'datetime',
    ];

    // ─── Set order_number to PENDING placeholder on create ───────────────────
    // ✅ KEPT — generateOrderNumber() is called after items are saved,
    //    so we need a placeholder value while the order row is first inserted
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'PENDING';
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    // ✅ KEPT
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ KEPT
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ✅ KEPT
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // ✅ KEPT
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    // ✅ FIX: was missing — AdminController's showOrder() calls
    //    $order->returns but the relationship didn't exist
    public function returns(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    // ─── Generate 16-digit order number ──────────────────────────────────────
    // ✅ KEPT your exact logic — call this after order items are saved
    // Format:  [1-digit delivery type] + [7-digit product code] + [8-digit order id]
    // Example: 1SH0000100000042
    public function generateOrderNumber(): void
    {
        $firstItem   = $this->orderItems()->with('product')->first();
        $productCode = $firstItem ? $firstItem->product->product_code : '0000000';
        $sequence    = str_pad($this->id, 8, '0', STR_PAD_LEFT);

        $this->order_number = $this->delivery_type_code . $productCode . $sequence;
        $this->save();
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Use in Blade for status badge colour:
    //   <span class="{{ $order->statusColor() }}">{{ $order->status }}</span>
    public function statusColor(): string
    {
        return match($this->status) {
            'pending'    => 'bg-yellow-100 text-yellow-800',
            'cleared'    => 'bg-blue-100 text-blue-800',
            'dispatched' => 'bg-purple-100 text-purple-800',
            'completed'  => 'bg-green-100 text-green-800',
            'cancelled'  => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }

    // Can the customer still cancel this order?
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'cleared']);
    }

    // Can admin dispatch this order?
    public function isDispatchable(): bool
    {
        if ($this->status !== 'cleared' && $this->status !== 'pending') {
            return false;
        }
        // VPP (cash on delivery) can be dispatched without payment clearance
        if ($this->payment && $this->payment->method === 'vpp') {
            return true;
        }
        return $this->payment && $this->payment->clearance_status === 'cleared';
    }
}