<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    /**
     * Exemple de logique métier embarquée : vérifie si le statut actuel permet une annulation
     */
    /* public function canBeCancelled(): bool
    {
        return match($this) {
            self::Pending, self::Confirmed => true,
            self::Cancelled, self::Completed => false,
        };
    } */
}
