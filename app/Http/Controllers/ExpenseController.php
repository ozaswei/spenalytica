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
            'categoryId' => 'required',
            'subscription' => 'required',
            'cost' => 'required',
        ]);

        if (Expense::create([
            'expense' => $request->expense,
            'userId' => Auth::id(),
            'categoryId' => $request->categoryId,
            'subscription' => $request->subscription,
            'cost' => $request->cost,
            'description' => $request->edescription
        ])) {
            return redirect()->back()->with('success', 'Expense added successfully.');
        } else {
            return redirect()->back()->with('failed', 'Expense failed to add.');
        }
    }
}
