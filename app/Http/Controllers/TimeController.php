<?php

namespace App\Http\Controllers;

use App\Models\TimeCourse;
use App\Src\Response;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    public function ShowAllTime(Request $request)
    {
        $times = TimeCourse::orderBy('time')->get(['id', 'time'])->toArray();
        return Response::success(data: $times);
    }
}
