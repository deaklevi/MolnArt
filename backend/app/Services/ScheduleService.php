<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;

class ScheduleService
{
    public function hasOverlap(
        int $userId,
        int $day,
        string $start,
        string $end,
        ?int $excludeId = null
    ): bool {
        return Schedule::where('user_id', $userId)
            ->where('day', $day)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('start', '<', $end)
            ->where('end', '>', $start)
            ->exists();
    }

    public function create(User $user, array $data): Schedule
    {
        if ($this->hasOverlap($user->id, $data['day'], $data['start'], $data['end'])) {
            throw new \InvalidArgumentException(
                'This time slot overlaps with an existing schedule.'
            );
        }

        return Schedule::create([
            'user_id' => $user->id,
            'day'     => $data['day'],
            'start'   => $data['start'],
            'end'     => $data['end'],
        ]);
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        $day   = $data['day']   ?? $schedule->day;
        $start = $data['start'] ?? $schedule->start;
        $end   = $data['end']   ?? $schedule->end;

        if ($this->hasOverlap($schedule->user_id, $day, $start, $end, $schedule->id)) {
            throw new \InvalidArgumentException(
                'This time slot overlaps with an existing schedule.'
            );
        }

        $schedule->update($data);

        return $schedule->fresh();
    }
}