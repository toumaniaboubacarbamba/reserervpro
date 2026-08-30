<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireStaleBookings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expirees = Booking::where('status', BookingStatus::Pending)
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($expirees as $booking) {
            $booking->status = BookingStatus::Cancelled;
            $booking->cancelled_by = 'system';
            $booking->refund_amount = 0; // rien n'a été payé, rien à rembourser
            $booking->save();
        }
    }
}
