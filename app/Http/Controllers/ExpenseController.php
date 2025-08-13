<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function addExpense(Request $request)
    {
        $request->validate([
            'expense' => ['required', 'unique:' . Expense::class],
            'ecategoryId' => 'required',
            'subscription' => 'required',
            'cost' => 'required',
        ]);

        if (Expense::create([
            'expense' => $request->expense,
            'userId' => Auth::id(),
            'categoryId' => $request->ecategoryId,
            'subscription' => $request->subscription,
            'cost' => $request->cost,
            'description' => $request->edescription
        ])) {
            return redirect()->back()->with('success', 'Expense added successfully.')->with('activeTab', 'addExpense');
        } else {
            return redirect()->back()->with('failed', 'Expense failed to add.')->with('activeTab', 'addExpense');
        }
    }

    public function editExpense(Request $request)
    {
        $request->validate([
            'expense' => ['required', 'unique:' . Expense::class],
            'ecategoryId' => 'required',
            'subscription' => 'required',
            'cost' => 'required',
        ]);

        $expense = Expense::findOrFail($request->expenseId);
        $expense->expense = $request->expense;
        $expense->categoryId = $request->ecategoryId;
        $expense->subscription = $request->subscription;
        $expense->cost = $request->cost;
        $expense->description = $request->edescription;
        if ($expense->save()) {
            return redirect()->back()
                ->with('success', 'Expense data updated successfully')
                ->with('activeTab', 'addExpense');
        } else {
            return redirect()->back()
                ->with('failed', 'Expense data update failed')
                ->with('activeTab', 'addExpense');
        }
    }
    public function deleteExpense(Request $request)
    {
        $expense = Expense::find($request->expenseId);
        if ($expense->delete()) {
            return redirect()->back()
                ->with('success', 'Expense data deleted successfully')
                ->with('activeTab', 'addExpense');
        } else {
            return redirect()->back()
                ->with('failed', 'Expense data deletion failed')
                ->with('activeTab', 'addExpense');
        }
    }
}
