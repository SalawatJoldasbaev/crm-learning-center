<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\GroupCreateRequest;
use App\Models\Group;
use App\Models\TeacherInGroup;
use App\Src\Response;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function createGroup(GroupCreateRequest $request)
    {
        $group = Group::create([
            'name' => $request->name,
            'time_id' => $request->time_id,
            'group_start_date' => $request->group_start_date,
            'room_id' => $request->room_id,
            'days' => $request->days,
            'course_id' => $request->course_id,
        ]);
        foreach ($request->teacher_ids as $id) {
            TeacherInGroup::create([
                'group_id' => $group->id,
                'teacher_id' => $id,
            ]);
        }
        return Response::success();
    }

    public function ShowAllGroups(Request $request)
    {
        $groups = Group::paginate($request->per_page ?? 30);
        $final = [
            'per_page' => $groups->perPage(),
            'last_page' => $groups->lastPage(),
            'data' => [],
        ];
        foreach ($groups as $group) {
            $temps = TeacherInGroup::with('teacher')->where('group_id', $group->id)->get();
            $tachers = [];
            foreach ($temps as $temp) {
                $tachers[] = [
                    'id' => $temp->teacher_id,
                    'name' => $temp->teacher->name,
                ];
            }
            $final['data'][] = [
                'id' => $group->id,
                'course' => [
                    'id' => $group->course_id,
                    'name' => $group->course->name,
                ],
                'room' => [
                    'id' => $group->room_id,
                    'name' => $group->room->name,
                ],
                'days' => $group->days,
                'time' => [
                    'id' => $group->time_id,
                    'time' => $group->time->time,
                ],
                'tachers' => $tachers,
                'name' => $group->name,
                'group_start_date' => $group->group_start_date,
            ];
        }
        return Response::success(data:$final);
    }
}
