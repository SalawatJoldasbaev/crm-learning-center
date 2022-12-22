<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Group;
use App\Src\Response;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\StudentInGroup;
use App\Http\Requests\SetAttendanceRequest;
use App\Models\Salary;
use App\Models\Student;
use App\Models\TeacherInGroup;

class AttendanceController extends Controller
{
    public function SetAttendance(SetAttendanceRequest $request)
    {
        $check = StudentInGroup::where('group_id', $request->group_id)->where('student_id', $request->student_id)->first();
        if (!$check) {
            return Response::Error('student id invalid', code: 400);
        }
        $group = Group::find($request->group_id);
        $course = $group->course;
        if ($request->status === true) {
            $teachers = TeacherInGroup::where('group_id', $request->group_id)->get();
            foreach ($teachers as $teacher) {
                $salary = Salary::where('teacher_id', $teacher->teacher_id)
                    ->where('date', $request->date)->first();
                $flex = ($teacher->flex / 100) * $course->price / $course->lessons_per_module;
                if ($salary) {
                    $salary->update([
                        'amount' => $salary->amount + $flex,
                    ]);
                } else {
                    Salary::create([
                        'teacher_id' => $teacher->teacher_id,
                        'date' => $request->date,
                        'amount' => $flex,
                    ]);
                }
            }
        }
        if ($group->next_lesson_date == $request->date) {
            $next_date = strtotime($request->date);
            while ($next_date <= strtotime($group->group_end_date)) {
                $next_date += 86400;
                if (in_array(date('N', $next_date), $group->days)) {
                    break;
                }
            }
            $group->next_lesson_date = date('Y-m-d', $next_date);
            $group->save();
        }
        Attendance::create([
            'group_id' => $request->group_id,
            'student_id' => $request->student_id,
            'date' => $request->date,
            'description' => $request->description,
            'status' => $request->status
        ]);
        return Response::success();
    }

    public function GetAttendance(Request $request, Group $group)
    {
        $from = strtotime($request?->from ?? 0);
        $to = strtotime($request?->to ?? 0);

        $startDate = strtotime($group->group_start_date);
        if ($group->group_end_date == null) {
            $endDate = strtotime($group->group_start_date . '+' . $group->course->month . ' months');
        } else {
            $endDate = strtotime($group->group_end_date);
        }
        if ($from < $startDate or $to > $endDate) {
            return Response::error('from and to invalid', code: 400);
        }
        $next_date = $from;
        $all_days = [];
        $final = [
            'days' => [],
            'students' => []
        ];
        while ($next_date <= $to) {
            if (in_array(date('N', $next_date), $group->days)) {
                $final['days'][] = [
                    'data' => date('Y-m-d', $next_date)
                ];
            }
            $next_date += 86400;
        }
        $students = StudentInGroup::where('group_id', $group->id)->get();
        $attendances = Attendance::whereDate('date', '>=', date('Y-m-d', $from))
            ->whereDate('date', '<=', date('Y-m-d', $to))
            ->where('group_id', $group->id)
            ->get(['student_id', 'date', 'status', 'description']);
        foreach ($students as $student) {
            $studentStartDate = $student->start_date;
            $student = Student::find($student->student_id);
            $final['students'][] = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'phone' => $student->phone,
                'birthday' => $student->birthday,
                'address' => $student->address,
                'gender' => $student->gender,
                'balance' => $student->balance ?? 0,
                'start_date' => $studentStartDate,
                'addition_phone' => $student->addition_phone,
                'attendance' => $attendances->where('student_id', $student->id)->values(),
            ];
        }
        return $final;
    }
}
