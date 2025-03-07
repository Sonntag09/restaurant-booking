<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Services\ReservationService;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    private ReservationService $service;

    public function __construct(ReservationService $reservationService)
    {
        $this->service = $reservationService;
    }

    /**
     * Display a listing of the resource.
     * @param GetReservationRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(GetReservationRequest $request): AnonymousResourceCollection
    {
        return $this->service->getReservations($request->validated());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReservationRequest $request
     * @return JsonResponse
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->reserve($request->validated())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Reservation $reservation
     * @return JsonResponse
     */
    public function cancel(Reservation $reservation): JsonResponse
    {
        return response()->json(
            $this->service->cancel($reservation)
        );
    }
}
