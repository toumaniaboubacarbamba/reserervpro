<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    //
    protected $fillable = [
        'establishment_id',
        'day_of_week',
        'start_time',
        'end_time',
        'capacity',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    // un crénau générique est lié à plusieurs réservations au fil du temps (en tant que client)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }



    // un crénau générique est lié à plusieurs réservations au fil du temps (en tant que propriétaire)
    public function placesRestantes(\Carbon\Carbon|string $date): int
{
    $occupees = $this->bookings()
        ->where('booking_date', $date)
        ->where('status', '!=', BookingStatus::Cancelled)
        ->count();

    return $this->capacity - $occupees;
}
}
