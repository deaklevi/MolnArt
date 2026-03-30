<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Emlékeztető email küldése a holnapi időpontokról';

    public function handle()
    {
        // Keressük ki a holnapi napot
        $tomorrow = Carbon::tomorrow();

        // Lekérjük azokat a foglalásokat, amik holnap kezdődnek
        $appointments = Appointment::with('customer')
            ->whereDate('appointment_from', $tomorrow)
            ->get();

        foreach ($appointments as $appointment) {
            $customer = $appointment->customer;

            if ($customer && $customer->email) {
                $mailData = [
                    'name' => $customer->name,
                    'service' => $appointment->service,
                    'start' => Carbon::parse($appointment->appointment_from)->format('H:i'),
                    'worker_name' => $customer->user->user_name ?? 'Szakemberünk',
                ];

                Mail::to($customer->email)->send(new AppointmentReminder($mailData));
            }
        }

        $this->info(count($appointments) . ' emlékeztető kiküldve.');
    }
}