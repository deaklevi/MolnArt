<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;

class ScheduleService
{
    public function hasOverlap(
        int $userId,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null
    ): bool {
        return Schedule::where('user_id', $userId)
            ->where('date', $date)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('start', '<', $end)
            ->where('end', '>', $start)
            ->exists();
    }

    public function create(User $user, array $data): Schedule
    {
        if ($this->hasOverlap(
            $user->id,
            $data['date'],
            $data['start'],
            $data['end']
        )) {
            throw new \InvalidArgumentException(
                'Ez az időpont ütközik egy meglévővel.'
            );
        }

        return Schedule::create([
            'user_id' => $user->id,
            'date'    => $data['date'],
            'start'   => $data['start'],
            'end'     => $data['end'],
        ]);
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        $date  = $data['date']  ?? $schedule->date->toDateString();
        $start = $data['start'] ?? $schedule->start;
        $end   = $data['end']   ?? $schedule->end;

        if ($this->hasOverlap(
            $schedule->user_id,
            $date,
            $start,
            $end,
            $schedule->id
        )) {
            throw new \InvalidArgumentException(
                'Ez az időpont ütközik egy meglévővel.'
            );
        }

        $schedule->update([
            'date'  => $date,
            'start' => $start,
            'end'   => $end,
        ]);

        return $schedule->fresh();
    }
}