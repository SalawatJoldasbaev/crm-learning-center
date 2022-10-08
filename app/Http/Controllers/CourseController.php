<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseCreateRequest;
use App\Models\Course;
use App\Src\Response;

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
}
