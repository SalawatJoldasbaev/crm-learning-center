<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\TeacherInGroup;
use App\Src\Response;

class ScheduleController extends Controller
{
    public function GetSchedule(Request $request)
    {
        $groups = Group::active()->orderBy('time_id', 'desc')->get();
        $Schedule = [];
        foreach ($groups as $group) {
            $tachers = [];
            foreach (TeacherInGroup::with('teacher')->where('group_id', $group->id)->get() as $teacher) {
                $tachers[] = [
                    'id' => $teacher->teacher_id,
                    'name' => $teacher->teacher?->name,
                ];
            }
            $temp = [
                'name' => $group->name,
                'days' => $group->days,
                'start_time' => $group->time->time,
                'end_time' => date('H:i', strtotime($group->time->time . '    + ' . $group->course->lesson_duration . ' minutes',)),
                'student_count' => $group->student_count,
                'group_start_date' => $group->group_start_date,
                'group_end_date' => $group->group_end_date,
                'room' => [
                    'id' => $group->room->id,
                    'name' => $group->room->name,
                ],
                'teachers' => $tachers,
            ];
            $Schedule[] = $temp;
        }
        $collect = collect($Schedule)->sortBy([
            ['end_time', 'asc']
        ])->values();
        return Response::success(data: $collect->toArray());
    }
}
