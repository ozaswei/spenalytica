<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
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

    public function homePage(Request $request)
    {
        $userId = Auth::id();
        $currentDate = \Carbon\Carbon::now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        //all months
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // --- load data
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::where('userId', $userId)->get();
        $incomes    = Income::where('userId', $userId)->get();

        // budgets
        $budgets = \App\Models\Budget::where('userId', $userId)->get();

        // Attach budget amount to category for easy Blade access
        foreach ($categories as $cat) {
            $catBudget = $budgets->firstWhere('categoryId', $cat->id);
            $cat->budget = $catBudget ? $catBudget->amount : 0;
        }

        // top 5 highest expenses
        $highestExpenses = $expenses->sortByDesc('cost')->take(5);

        // monthly revenues & expenses (basic, could expand for subscriptions)
        $monthlyRevenues = Income::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(revenue) as total_revenue')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyExpenses = Expense::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(cost) as total_expense')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // build monthlyMap
        $monthlyMap = [];
        foreach ($monthlyRevenues as $r) {
            $key = "{$r->year}-{$r->month}";
            $monthlyMap[$key] = ['income' => (float)$r->total_revenue, 'expense' => 0, 'savings' => 0];
        }
        foreach ($monthlyExpenses as $e) {
            $key = "{$e->year}-{$e->month}";
            $monthlyMap[$key]['expense'] = (float)$e->total_expense;
        }

        // compute savings
        foreach ($monthlyMap as $k => $v) {
            $monthlyMap[$k]['savings'] = $v['income'] - $v['expense'];
        }

        ksort($monthlyMap);
        $monthlyDatas = collect(array_values($monthlyMap));

        $currentBalance = $incomes->sum('revenue') - $expenses->sum('cost');

        $avgSavings = $monthlyDatas->count() > 0
            ? (float) collect($monthlyMap)->map(fn($m) => $m['income'] - $m['expense'])->avg()
            : 0;

        // spendingHealth
        $latestMonth = $monthlyDatas->last();
        $spendingHealth = 'Unknown';
        if ($latestMonth) {
            $incomeLatest = max(0, $latestMonth['income']);
            $expenseLatest = max(0, $latestMonth['expense']);
            if ($incomeLatest <= 0 && $expenseLatest > 0) $spendingHealth = 'Critical';
            elseif ($expenseLatest === 0 && $incomeLatest === 0) $spendingHealth = 'No activity';
            else {
                $ratio = $expenseLatest / max(1, $incomeLatest);
                $spendingHealth = $ratio >= 1 ? 'Unhealthy' : ($ratio >= 0.75 ? 'At Risk' : ($ratio >= 0.5 ? 'Neutral' : 'Healthy'));
            }
        }

        $monthsUntilBroke = ($avgSavings < 0) ? max(0, floor($currentBalance / abs($avgSavings))) : null;

        $savingsGoal = session('savingsGoal', null);



        $monthlyIncome = collect(range(1, 12))->map(
            fn($m) =>
            $incomes->whereBetween('created_at', [now()->month($m)->startOfMonth(), now()->month($m)->endOfMonth()])->sum('amount')
        );

        $monthlyExpenses = collect(range(1, 12))->map(
            fn($m) =>
            $expenses->whereBetween('created_at', [now()->month($m)->startOfMonth(), now()->month($m)->endOfMonth()])->sum('cost')
        );

        return view('spenalytica.homePage', compact(
            'categories',
            'expenses',
            'highestExpenses',
            'incomes',
            'monthlyDatas',
            'spendingHealth',
            'monthsUntilBroke',
            'currentBalance',
            'avgSavings',
            'savingsGoal',
            'budgets','monthlyIncome','monthlyExpenses','months'
        )); // ensure Budget tab stays active
    }





    /**
     * Save a savings goal to session (simple implementation).
     * POST route: /set-savings-goal
     */
    public function setSavingsGoal(Request $request)
    {
        $request->validate([
            'goal' => ['required', 'numeric', 'min:1']
        ]);

        session(['savingsGoal' => (float)$request->input('goal')]);

        return Redirect::back()->with('status', 'savings-goal-set');
    }

    public function getForecastData()
    {
        $userId = Auth::id();

        // Total income and expense for the last 6 months
        $totalIncome = DB::table('incomes')
            ->where('userId', $userId)
            ->sum('revenue');

        $totalExpense = DB::table('expenses')
            ->where('userId', $userId)
            ->sum('cost');

        $currentBalance = $totalIncome - $totalExpense;

        // Average monthly savings (income - expenses) over last 6 months
        $avgMonthlySavings = ($currentBalance / 6);

        $forecast = [];
        $months = [];

        for ($i = 1; $i <= 6; $i++) {
            $months[] = now()->addMonths($i)->format('M Y');
            $forecast[] = $currentBalance + ($avgMonthlySavings * $i);
        }

        return response()->json([
            'months' => $months,
            'forecast' => $forecast,
            'currentBalance' => $currentBalance,
            'avgMonthlySavings' => $avgMonthlySavings
        ]);
    }
}
