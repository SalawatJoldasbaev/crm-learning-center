<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\RoomCreateRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
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
        $rooms = Room::select('id', 'name', 'capacity')->paginate($request->per_page ?? 30);
        $data = [
            'per_page' => $rooms->PerPage(),
            'last_page' => $rooms->LastPage(),
            'data' => [],
        ];
        foreach ($rooms as $room) {
            $data['data'][] = $room;
        }
        return Response::success(data: $data);
    }

    public function UpdateRoom(UpdateRoomRequest $request, Room $room)
    {
        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);
        return Response::success();
    }
}
