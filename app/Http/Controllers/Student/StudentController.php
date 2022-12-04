<?php

namespace App\Http\Controllers\Student;

use App\Src\Response;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentInGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Student\StudentCreateRequest;
use App\Http\Requests\Student\UpdateStudentRequest;

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

    public function payments(Request $request, Student $student)
    {
        $payments = Payment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 150);

        $final = [
            'last_page' => $payments->lastPage(),
            'per_page' => $payments->perPage(),
            'data' => []
        ];

        foreach ($payments as $payment) {
            $final['data'][] = [
                'id' => $payment->id,
                'employee' => [
                    'id' => $payment->employee->id,
                    'name' => $payment->employee->name,
                    'phone' => $payment->employee->phone,
                ],
                'student' => [
                    'id' => $payment->student->id,
                    'first_name' => $payment->student->first_name,
                    'last_name' => $payment->student->last_name,
                    'phone' => $payment->student->phone,
                ],
                'group' => [
                    'id' => $payment->group->id,
                    'name' => $payment->group->name,
                    'active' => $payment->group->active
                ],
                'amount' => $payment->amount,
                'payment_type' => $payment->payment_type,
                'date' => $payment->date,
                'description' => $payment->description,
                'created_at' => $payment->created_at,
            ];
        }
        return Response::success(data: $final);
    }
}
