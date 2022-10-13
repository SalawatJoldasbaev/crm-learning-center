<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\RoomCreateRequest;
use App\Models\Room;
use App\Src\Response;
use Illuminate\Http\Request;

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

    public function ShowAllRooms(Request $request)
    {
        $rooms = Room::all(['id', 'name', 'capacity']);
        return Response::success(data:$rooms->toArray());
    }
}
