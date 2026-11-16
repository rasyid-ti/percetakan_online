<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Operator = 'operator';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Admin => 'Admin',
            self::Operator => 'Operator Produksi',
            self::Customer => 'Pelanggan',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Owner => 'owner.dashboard',
            self::Admin => 'admin.dashboard',
            self::Operator => 'operator.dashboard',
            self::Customer => 'customer.dashboard',
        };
    }
}