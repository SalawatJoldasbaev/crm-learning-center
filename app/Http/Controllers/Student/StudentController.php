<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentCreateRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use App\Models\StudentInGroup;
use App\Src\Response;
use Illuminate\Http\Request;
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
            'addition_phone' => $request->addition_phone,
        ]);
        return Response::success();
    }

    public function ShowAllStudents(Request $request)
    {
        $students = Student::when($request->search, function ($query, $search) {
            $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->paginate($request->per_page ?? 30);

        $final = [
            'per_page' => $students->perPage(),
            'last_page' => $students->lastPage(),
            'data' => [],
        ];
        foreach ($students as $student) {
            $final['data'][] = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'phone' => $student->phone,
                'address' => $student->address,
                'birthday' => $student->birthday,
                'gender' => $student->gender,
                'balance' => $student->balance,
                'addition_phone' => $student->addition_phone,
            ];
        }
        return Response::success(data: $final);
    }

    public function selectableStudents(Request $request)
    {
        $students = Student::when($request->search, function ($query, $search) {
            $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })->get();

        $final = [];
        foreach ($students as $student) {
            $final[] = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'phone' => $student->phone,
                'balance' => $student->balance,
            ];
        }
        return Response::success(data: $final);
    }

    public function UpdateStudent(UpdateStudentRequest $request, Student $student)
    {
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'addition_phone' => $request->addition_phone,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $student->update($data);
        return Response::success();
    }

    public function StudentGroups(Request $request, Student $student)
    {
        $groups = StudentInGroup::where('student_id', $student->id)->get();
        $final = [];
        foreach ($groups as $group) {
            $final[] = [
                'id' => $group->group_id,
                'name' => $group->group->name,
                'active' => $group->active
            ];
        }
        return Response::success(data: $final);
    }
}
