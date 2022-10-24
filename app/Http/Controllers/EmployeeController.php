<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Branch;
use App\Models\Employee;
use App\Src\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function ShowAllEmployees(Request $request)
    {
        $employees = Employee::when($request->role, function ($query, $role) {
            return $query->whereJsonContains('role', $role);
        })->when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%" . $search . "%")
                ->orWhere('phone', 'like', "%" . $search . "%");
        })->when($request->branch_id, function ($query, $branch_id) {
            return $query->where('branch_id', $branch_id);
        })->get();
        $final = [];
        foreach ($employees as $employee) {
            $final[] = [
                'id' => $employee->id,
                'file' => null,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'role' => $employee->role,
                'role' => $employee->role,
                'gender' => $employee->gender,
                'salary' => $employee->salary,
            ];
        }
        return Response::success(data: $final);
    }

    public function UpdateEmployee(UpdateEmployeeRequest $request, Employee $employee)
    {
        $newRoles = $request->roles;

        foreach ($newRoles as $role) {
            if (in_array($role, ['teacher', 'student'])) {
                return Response::error('not updated role: teacher, student', code: 400);
            }
        }
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->roles,
            'gender' => $request->gender,
            'salary' => $request->salary,
            'file_id' => $request->file_id,
            'salary' => $request->salary,
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $employee->update($data);
        return Response::success();
    }
}
