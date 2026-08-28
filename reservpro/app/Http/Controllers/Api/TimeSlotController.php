<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request, Establishment $establishment)
{
    // 1. Validation de la date reçue en paramètre (?date=YYYY-MM-DD)
    $request->validate([
        'date' => 'required|date_format:Y-m-d|after_or_equal:today',
    ]);

    // 2. Conversion avec Carbon pour obtenir le jour de la semaine (0 = Dimanche, 6 = Samedi)
    $dateParam = $request->query('date');
    $carbonDate = Carbon::parse($dateParam);
    $dayOfWeek = $carbonDate->dayOfWeek;

    // 3. Récupération des créneaux de cet établissement pour ce jour de la semaine
    $timeSlots = $establishment->timeSlots()
        ->where('day_of_week', $dayOfWeek)
        ->get();

    // 4. Enrichissement : on ajoute places_restantes, calculé dynamiquement
    $result = $timeSlots->map(function ($timeSlot) use ($carbonDate) {
        return [
            'id' => $timeSlot->id,
            'start_time' => $timeSlot->start_time,
            'end_time' => $timeSlot->end_time,
            'capacity' => $timeSlot->capacity,
            'places_restantes' => $timeSlot->placesRestantes($carbonDate),
        ];
    });

    // 5. Réponse JSON
    return response()->json([
        'establishment' => $establishment->only('id', 'name', 'category'),
        'date' => $dateParam,
        'timeslots' => $result,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'establishment_id' => 'required|exists:establishments,id',
        'day_of_week' => 'required|integer|between:0,6',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'capacity' => 'required|integer|min:1',
    ]);

    $establishment = Establishment::findOrFail($validated['establishment_id']);

    $this->authorize('create', [TimeSlot::class, $establishment]);

    $timeSlot = TimeSlot::create($validated);

    return response()->json([
        'message' => 'Créneau horaire créé avec succès.',
        'data' => $timeSlot,
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeSlot $timeSlot)
{
    $this->authorize('update', $timeSlot);

    $validated = $request->validate([
        'day_of_week' => 'sometimes|integer|between:0,6',
        'start_time' => 'sometimes|date_format:H:i',
        'end_time' => 'sometimes|date_format:H:i|after:start_time',
        'capacity' => 'sometimes|integer|min:1',
    ]);

    $timeSlot->update($validated);

    return response()->json([
        'message' => 'Créneau horaire mis à jour avec succès.',
        'data' => $timeSlot,
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSlot $timeSlot)
{
    $this->authorize('delete', $timeSlot);

    $timeSlot->delete();

    return response()->json(['message' => 'Créneau horaire supprimé avec succès.']);
}
}
