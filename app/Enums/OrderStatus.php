<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::IN_PROGRESS => 'Sedang Dibuat',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-200 text-yellow-800',
            self::IN_PROGRESS => 'bg-blue-200 text-blue-800',
            self::COMPLETED => 'bg-green-200 text-green-800',
            self::CANCELLED => 'bg-red-200 text-red-800',
        };
    }
}