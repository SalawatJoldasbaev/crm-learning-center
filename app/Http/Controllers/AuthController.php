<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\RegisterEmployeeRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Src\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $employee = Employee::where('phone', $request->phone)->first();
        $student = Student::where('phone', $request->phone)->first();
        $teacher = Teacher::where('phone', $request->phone)->first();
        $roles = array_merge([], $employee?->role ?? []);
        $role = $request->role;
        $data = false;
        if ($student) {
            $roles[] = 'student';
        }
        if ($teacher) {
            $roles[] = 'teacher';
        }
        if (count($roles) == 1 and is_null($role)) {
            if (in_array($roles[0], $employee?->role ?? [])) {
                $data = $this->createToken($roles[0], $employee, $request->password);
            } elseif ($roles[0] == 'student') {
                $data = $this->createToken($roles[0], $student, $request->password);
            } elseif ($roles[0] == 'teacher') {
                $data = $this->createToken($roles[0], $teacher, $request->password);
            }
        } elseif (count($roles) > 1 and is_null($role)) {
            return Response::error('role', [
                'roles' => $roles,
            ], 401);
        } elseif (!is_null($role)) {
            if (in_array($role, $employee?->role ?? [])) {
                $data = $this->createToken($role, $employee, $request->password);
            } elseif ($role == 'student') {
                $data = $this->createToken($role, $student, $request->password);
            } elseif ($role == 'teacher') {
                $data = $this->createToken($role, $teacher, $request->password);
            }
        }
        if ($data === false) {
            return Response::error('phone or password incorrect', code: 401);
        }
        return Response::success(data: $data);
    }
    private function createToken($role, $user, $password)
    {
        if (!Hash::check($password, $user->password)) {
            return false;
        }
        $token = $user->createToken('token', ['role:' . $role])->plainTextToken;
        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => $token,
            'role' => $role,
        ];
    }

    public function register(RegisterEmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $roles = $request->roles;
            if (in_array('teacher', $request->roles)) {
                $index = array_search('teacher', $roles);
                unset($roles[$index]);
                $roles = array_values($roles);
            }
            if (!empty($roles)) {
                $employee = Employee::where('phone', $request->phone)->first();
                if ($employee) {
                    return Response::error('phone already exists', code: 401);
                }
                $newEmployee = Employee::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'file_id' => $request->file_id,
                    'password' => Hash::make($request->password),
                    'role' => $roles,
                    'gender' => $request->gender,
                    'salary' => $request->salary,
                ]);
            }

            if (in_array('teacher', $request->roles)) {
                $employee = Teacher::where('phone', $request->phone)->first();
                if ($employee) {
                    return Response::error('phone already exists', code: 401);
                }
                Teacher::create([
                    'file_id' => $request->file_id,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'salary_percentage' => $request->salary_percentage,
                    'gender' => $request->gender,
                ]);
            }
        });
        return Response::success();
    }
}
