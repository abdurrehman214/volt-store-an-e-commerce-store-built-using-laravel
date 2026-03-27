<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;
protected $table = 'feedbacks';
    // ✅ FIX: The feedbacks migration existed in your project but the
    //    Feedback model was completely missing. AdminController has a
    //    feedback() method and imports `use App\Models\Feedback` at the top —
    //    this would throw a fatal "Class not found" error at runtime.

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',       // 1 to 5
        'comment',
        'is_visible',   // admin can hide inappropriate reviews
    ];

    protected $casts = [
        'rating'     => 'integer',
        'is_visible' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Render star rating as a string — use in Blade
    // {{ $feedback->stars() }}  →  "★★★★☆"
    public function stars(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    // Tailwind colour for rating badge
    public function ratingColor(): string
    {
        return match(true) {
            $this->rating >= 4 => 'bg-green-100 text-green-800',
            $this->rating === 3 => 'bg-yellow-100 text-yellow-800',
            default             => 'bg-red-100 text-red-800',
        };
    }
}