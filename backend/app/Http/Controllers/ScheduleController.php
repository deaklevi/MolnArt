<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function __construct(
        private ScheduleService $service
    ) {}

    public function index()
    {
        return ScheduleResource::collection(
            $this->service->getUserSchedule(Auth::id())
        );
    }

    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $this->authorize('create', Schedule::class);

        $schedule = $this->service->create(
            Auth::id(),
            $request->validated()
        );

        return new ScheduleResource($schedule);
    }

    public function show(Schedule $schedule): ScheduleResource
    {
        $this->authorize('view', $schedule);

        return new ScheduleResource($schedule->load('user'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): ScheduleResource
    {
        $this->authorize('update', $schedule);

        $schedule = $this->service->update($schedule, $request->validated());

        return new ScheduleResource($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $this->service->delete($schedule);

        return response()->noContent();
    }
}