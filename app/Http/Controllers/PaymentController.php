<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Src\Response;
use App\Models\Profit;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\TeacherInGroup;
use App\Http\Requests\FromToRequest;
use App\Http\Requests\Payment\NewPaymentRequest;
use App\Models\Group;
use App\Models\StudentInGroup;

class PaymentController extends Controller
{
    public function NewPayment(NewPaymentRequest $request)
    {
        $student = Student::find($request->student_id);
        $accept = ['cash', 'card', 'click', 'payme', 'bank', 'Money refunded'];
        if (!in_array($request->payment_type, $accept)) {
            return Response::error('payment type required with: ' . implode(',', $accept));
        }
        Payment::create([
            'student_id' => $request->student_id,
            'group_id' => $request->group_id,
            'date' => $request->date,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'description' => $request->description,
            'employee_id' => $request->user()->id
        ]);
        $student->balance += $request->amount;
        $student->save();
        $teachers = TeacherInGroup::where('group_id', $request->group_id)->get();
        $sum = $request->amount;
        $profit = 0;
        foreach ($teachers as $teacher) {
            $temp = ($teacher->flex / 100) * $sum;
            $sum -= $temp;
            $profit += $temp;
        }
        $checkProfit = Profit::where('date', Carbon::today())->first();
        if ($checkProfit) {
            $checkProfit->amount += $profit;
            $checkProfit->save();
        } else {
            Profit::create([
                'amount' => $profit,
                'date' => Carbon::today()
            ]);
        }
        return Response::success();
    }

    public function ShowPayments(FromToRequest $request)
    {
        $from = $request->from;
        $to = $request->to;
        $employee = $request->employee;
        $student = $request->student;

        $payments = Payment::WhereDate('created_at', '>=', $from)
            ->whereHas('employee', function ($query) use ($employee) {
                return $query->where('name', 'like', "%" . $employee . "%")
                    ->Orwhere('phone', 'like', "%" . $employee . "%");
            })
            ->whereHas('student', function ($query) use ($student) {
                return $query->where('first_name', 'like', "%" . $student . "%")
                    ->Orwhere('last_name', 'like', "%" . $student . "%")
                    ->Orwhere('phone', 'like', "%" . $student . "%");
            })
            ->when($request->payment_type, function ($query, $payment_type) {
                return $query->where('payment_type', $payment_type);
            })
            ->whereDate('created_at', '<=', $to)
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

    public function GetAmount(FromToRequest $request)
    {
        $from = $request->from;
        $to = $request->to;

        $payments = Payment::WhereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)->sum('amount');

        return Response::success(data: ['amount' => $payments]);
    }

    public function GetProfit(FromToRequest $request)
    {
        $from = $request->from;
        $to = $request->to;

        $payments = Profit::WhereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)->sum('amount');
        $expenses = Expense::WhereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)->sum('amount');
        return Response::success(data: ['amount' => $payments - $expenses]);
    }

    public function Expected(Request $request)
    {
        $groups = Group::where('active', true)->get();
        $data = [
            'amount' => 0,
            'profit' => 0,
        ];
        foreach ($groups as $group) {
            $amount = StudentInGroup::where('group_id', $group->id)
                ->where('active', true)
                ->sum('amount');
            $data['amount'] += $amount;
        }
        return Response::success(data: $data);
    }
}
