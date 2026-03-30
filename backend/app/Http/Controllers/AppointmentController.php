<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ReviewResource;
use App\Mail\AppointmentBooked;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AppointmentResource::collection(Appointment::with('customer')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    // app/Http/Controllers/Api/BookingController.php

    public function store(Request $request) 
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'phone_number' => 'required|string|max:20',
                'worker_id' => 'required|exists:users,id',
                'service' => 'required|string',
                'start' => 'required|date',
                'end' => 'required|date',
            ]);

            $start = \Carbon\Carbon::parse($validated['start']);
            $end = \Carbon\Carbon::parse($validated['end']);

            // Ütközésvizsgálat
            $exists = Appointment::where('appointment_from', '<', $end)
                                 ->where('appointment_to', '>', $start)
                                 ->exists();

            if ($exists) {
                return response()->json(['message' => 'Ez az időpont időközben foglalt lett.'], 422);
            }

            // Customer létrehozás tranzakcióbiztosan
            $customer = Customer::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone_number' => $validated['phone_number'],
                    'user_id' => $validated['worker_id']
                ]
            );

            $appointment = Appointment::create([
                'appointment_from' => $start,
                'appointment_to' => $end,
                'service' => $validated['service'],
                'customer_id' => $customer->id,
            ]);

            $worker = \App\Models\User::find($validated['worker_id']);

            $mailData = [
                'name'        => $customer->name,
                'service'     => $appointment->service,
                'start'       => $start->format('Y-m-d H:i'),
                'worker_name' => $worker ? $worker->user_name : 'Szakemberünk', // ITT VOLT A HIBA: user_name kell!
            ];

            // Email küldés - ha ez elszáll, a foglalás még megmarad!
            try {
                Mail::to($customer->email)->send(new \App\Mail\AppointmentBooked($mailData ));
            } catch (\Exception $mailEx) {
                Log::warning("Email küldési hiba: " . $mailEx->getMessage());
            }

            return response()->json(['message' => 'Sikeres foglalás!', 'data' => $appointment], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Súlyos foglalási hiba: " . $e->getMessage());
            return response()->json(['message' => 'Belső szerverhiba történt.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return new ReviewResource($appointment->load('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());
        return new AppointmentResource($appointment->load('customer'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
    
        // Opcionális: Ellenőrizd, hogy a bejelentkezett user törölheti-e
        // if ($appointment->customer->user_id !== auth()->id()) { return response(status: 403); }

        $appointment->delete();

        return response()->json(['message' => 'Időpont törölve']);
    }
}
