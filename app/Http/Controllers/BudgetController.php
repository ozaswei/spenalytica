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
            'budgets' => ['required', 'array'],
            'budgets.*' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $period = 'monthly'; // default period
        $start_date = now()->toDateString();

        foreach ($request->input('budgets') as $categoryId => $amount) {
            if ($amount === null || $amount <= 0) continue;

            \App\Models\Budget::updateOrCreate(
                [
                    'userId' => Auth::id(),
                    'categoryId' => $categoryId,
                ],
                [
                    'amount' => $amount,
                    'period' => $period,
                    'start_date' => $start_date,
                    'active' => true,
                ]
            );
        }

        return redirect()->back()->with(['success', 'Budget Added Successfully.', 'activeTab' => 'budget']);
    }


    public function update(Request $request, Budget $budget)
    {
        // $this->authorize('update', $budget); // optional gate if set

        $request->validate([
            'categoryId' => ['nullable', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'period' => ['required', 'in:monthly,weekly'],
            'start_date' => ['nullable', 'date'],
            'active' => ['nullable', 'boolean'],
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
