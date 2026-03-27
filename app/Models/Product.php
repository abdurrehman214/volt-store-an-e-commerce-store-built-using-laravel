<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'description',
        'price',
        'stock_quantity',
        'category',       // ✅ column now exists in fixed migration
        'image_url',      // ✅ column now exists in fixed migration
        'warranty_applicable',
        'is_active',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'stock_quantity'      => 'integer',
        'warranty_applicable' => 'boolean',
        'is_active'           => 'boolean',
    ];

    // ─── Auto-generate 7-digit product code on create ─────────────────────────
    // ✅ KEPT exactly as you wrote it — this logic is solid
    // Example: category "shoes" → code "SH00001", next one → "SH00002"
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->product_code)) {
                $categoryCode = strtoupper(substr($product->category ?? 'PR', 0, 2));

                $lastProduct = static::where('product_code', 'like', "{$categoryCode}%")
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastProduct
                    ? (int) substr($lastProduct->product_code, 2) + 1
                    : 1;

                $product->product_code = $categoryCode . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    // ✅ KEPT — One product can appear in many order items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Use in Blade:  @if($product->inStock())
    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    // Use in Blade:  @if($product->isLowStock())  → show "Only 3 left!" warning
    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity < 10;
    }

    // Returns image_url or a placeholder if none uploaded yet
    // Use in Blade:  <img src="{{ $product->getImage() }}">
    public function getImage(): string
    {
        return $this->image_url
            ?? 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=400&q=70';
    }
}