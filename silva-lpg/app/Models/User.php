<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // --- Role helpers -------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isDelivery(): bool
    {
        return $this->role === 'delivery';
    }

    /** Where should this user land after login? */
    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'cashier' => 'cashier.dashboard',
            'delivery' => 'delivery.dashboard',
            default => 'dashboard',
        };
    }

    // --- Relationships --------------------------------------------------

    public function ordersCreated()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function deliveries()
    {
        return $this->hasMany(Order::class, 'assigned_delivery_id');
    }
}
