<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ReviewResource;

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
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = Appointment::create($request->validated());
        return new AppointmentResource($appointment->load(['customer']));
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
