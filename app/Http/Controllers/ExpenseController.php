<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\NewExpenseRequest;
use App\Http\Requests\FromToRequest;
use App\Models\Expense;
use App\Src\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function NewExpense(NewExpenseRequest $request)
    {
        DB::transaction(function () use ($request) {
            Expense::create([
                'name' => $request->name,
                'expense_category_id' => $request->expense_category_id,
                'payee' => $request->payee,
                'date' => $request->date,
                'amount' => $request->amount,
            ]);
        });
        return Response::success();
    }

    public function GetExpenses(FromToRequest $request)
    {
        $from = $request->from;
        $to = $request->to;

        $expenses = Expense::WhereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->paginate($request->per_page ?? 30);
        $final = [
            'last_page' => $expenses->lastPage(),
            'per_page' => $expenses->perPage(),
            'data' => [],
        ];
        foreach ($expenses as $expense) {
            $final['data'][] = [
                'id' => $expense->id,
                'expense_category' => [
                    'id' => $expense->expense_category_id,
                    'name' => $expense->category->name,
                ],
                'name' => $expense->name,
                'payee' => $expense->payee,
                'date' => $expense->date,
                'amount' => $expense->amount,
                'created_at' => $expense->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return Response::success(data: $final);
    }
}
