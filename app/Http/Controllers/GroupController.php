<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\GroupCreateRequest;
use App\Models\Group;
use App\Src\Response;

class GroupController extends Controller
{
    public function createGroup(GroupCreateRequest $request)
    {
        $group = Group::create([
            'name' => $request->name,
            'time_id' => $request->time_id,
            'group_start_date' => $request->group_start_date,
            'teacher_id' => $request->teacher_id,
            'room_id' => $request->room_id,
            'days' => $request->days,
            'course_id' => $request->course_id,
        ]);
        return Response::success();
    }
}
