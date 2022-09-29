<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Src\Response;

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
}
