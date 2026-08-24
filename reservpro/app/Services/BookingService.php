<?php

namespace App\Services;

use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingService
{
    public function reserver(TimeSlot $timeSlot, User $client, Carbon|string $date, int $amount): Booking
    {
        return DB::transaction(function () use ($timeSlot, $client, $date, $amount) {
            $timeSlot = TimeSlot::where('id', $timeSlot->id)->lockForUpdate()->first();

            if ($timeSlot->placesRestantes($date) <= 0) {
                throw new \Exception('Plus de places disponibles pour ce créneau.');
            }

            return Booking::create([
                'user_id' => $client->id,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $date,
                'amount' => $amount,
            ]);
        });
    }
}
