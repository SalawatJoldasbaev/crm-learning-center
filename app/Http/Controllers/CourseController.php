<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseCreateRequest;
use App\Models\Course;
use App\Src\Response;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function createCourse(CourseCreateRequest $request)
    {
        $course = Course::create([
            'name' => $request->name,
            'file_id' => $request->file_id,
            'description' => $request->description,
            'lesson_duration' => $request->lesson_duration,
            'month' => $request->month,
            'price' => $request->price,
        ]);

        return Response::success();
    }

    public function ShowAllCourses(Request $request)
    {
        $courses = Course::all();
        $final = [];
        foreach ($courses as $course) {
            $final[] = [
                'id' => $course->id,
                'file' => null,
                'name' => $course->name,
                'description' => $course->description,
                'month' => $course->month,
                'price' => $course->price,
            ];
        }
        return Response::success(data:$final);
    }
}
