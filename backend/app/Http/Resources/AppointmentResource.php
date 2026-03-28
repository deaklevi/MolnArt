<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
          'appointment_from' => $this->appointment_from,
          'appointment_to' => $this->appointment_to,
          'service' => $this->service,
          //'customer_id' => $this->customer_id,
          'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
