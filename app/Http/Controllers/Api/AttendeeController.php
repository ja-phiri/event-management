<?php

namespace App\Http\Controllers\Api;


use Illuminate\Routing\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    use AuthorizesRequests, CanLoadRelationships;
    private $relations = ['user'];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {

        $attendees = $this->loadRelationships(
            $event->attendees()
        );

        return AttendeeResource::collection(
            $attendees->latest()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => 1
        ]);

        return new AttendeeResource($this->loadRelationships($attendee));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {

        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {

        // Delete the attendee
        $attendee->delete();
        return response()->noContent();
    }
}
