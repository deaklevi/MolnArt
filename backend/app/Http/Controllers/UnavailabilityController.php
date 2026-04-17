<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreUnavailabilityRequest;
use App\Http\Requests\UpdateUnavailabilityRequest;
use App\Http\Resources\UnavailabilityResource;
use App\Models\Unavailability;
use App\Services\UnavailabilityService;
use Illuminate\Support\Facades\Auth;

class UnavailabilityController extends Controller
{
    public function __construct(
        private UnavailabilityService $service
    ) {}

    public function index()
    {
        return UnavailabilityResource::collection(
            $this->service->getUserUnavailability(Auth::id())
        );
    }

    public function store(StoreUnavailabilityRequest $request)
    {
        $unavailability = $this->service->create(
            Auth::user(),
            $request->validated()
        );

        return new UnavailabilityResource($unavailability);
    }

    public function update(UpdateUnavailabilityRequest $request, Unavailability $unavailability)
    {
        abort_if($unavailability->user_id !== Auth::id(), 403);

        $unavailability = $this->service->update(
            $unavailability,
            $request->validated()
        );

        return new UnavailabilityResource($unavailability);
    }

    public function destroy(Unavailability $unavailability)
    {
        abort_if($unavailability->user_id !== Auth::id(), 403);

        $this->service->delete($unavailability);

        return response()->noContent();
    }
}