<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',         // 'admin', 'employee', 'customer'
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    // ✅ FIX: was missing — AdminController calls $order->user and
    //    myOrders() needs User → Orders link for auth()->user()->orders()
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ✅ FIX: was missing — needed for admin feedback view
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    // ─── Helper methods ───────────────────────────────────────────────────────

    // Use these in Blade instead of checking role string directly
    // e.g.  @if(auth()->user()->isAdmin())  instead of  @if(auth()->user()->role === 'admin')
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // True for both admin and employee — useful for admin panel access checks
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'employee']);
    }
}