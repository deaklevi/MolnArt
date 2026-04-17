<?php
namespace App\Services\Availability;

use App\Models\Schedule;
use App\Models\Unavailability;
use App\Models\Appointment;

class AvailabilityConflictService
{
    public function hasConflict(int $userId, string $date, string $start, string $end): bool
    {
        return
            $this->isOutsideSchedule($userId, $date, $start, $end) ||
            $this->hasAppointmentOverlap($userId, $date, $start, $end) ||
            $this->hasUnavailabilityOverlap($userId, $date, $start, $end);
    }

    /**
     * Check if requested slot is OUTSIDE working hours
     */
    private function isOutsideSchedule(int $userId, string $date, string $start, string $end): bool
    {
        return !Schedule::where('user_id', $userId)
            ->where('date', $date)
            ->where('start', '<=', $start)
            ->where('end', '>=', $end)
            ->exists();
    }

    /**
     * Check appointment overlap
     */
    private function hasAppointmentOverlap(int $userId, string $date, string $start, string $end): bool
    {
        return Appointment::where('user_id', $userId)
            ->where('appointment_from', '<', "$date $end")
            ->where('appointment_to', '>', "$date $start")
            ->exists();
    }

    /**
     * Check break / unavailability overlap
     */
    private function hasUnavailabilityOverlap(int $userId, string $date, string $start, string $end): bool
    {
        return Unavailability::where('user_id', $userId)
            ->where('date', $date)
            ->where('start', '<', $end)
            ->where('end', '>', $start)
            ->exists();
    }
}
