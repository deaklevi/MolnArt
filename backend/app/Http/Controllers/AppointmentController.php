<?php

// app/Http/Controllers/Api/AppointmentController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $service) {}

    public function index()
    {
        $appointments = Appointment::with('customer', 'products')->get();
        return AppointmentResource::collection($appointments);  
    }

    public function show(Appointment $appointment): AppointmentResource
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        return new AppointmentResource(
            $appointment->load('customer', 'products')
        );
    }

    public function store(StoreAppointmentRequest $request): AppointmentResource
    {
        $appointment = $this->service->book($request);

        return new AppointmentResource($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): AppointmentResource
    {
        abort_if($appointment->user_id !== Auth::id(), 403);

        $data = $request->validated();

        if (isset($data['appointment_from']) || isset($data['appointment_to'])) {
            $from = $data['appointment_from'] ?? $appointment->appointment_from;
            $to   = $data['appointment_to']   ?? $appointment->appointment_to;

            $conflict = Appointment::where('id', '!=', $appointment->id)
                ->where('user_id', Auth::id())
                ->where('appointment_from', '<', $to)
                ->where('appointment_to', '>', $from)
                ->exists();

            if ($conflict) {
                abort(422, 'This time slot conflicts with another appointment.');
            }
        }

        $appointment->update(
            $request->only(['service', 'appointment_from', 'appointment_to'])
        );

        if ($appointment->customer) {
            $appointment->customer->update(
                $request->only(['name', 'email', 'phone_number'])
            );
        }

        return new AppointmentResource($appointment->load('customer', 'products'));
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        DB::transaction(function () use ($appointment) {
            foreach ($appointment->products as $product) {
                $product->increment('stock', $product->usage->quantity);
            }

            $appointment->delete();
        });

        return response()->json(['message' => 'Appointment cancelled.']);
    }
}