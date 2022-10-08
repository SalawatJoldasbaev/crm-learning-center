<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\RoomCreateRequest;
use App\Models\Room;
use App\Src\Response;

class RoomController extends Controller
{
    public function createRoom(RoomCreateRequest $request)
    {
        $room = Room::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);
        return Response::success();
    }
}
