<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'user_name' => $this->user_name,
          'description' => $this->description,
          'profile_image' => $this->profile_image,
          'services' => ServiceResource::collection($this->whenLoaded('services')),
          'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
          'customers' => CustomerResource::collection($this->whenLoaded('customers')),
        ];
    }
}
