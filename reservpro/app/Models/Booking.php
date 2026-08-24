<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * Note sur la sécurité : 'status' est délibérément EXCLU du fillable.
     * La base de données applique automatiquement la valeur par défaut 'pending'.
     * Tout changement de statut ultérieur (validation CinetPay/Wave, annulation)
     * se fera via une méthode métier explicite pour empêcher toute falsification par le client.
     */
    protected $fillable = [
        'user_id',
        'time_slot_id',
        'booking_date',
        'amount',
    ];

    /**
     * Les moulages (casts) d'attributs.
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'status' => BookingStatus::class, // Cast automatique vers notre Enum PHP
        ];
    }

    /**
     * Une réservation appartient à un utilisateur (le client).
     * La colonne 'user_id' respecte la convention, pas d'argument requis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une réservation est adossée à un créneau (le template récurrent).
     * La colonne 'time_slot_id' respecte la convention, pas d'argument requis.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
