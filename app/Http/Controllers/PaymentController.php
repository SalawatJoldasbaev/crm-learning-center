<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\NewPaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Src\Response;
use Illuminate\Http\Request;

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
        return Response::success();
    }

    public function ShowPayments(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $payments = Payment::WhereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)->get();
        return Response::success(data: $payments->toArray());
    }

    public function GetAmount(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $payments = Payment::WhereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)->sum('amount');

        return Response::success(data: ['amount' => $payments]);
    }
}
