<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'user' => $this->whenLoaded('user',function(){ 
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'user_id' => $this->user_id,
                    'user' => new UserResource($this->whenLoaded('user')),
                    'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
                ];
            })
        ];
    }
}
