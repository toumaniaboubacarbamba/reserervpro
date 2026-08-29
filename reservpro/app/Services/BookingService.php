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

    // Annuler une réservation
   public function annuler(Booking $booking, User $user): Booking
{

    return DB::transaction(function () use ($booking, $user) {
        $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();

        $estGerant = $user->id === $booking->timeSlot->establishment->owner_id;
        $estClient = $user->id === $booking->user_id;

        if (! $estGerant && ! $estClient) {
            throw new \Exception('Non autorisé à annuler cette réservation.');
        }

        if ($estGerant) {
            $booking->cancelled_by = 'gerant';
            $booking->refund_amount = $booking->amount; // remboursement total
        } else {
            // Calcul du délai avant le début de la prestation
            $debutPrestation = \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->timeSlot->start_time);
            $heuresRestantes = now()->diffInHours($debutPrestation, false);

            $booking->cancelled_by = 'client';
            $booking->refund_amount = $heuresRestantes >= 24
                ? $booking->amount
                : (int) round($booking->amount * 0.5);
        }

        $booking->status = \App\Enums\BookingStatus::Cancelled;
        $booking->save();

        return $booking;
    });

    
}
}
