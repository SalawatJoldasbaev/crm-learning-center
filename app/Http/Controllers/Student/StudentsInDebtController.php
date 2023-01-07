<?php

namespace App\Http\Controllers\Student;

use App\Src\Response;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StudentsInDebtController extends Controller
{
    public function showAllDebtors(Request $request)
    {
        $students = Student::where('balance', '<', 0)->when($request->search, function ($query, $search) {
            $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        });
        $amount = clone $students;
        $amount = $amount->select(
            DB::raw('SUM(balance) as balance'),
        )->first();
        $students = $students->paginate($request->per_page ?? 30);
        $final = [
            'per_page' => $students->perPage(),
            'last_page' => $students->lastPage(),
            'data' => [
                'debt' => $amount->balance,
                'students' => [],
            ],
        ];
        foreach ($students as $student) {
            $final['data']['students'][] = [
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
}
