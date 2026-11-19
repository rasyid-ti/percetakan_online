<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'code', 'user_id', 'operator_id', 'status', 'payment_status',
        'delivery_method', 'delivery_address', 'subtotal', 'shipping_cost',
        'total', 'notes', 'deadline', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'deadline' => 'date',
            'completed_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }
     
    /** Kode ambil 6 karakter, dihitung dari kode pesanan (tidak perlu disimpan). */
    protected function pickupCode(): Attribute
    {
        return Attribute::get(
            fn () => strtoupper(substr(hash_hmac('sha256', (string) $this->code, (string) config('app.key')), 0, 6))
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    /** Contoh hasil: TKT-20261116-0001 */
    public static function generateCode(): string
    {
        $prefix = 'TKT-' . now()->format('Ymd') . '-';
        $count = static::where('code', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    protected function totalLabel(): Attribute
    {
        return Attribute::get(fn () => 'Rp ' . number_format($this->total, 0, ',', '.'));
    }
}