<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\AddStudentToGroupRequest;
use App\Http\Requests\Group\GroupCreateRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentInGroup;
use App\Models\TeacherInGroup;
use App\Src\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function createGroup(GroupCreateRequest $request)
    {
        $days = [];
        foreach ($request->days as $day) {
            if ($day > 7) {
                return Response::error('unknown day', code: 400);
            }
            $days[] = $day;
        }
        $group = Group::where('room_id', $request->room_id)->where('time_id', $request->time_id)->first();
        if ($group) {
            return Response::error('Lesson chalk in the room', code: 400);
        }
        $course = Course::find($request->course_id);
        if (!$request->group_end_date) {
            $end_date = strtotime($request->group_start_date . '+' . $course->month . ' months');
        } else {
            $end_date = strtotime($request->group_end_date);
        }
        $next_date = strtotime($request->group_start_date);
        $first_lesson = 0;
        $lessons = [];
        $lesson = 0;
        while ($next_date <= $end_date) {
            if (in_array(date('N', $next_date), $request->days)) {
                if ($lesson == $course->lessons_per_module) {
                    $lesson = 1;
                    $is_payment_day = true;
                } else {
                    $lesson += 1;
                    $is_payment_day = false;
                }
                if ($first_lesson == 0) {
                    $first_lesson = $next_date;
                    $lessons[] = [
                        'lesson_the_month' => $lesson,
                        'date' => date('Y-m-d', $first_lesson),
                        'is_payment_day' => false,
                    ];
                } else {
                    $lessons[] = [
                        'lesson_the_month' => $lesson,
                        'date' => date('Y-m-d', $next_date),
                        'is_payment_day' => $is_payment_day,
                    ];
                }
            }
            $next_date += 86400;
        }
        $group = Group::create([
            'name' => $request->name,
            'time_id' => $request->time_id,
            'group_start_date' => $request->group_start_date,
            'group_end_date' => $request->group_end_date ?? date('Y-m-d', $end_date),
            'room_id' => $request->room_id,
            'days' => $days,
            'course_id' => $request->course_id,
            'next_lesson_date' => date('Y-m-d', $first_lesson),
            'lessons' => $lessons,
        ]);
        foreach ($request->teachers  as $teacher) {
            TeacherInGroup::create([
                'group_id' => $group->id,
                'teacher_id' => $teacher['teacher_id'],
                'flex' => $teacher['flex']
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
                    'name' => $temp->teacher?->name,
                    'flex' => $temp->flex
                ];
            }
            $lessons = collect($group->lessons)
                ->sortBy([
                    ['date', 'asc']
                ])
                ->where('is_payment_day', true)
                ->where('date', '>', Carbon::today())
                ->first();
            $final['data'][] = [
                'id' => $group->id,
                'course' => [
                    'id' => $group->course_id,
                    'name' => $group->course->name,
                    'lesson_duration' => $group->course->lesson_duration,
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
                'active' => $group->active,
                'student_count' => $group->student_count,
                'next_payment_date' => $lessons['date'],
                'group_start_date' => $group->group_start_date,
                'group_end_date' => $group->group_end_date,
            ];
        }
        return Response::success(data: $final);
    }

    public function UpdateGroup(UpdateGroupRequest $request, Group $group)
    {
        $group->update([
            'name' => $request->name,
            'time_id' => $request->time_id,
            'group_start_date' => $request->group_start_date,
            'group_end_date' => $request->group_end_date,
            'room_id' => $request->room_id,
            'days' => $request->days,
            'course_id' => $request->course_id,
        ]);
        $teachers = TeacherInGroup::where('group_id', $group->id)->get();
        foreach ($teachers as $teacher) {
            if (!in_array($teacher->teacher_id, $request->teacher_ids)) {
                $teacher->delete();
            }
        }
        foreach ($request->teacher_ids as $id) {
            if (!$teachers->where('teacher_id', $id)->first()) {
                TeacherInGroup::create([
                    'group_id' => $group->id,
                    'teacher_id' => $id,
                ]);
            }
        }
        return Response::success();
    }

    public function AddStudentToGroup(AddStudentToGroupRequest $request)
    {
        $check = StudentInGroup::where('group_id', $request->group_id)->where('student_id', $request->student_id)->first();
        if ($check) {
            return Response::error('student id already exists');
        }

        StudentInGroup::create([
            'amount' => $request->amount ?? Group::find($request->group_id)->course->price,
            'group_id' => $request->group_id,
            'student_id' => $request->student_id,
            'start_date' => $request->start_date,
        ]);

        return Response::success();
    }

    public function ActiveGroup(Request $request, Group $group)
    {
        $group->active = true;
        $group->save();
        return Response::success();
    }
}
