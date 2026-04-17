<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'date'   => $this->date->toDateString(),
            'start' => substr($this->start,0,5),
            'end'   => substr($this->end, 0, 5),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
