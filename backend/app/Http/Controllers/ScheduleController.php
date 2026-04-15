<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Services\ScheduleService;
class ScheduleController extends Controller
{

    public function index(Request $request)
    {
        
        $query = Schedule::where('user_id', $request->user()->id);

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        return ScheduleResource::collection($query->orderBy('date')->get());
    }

    public function store(StoreScheduleRequest $request, ScheduleService $service): ScheduleResource
    {
        $this->authorize('create', Schedule::class);
        $schedule = $service->create($request->user(), $request->validated());

        return new ScheduleResource($schedule);
    }

    public function show(Schedule $schedule): ScheduleResource
    {
        return new ScheduleResource($schedule->load('user'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule, ScheduleService $service)
    {
        $this->authorize('update', $schedule);
        $schedule = $service->update($schedule, $request->validated());
        return new ScheduleResource($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);
        $schedule->delete();
        return response()->noContent();
    }
}
