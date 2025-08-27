<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'categoryId' => ['nullable','exists:categories,id'],
            'amount' => ['required','numeric','min:0.01'],
            'period' => ['required','in:monthly,weekly'],
            'start_date' => ['nullable','date'],
        ]);

        Budget::create([
            'userId' => Auth::id(),
            'categoryId' => $request->input('categoryId'),
            'amount' => $request->input('amount'),
            'period' => $request->input('period'),
            'start_date' => $request->input('start_date') ?: now()->toDateString(),
            'active' => true,
        ]);

        return Redirect::back()->with('status', 'budget-created');
    }

    public function update(Request $request, Budget $budget)
    {
        // $this->authorize('update', $budget); // optional gate if set

        $request->validate([
            'categoryId' => ['nullable','exists:categories,id'],
            'amount' => ['required','numeric','min:0.01'],
            'period' => ['required','in:monthly,weekly'],
            'start_date' => ['nullable','date'],
            'active' => ['nullable','boolean'],
        ]);

        $budget->update([
            'categoryId' => $request->input('categoryId'),
            'amount' => $request->input('amount'),
            'period' => $request->input('period'),
            'start_date' => $request->input('start_date') ?: $budget->start_date,
            'active' => (bool) $request->input('active', true),
        ]);

        return Redirect::back()->with('status', 'budget-updated');
    }

    public function destroy(Budget $budget)
    {
        // $this->authorize('delete', $budget); // optional
        $budget->delete();
        return Redirect::back()->with('status', 'budget-deleted');
    }
}
