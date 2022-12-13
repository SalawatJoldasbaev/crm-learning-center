<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Student;
use App\Models\StudentInGroup;
use App\Src\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LessonsCheckController extends Controller
{
    public function check()
    {
        $today = Carbon::today();
        $groups = Group::where('active', true)->where('next_lesson_date', $today)->get();
        foreach ($groups as $group) {
            $course = $group->course;
            $payment_date = collect($group->lessons)->where('date', $today->format('Y-m-d'))->first();
            if ($payment_date['is_payment_day'] == true) {
                $students = StudentInGroup::where('group_id', $group->id)->get();
                foreach ($students as $student) {
                    $student = $student->student;
                    $student->balance = -$course->price;
                    $student->save();
                }
            }
        }
        return Response::success();
    }
}
