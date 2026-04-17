<?php

namespace App\Services;

use App\Models\Schedule;

class ScheduleService
{
    public function getUserSchedule(int $userId)
    {
        return Schedule::where('user_id', $userId)
            ->orderBy('date')
            ->get();
    }

    public function isAvailable(int $userId, string $date, string $start, string $end): bool
    {
        return Schedule::where('user_id', $userId)
            ->where('date', $date)
            ->where('start', '<=', $start)
            ->where('end', '>=', $end)
            ->exists();
    }

    public function create(int $userId, array $data): Schedule
    {
        return Schedule::create([
            'user_id' => $userId,
            ...$data
        ]);
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);
        return $schedule;
    }

    public function delete(Schedule $schedule): void
    {
        $schedule->delete();
    }
}