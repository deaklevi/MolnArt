<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * @param array $details Tartalmazza: name, service, start, worker_name
     */
    public function __construct($details)
    {
        // Az adatok átadása a publikus változónak, így a Blade-ben elérhető lesz
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('Emlékeztető: Holnap várunk!')
                    ->view('emails.appointment_reminder');
    }
}