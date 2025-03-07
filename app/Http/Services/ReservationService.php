<?php

namespace App\Http\Services;

use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ReservationService
 * @package App\Http\Services
 */
class ReservationService
{
    /**
     * @param array $data
     * @return AnonymousResourceCollection
     */
    public function getReservations(array $data): AnonymousResourceCollection
    {
        $reservations = Reservation::where('reservation_date', $data['date'])
            ->with('table')
            ->orderBy('reservation_time')
            ->get();

        return ReservationResource::collection($reservations);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function reserve(array $data): mixed
    {
        $date = $data['reservation_date'];
        $time = $data['reservation_time'];
        $guestCount = $data['guest_count'];

        $reservedTables = Reservation::where('reservation_date', $date)
            ->pluck('table_id');

        $availableTables = Table::whereNotIn('id', $reservedTables)
            ->get();

        $tablesToReserve = collect();
        $remainingGuests = $guestCount;

        foreach ($availableTables as $table) {
            if ($remainingGuests <= 0) {
                break;
            }

            $tablesToReserve->push($table);
            $remainingGuests -= $table->seating_capacity;
        }

        if ($remainingGuests > 0) {
            return ['message' => 'We are sorry, but there are not enough available tables for that date.'];
        }

        DB::beginTransaction();

        try {
            $reservations = [];

            foreach ($tablesToReserve as $table) {
                $reservation = Reservation::create([
                    'table_id' => $table->id,
                    'reservation_date' => $date,
                    'reservation_time' => $time,
                ]);

                $reservations[] = $reservation;
            }

            DB::commit();
        } catch (Throwable $e) {
            if (DB::transactionLevel()) {
                DB::rollBack();
            }

            throw $e;
        }

        return ReservationResource::collection($reservations);
    }

    /**
     * @param Reservation $reservation
     * @return array
     */
    public function cancel(Reservation $reservation): array
    {
        $reservationTime = Carbon::parse($reservation->reservation_date->format('Y-d-m') . ' ' . $reservation->reservation_time->format('H:i:s'));
        $currentTime = Carbon::now();

        if ($currentTime->diffInHours($reservationTime) < 2) {
            return ['message' => 'You can only cancel a reservation at least 2 hours before the reserved time.'];
        }

        $reservation->delete();

        return ['message' => 'Reservation canceled successfully.'];
    }
}
