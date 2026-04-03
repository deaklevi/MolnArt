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

    public function __construct(protected ScheduleService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScheduleResource::collection(Schedule::with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $schedule = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return new ScheduleResource($schedule);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $this->authorize('update',$schedule);
        
        $schedule = $this->service->update($schedule,$request->validated());
        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete',$schedule);
        $schedule->delete();
        return response()->noContent();
    }
}
