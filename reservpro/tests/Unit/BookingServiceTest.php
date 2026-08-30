<?php

use App\Enums\UserRole;
use App\Models\Establishment;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\BookingService;

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

    // Étape 2 — Saturation (cas passant)
    $service->reserver($timeSlot, $client1, '2026-09-15', 5000);

    // Étape 3 & 4 — Sur-réservation + assertion
    expect(fn () => $service->reserver($timeSlot, $client2, '2026-09-15', 5000))
        ->toThrow(\Exception::class, 'Plus de places disponibles pour ce créneau.');

    // Vérification finale : une seule réservation en base, pas deux
    expect(\App\Models\Booking::count())->toBe(1);
});
