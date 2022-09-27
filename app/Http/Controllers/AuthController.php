<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Src\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $employee = Employee::where('phone', $request->phone)->first();
        if (!$employee or Hash::check($request->password, $employee->password)) {
            return Response::error('phone or password incorrect', code: 401);
        }
        return  $employee->createToken('token', 'employee');
    }
}
