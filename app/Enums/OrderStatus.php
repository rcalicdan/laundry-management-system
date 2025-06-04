<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case DELIVERED = 'delivered';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::DELIVERED => 'Delivered',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::DELIVERED => 'bg-purple-100 text-purple-800',
        };
    }
}