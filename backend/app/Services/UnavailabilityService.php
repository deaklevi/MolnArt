<?php
namespace App\Services;

use App\Models\Unavailability;
use App\Models\User;

class UnavailabilityService
{
    public function getUserUnavailability(int $userId)
    {
        return Unavailability::where('user_id', $userId)
            ->orderBy('date')
            ->get();
    }

    public function create(User $user, array $data): Unavailability
    {
        return $user->unavailabilities()->create($data);
    }

    public function update(Unavailability $unavailability, array $data): Unavailability
    {
        $unavailability->update($data);
        return $unavailability;
    }

    public function delete(Unavailability $unavailability): void
    {
        $unavailability->delete();
    }
}