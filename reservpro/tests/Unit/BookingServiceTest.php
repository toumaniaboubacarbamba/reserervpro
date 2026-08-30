<?php

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Establishment;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;

test('rejette une réservation quand le créneau est complet', function () {
    // Étape 1 — L'univers de test
    $gerant = User::factory()->create(['role' => UserRole::Gerant]);
    $establishment = Establishment::create([
        'owner_id' => $gerant->id,
        'name' => 'Salon Test',
        'category' => 'salon',
    ]);
    $timeSlot = TimeSlot::create([
        'establishment_id' => $establishment->id,
        'day_of_week' => 2,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'capacity' => 1,
    ]);

    $client1 = User::factory()->create(['role' => UserRole::Client]);
    $client2 = User::factory()->create(['role' => UserRole::Client]);

    $service = new BookingService();

    $service->reserver($timeSlot, $client1, '2026-09-15', 5000);

    expect(fn () => $service->reserver($timeSlot, $client2, '2026-09-15', 5000))
        ->toThrow(\Exception::class, 'Plus de places disponibles pour ce créneau.');

    expect(Booking::count())->toBe(1);
});

test('applique une penalite de 50 pourcent si le client annule moins de 24h avant la prestation', function () {
    $maintenant = Carbon::parse('2026-09-14 10:00:00');
    Carbon::setTestNow($maintenant);

    $gerant = User::factory()->create(['role' => UserRole::Gerant]);
    $establishment = Establishment::create([
        'owner_id' => $gerant->id,
        'name' => 'Salon Test Annulation',
        'category' => 'salon',
    ]);

    $timeSlot = TimeSlot::create([
        'establishment_id' => $establishment->id,
        'day_of_week' => 2,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'capacity' => 5,
    ]);

    $client = User::factory()->create(['role' => UserRole::Client]);

    $booking = Booking::create([
        'user_id' => $client->id,
        'time_slot_id' => $timeSlot->id,
        'booking_date' => Carbon::parse('2026-09-15'),
        'amount' => 10000,
        'status' => BookingStatus::Pending,
    ]);

    $service = new BookingService();
    $bookingAnnule = $service->annuler($booking, $client);

    expect($bookingAnnule->status)->toBe(BookingStatus::Cancelled);
    expect($bookingAnnule->cancelled_by)->toBe('client');
    expect($bookingAnnule->refund_amount)->toBe(5000);

    Carbon::setTestNow();
});
