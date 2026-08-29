<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_slot_id' => 'required|exists:time_slots,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'amount' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $timeSlot = TimeSlot::findOrFail($request->time_slot_id);

        try {
            $booking = $this->bookingService->reserver(
                $timeSlot,
                $request->user(),
                $request->booking_date,
                $request->amount,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($booking, 201);
    }

    public function myBookings(Request $request)
{
    $bookings = $request->user()->bookings()
        ->with('timeSlot.establishment')
        ->orderBy('booking_date', 'desc')
        ->get();

    return response()->json($bookings);
}

public function cancel(Request $request, Booking $booking)
{
    try {
        $booking = $this->bookingService->annuler($booking, $request->user());
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 422);
    }

    return response()->json([
        'message' => 'Réservation annulée avec succès.',
        'data' => $booking,
    ]);
}
}
