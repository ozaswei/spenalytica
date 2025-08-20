<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function homePage()
    {
        $userId = Auth::id();
        //get the categories
        $categories = Category::where('userId', $userId)->get();

        //expenses
        $expenses = Expense::where('userId', $userId)->get();

        //top 5 highest expenses
        $highestExpenses = Expense::where('userId', $userId)->orderBy('cost','desc')->take(5)->get();

        //incomes
        $incomes = Income::where('userId', $userId)->get();

        //getting total income for each months
        $monthlyRevenues = Income::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(revenue) as total_revenue')
        )
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        //dd($monthlyRevenues);

        //getting monthly expenses
        // Sum expense by month and year
        $monthlyExpenses = Expense::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(cost) as total_expense')
        )
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Merge incomes and expenses into a single collection with savings
        $monthlyDatas = collect();

        // Create a lookup for expenses by year and month for easy matching
        $expensesLookup = $monthlyExpenses->keyBy(function ($item) {
            return $item->year . '-' . $item->month;
        });

        foreach ($monthlyRevenues as $income) {
            $key = $income->year . '-' . $income->month;
            $expense = $expensesLookup->get($key);

            $totalExpense = $expense ? $expense->total_expense : 0;
            $savings = max(0, $income->total_revenue - $totalExpense);

            $monthlyDatas->push((object)[
                'year' => $income->year,
                'month' => $income->month,
                'income' => $income->total_revenue,
                'expense' => $totalExpense,
                'savings' => $savings,
            ]);
        }

        //dd($monthlyDatas);
        return view('spenalytica.homePage', compact('categories', 'expenses','highestExpenses' ,'incomes','monthlyDatas'));
    }
}
