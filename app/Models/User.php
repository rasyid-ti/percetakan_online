<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'role', 'password', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'operator_id');
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isOwner(): bool    { return $this->role === Role::Owner; }
    public function isAdmin(): bool    { return $this->role === Role::Admin; }
    public function isOperator(): bool { return $this->role === Role::Operator; }
    public function isCustomer(): bool { return $this->role === Role::Customer; }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->implode('');
    }
}