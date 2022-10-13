<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Models\Branch;
use App\Models\Teacher;
use App\Src\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function create(CreateTeacherRequest $request)
    {
        Teacher::create([
            'branch_id' => $request->branch_id,
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
        })->get();
        $branches = Branch::all();
        $final = [];
        foreach ($teachers as $teacher) {
            $final[] = [
                'id' => $teacher->id,
                'file' => null,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'gender' => $teacher->gender,
                'salary_percentage' => $teacher->salary_percentage,
                'branch' => [
                    'id' => $teacher->branch_id,
                    'name' => $branches->where('id', $teacher->branch_id)->first()->name,
                ],
            ];
        }
        return Response::success(data:$final);
    }
}
