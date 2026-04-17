<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreUnavailabilityRequest;
use App\Http\Requests\UpdateUnavailabilityRequest;
use App\Http\Resources\UnavailabilityResource;
use App\Models\Unavailability;
use App\Services\UnavailabilityService;
use Illuminate\Http\Request;

class UnavailabilityController extends Controller
{
    public function index(Request $request, UnavailabilityService $service)
    {
        return UnavailabilityResource::collection(
            $service->getUserUnavailability($request->user()->id)
        );
    }

    public function store(StoreUnavailabilityRequest $request, UnavailabilityService $service)
    {
        $data = $request->validated();

        $unavailability = $service->create($request->user(), $data);

        return new UnavailabilityResource($unavailability);
    }

    public function update(UpdateUnavailabilityRequest $request, Unavailability $unavailability, UnavailabilityService $service)
    {
        $this->authorize('update', $unavailability);

        $unavailability = $service->update($unavailability, $request->validated());

        return new UnavailabilityResource($unavailability);
    }

    public function destroy(Unavailability $unavailability, UnavailabilityService $service)
    {
        $this->authorize('delete', $unavailability);

        $service->delete($unavailability);

        return response()->noContent();
    }
}