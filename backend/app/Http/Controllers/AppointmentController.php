<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ReviewResource;
use App\Models\Customer;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'name' => 'required|string|max:25',
            'email' => 'required|email|max:45',
            'phone_number' => 'required|string|max:25',
            'worker_id' => 'required|exists:users,id',
            'service' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);
    
        $start = \Carbon\Carbon::parse($validated['start']);
        $end = \Carbon\Carbon::parse($validated['end']);
    
        // --- EXTRA: Ütközés ellenőrzése ---
        $exists = Appointment::where('customer_id', '!=', 0) // Csak egy példa szűrés
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('appointment_from', [$start, $end])
                      ->orWhereBetween('appointment_to', [$start, $end]);
            })->exists();
    
        if ($exists) {
            return response()->json(['message' => 'Ez az időpont már foglalt!'], 422);
        }
        // ---------------------------------
    
        // 1. Customer kezelése
        $customer = Customer::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'user_id' => $validated['worker_id']
            ]
        );
    
        // 2. Foglalás mentése
        $appointment = Appointment::create([
            'appointment_from' => $start,
            'appointment_to' => $end,
            'service' => $validated['service'],
            'customer_id' => $customer->id,
        ]);
    
        return response()->json([
            'message' => 'Sikeres foglalás!', 
            'data' => $appointment
        ], 201);
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
