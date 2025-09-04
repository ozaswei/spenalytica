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
        // Allow 0 to CLEAR an existing budget; validate numbers cleanly
        $validated = $request->validate([
            'budgets'   => ['required', 'array'],
            'budgets.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $userId     = Auth::id();
        $period     = 'monthly';
        $start_date = now()->toDateString();

        // Existing budgets for this user (so we can clear when input is empty)
        $existing = Budget::where('userId', $userId)->pluck('id', 'categoryId');

        foreach (($validated['budgets'] ?? []) as $categoryId => $rawAmount) {
            // Normalize: empty string => null; otherwise cast to float
            $amount = ($rawAmount === '' || $rawAmount === null) ? null : (float) $rawAmount;

            if ($amount === null) {
                // No value submitted → delete existing budget for this category (clear it)
                if (isset($existing[$categoryId])) {
                    Budget::where('id', $existing[$categoryId])->delete();
                }
                continue;
            }

            // Save/update when >= 0.00
            Budget::updateOrCreate(
                ['userId' => $userId, 'categoryId' => $categoryId],
                [
                    'amount'     => $amount,
                    'period'     => $period,
                    'start_date' => $start_date,
                    'active'     => true,
                ]
            );
        }

        // ✅ Proper flash syntax + keep the Budgets tab selected
        return Redirect::back()
            ->with('success', 'Budgets saved.')
            ->with('activeTab', 'budget');
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'categoryId' => ['nullable', 'exists:categories,id'],
            'amount'     => ['required', 'numeric', 'min:0'],
            'period'     => ['required', 'in:monthly,weekly'],
            'start_date' => ['nullable', 'date'],
            'active'     => ['nullable', 'boolean'],
        ]);

        $budget->update([
            'categoryId' => $request->input('categoryId', $budget->categoryId),
            'amount'     => (float) $request->input('amount'),
            'period'     => $request->input('period'),
            'start_date' => $request->input('start_date') ?: $budget->start_date,
            'active'     => (bool) $request->input('active', true),
        ]);

        return Redirect::back()->with('status', 'budget-updated')->with('activeTab', 'budget');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return Redirect::back()->with('status', 'budget-deleted')->with('activeTab', 'budget');
    }
}
