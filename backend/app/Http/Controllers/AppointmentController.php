<?php

// app/Http/Controllers/Api/AppointmentController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Unavailability;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $service) {}

    public function index()
    {
        $appointments = Appointment::with('customer','products')->where('user_id', Auth::id())->get();
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
    // Módosítás: Ne az Auth::id()-t, hanem a request-ből érkező user_id-t használd
    // A StoreAppointmentRequest-ben győződj meg róla, hogy a 'user_id' validálva van
    $userId = $request->input('user_id'); 
    
    $appointment = $this->service->book($request, $userId);

    return new AppointmentResource($appointment);
} 

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): AppointmentResource
    {
        abort_if($appointment->user_id !== Auth::id(), 403);

        // conflict check stays in controller — it's HTTP concern, not business logic
        $data = $request->validated();

        if (isset($data['appointment_from']) || isset($data['appointment_to'])) {
            $from = $data['appointment_from'] ?? $appointment->appointment_from;
            $to   = $data['appointment_to']   ?? $appointment->appointment_to;

            $conflict = Appointment::where('id', '!=', $appointment->id)
                ->where('user_id', Auth::id())
                ->where('appointment_from', '<', $to)
                ->where('appointment_to', '>', $from)
                ->exists();

            if ($conflict) abort(422, 'This time slot conflicts with another appointment.');
        }

        return new AppointmentResource(
            $this->service->update($request, $appointment)
        );
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        DB::transaction(function () use ($appointment) {
            foreach ($appointment->products as $product) {
                $product->increment('stock', $product->pivot->quantity);
            }
            $appointment->delete();
        });

        return response()->json(['message' => 'Appointment cancelled.']);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'date'     => 'required|date_format:Y-m-d',
            'duration' => 'required|integer|min:15|max:480',
        ]);

        $userId   = $request->integer('user_id');
        $date     = $request->string('date');
        $duration = $request->integer('duration');

        $schedule = Schedule::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if (!$schedule) {
            return response()->json(['slots' => []]);
        }

        $start   = Carbon::createFromFormat('H:i:s', $schedule->start);
        $end     = Carbon::createFromFormat('H:i:s', $schedule->end);
        $slots   = [];
        $current = $start->copy();

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotStart = $current->format('H:i');
            $slotEnd   = $current->copy()->addMinutes($duration)->format('H:i');

            $hasAppointment = Appointment::where('user_id', $userId)
                ->where('appointment_from', '<', "$date $slotEnd:00")
                ->where('appointment_to',   '>', "$date $slotStart:00")
                ->exists();

            $hasBreak = Unavailability::where('user_id', $userId)
                ->where('date', $date)
                ->where('start', '<', "$slotEnd:00")
                ->where('end',   '>', "$slotStart:00")
                ->exists();

            if (!$hasAppointment && !$hasBreak) {
                $slots[] = ['start' => $slotStart, 'end' => $slotEnd];
            }

            $current->addMinutes($duration);
        }

        return response()->json(['slots' => $slots]);
    }
}