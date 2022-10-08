<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentCreateRequest;
use App\Models\Student;
use App\Src\Response;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function createStudent(StudentCreateRequest $request)
    {
        $student = Student::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'branch_id' => $request->user()->branch_id,
            'addition_phone' => $request->addition_phone,
        ]);
        return Response::success();
    }
}
