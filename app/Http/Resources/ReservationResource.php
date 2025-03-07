<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table_id' => $this->table_id,
            'reservation_date' => $this->reservation_date->toDateString(),
            'reservation_time' => $this->reservation_time->toTimeString(),
            'created_at' => $this->created_at,
        ];
    }
}
