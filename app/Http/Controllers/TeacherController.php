<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Branch;
use App\Models\Salary;
use App\Models\Teacher;
use App\Src\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function create(CreateTeacherRequest $request)
    {
        Teacher::create([
            'file_id' => $request->file_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'salary_percentage' => $request->salary_percentage,
            'gender' => $request->gender,
        ]);

        return Response::success();
    }

    public function ShowAllTeachers(Request $request)
    {
        $teachers = Teacher::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%" . $search . "%")
                ->orWhere('phone', 'like', "%" . $search . "%");
        })->when($request->branch_id, function ($query, $branch_id) {
            return $query->where('branch_id', $branch_id);
        })->paginate($request->per_page ?? 30);
        $branches = Branch::all();
        $final = [
            'per_page' => $teachers->perPage(),
            'last_page' => $teachers->lastPage(),
            'data' => []
        ];
        foreach ($teachers as $teacher) {
            $final['data'][] = [
                'id' => $teacher->id,
                'file' => null,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'gender' => $teacher->gender,
                'salary_percentage' => $teacher->salary_percentage,
            ];
        }
        return Response::success(data: $final);
    }

    public function selectableTeachers(Request $request)
    {
        $teachers = Teacher::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%" . $search . "%")
                ->orWhere('phone', 'like', "%" . $search . "%");
        })->when($request->branch_id, function ($query, $branch_id) {
            return $query->where('branch_id', $branch_id);
        })->get();
        $final = [];

        foreach ($teachers as $teacher) {
            $final[] = [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
            ];
        }
        return Response::success(data: $final);
    }

    public function UpdateTeacher(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'roles' => $request->roles,
            'gender' => $request->gender,
            'salary' => $request->salary,
            'file_id' => $request->file_id,
            'salary_percentage' => $request->salary_percentage,
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $teacher->update($data);
        return Response::success();
    }

    public function Salary(Request $request)
    {
        $from = $request->from ?? Carbon::today()->firstOfMonth()->format('Y-m-d');
        $to = $request->to ?? Carbon::today();
        $salaries = Salary::whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->with('teacher')
            ->get()
            ->groupBy('teacher_id');
        $final = [];

        foreach ($salaries as $salary) {
            $final[] = [
                'teacher_id' => $salary[0]->teacher_id,
                'teacher_name' => $salary[0]->teacher->name,
                'amount' => $salary->sum('amount'),
            ];
        }
        return Response::success(data: $final);
    }
}
