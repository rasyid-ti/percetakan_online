<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Processing = 'processing';
    case Printing = 'printing';
    case Finishing = 'finishing';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Konfirmasi',
            self::AwaitingPayment => 'Menunggu Pembayaran',
            self::Processing => 'Cek File & Antrean',
            self::Printing => 'Sedang Dicetak',
            self::Finishing => 'Finishing',
            self::Ready => 'Siap Diambil / Dikirim',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::AwaitingPayment => 'orange',
            self::Processing => 'sky',
            self::Printing => 'indigo',
            self::Finishing => 'violet',
            self::Ready => 'emerald',
            self::Completed => 'green',
            self::Cancelled => 'rose',
        };
    }
}