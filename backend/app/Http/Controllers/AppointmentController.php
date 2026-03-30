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
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Validáció
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone_number' => 'nullable|string|max:20',
            'service' => 'required|string',
            'appointment_from' => 'required|date',
            'appointment_to' => 'required|date',
        ]);

        // 1. Foglalás adatainak frissítése
        $appointment->update([
            'service' => $validated['service'],
            'appointment_from' => $validated['appointment_from'],
            'appointment_to' => $validated['appointment_to'],
        ]);

        // 2. Kapcsolódó vendég (Customer) adatainak frissítése
        if ($appointment->customer) {
            $appointment->customer->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
            ]);
        }

        return response()->json([
            'message' => 'Időpont és vendég adatai sikeresen frissítve!',
            'data' => $appointment->load('customer')
        ]);
    }

    private function saveAppointment(Request $request, $id = null)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'nullable|email|max:100',
                'phone_number' => 'nullable|string|max:20',
                'service' => 'required|string',
                'appointment_from' => 'required',
                'appointment_to' => 'required',
                'worker_id' => 'nullable|exists:users,id' // Új foglaláshoz kellhet
            ]);
    
            $start = \Carbon\Carbon::parse($validated['appointment_from']);
            $end = \Carbon\Carbon::parse($validated['appointment_to']);
    
            // --- ÜTKÖZÉSVIZSGÁLAT ---
            $query = \App\Models\Appointment::where(function ($q) use ($start, $end) {
                $q->where('appointment_from', '<', $end)
                  ->where('appointment_to', '>', $start);
            });
    
            // Ha szerkesztünk, a saját ID-nkat ne vegye ütközésnek
            if ($id) {
                $query->where('id', '!=', $id);
            }
    
            if ($query->exists()) {
                return response()->json(['message' => 'Ez az időpont ütközik egy másik foglalással!'], 422);
            }
    
            // --- MENTÉS VAGY LÉTREHOZÁS ---
            if ($id) {
                $appointment = \App\Models\Appointment::findOrFail($id);
                $appointment->update([
                    'service' => $validated['service'],
                    'appointment_from' => $start,
                    'appointment_to' => $end,
                ]);
                $customer = $appointment->customer;
            } else {
                // Új vendég kezelése (vagy meglévő keresése email alapján)
                $customer = \App\Models\Customer::firstOrCreate(
                    ['email' => $validated['email']],
                    ['name' => $validated['name'], 'phone_number' => $validated['phone_number']]
                );
    
                $appointment = \App\Models\Appointment::create([
                    'service' => $validated['service'],
                    'appointment_from' => $start,
                    'appointment_to' => $end,
                    'customer_id' => $customer->id
                ]);
            }
    
            // Vendég adatok frissítése
            if ($customer) {
                $customer->update([
                    'name' => $validated['name'],
                    'phone_number' => $validated['phone_number'],
                ]);
            }
    
            return response()->json(['message' => 'Sikeres mentés!', 'data' => $appointment->load('customer')]);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
