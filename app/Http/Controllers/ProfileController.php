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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

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
        $now = now();

        // short month names for charts
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // data (eager-load to avoid N+1 in Blade)
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::with('category')->where('userId', $userId)->get();
        $incomes    = Income::with('category')->where('userId', $userId)->get();

        $budgets = \App\Models\Budget::where('userId', $userId)->get()->keyBy('categoryId');

        // attach category budgets
        foreach ($categories as $cat) {
            $cat->budget = optional($budgets->get($cat->id))->amount ?? 0;
        }

        // highest expenses
        $highestExpenses = $expenses->sortByDesc('cost')->take(5);

        // monthly sums
        $monthlyRevenues = Income::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(revenue) as total_revenue')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyExpensesAgg = Expense::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(cost) as total_expense')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // build monthly map with month field (needed by JS)
        $monthlyMap = [];

        foreach ($monthlyRevenues as $r) {
            $key = "{$r->year}-{$r->month}";
            $monthlyMap[$key] = [
                'year'    => (int) $r->year,
                'month'   => (int) $r->month,
                'income'  => (float) $r->total_revenue,
                'expense' => 0.0,
                'savings' => 0.0,
            ];
        }

        foreach ($monthlyExpensesAgg as $e) {
            $key = "{$e->year}-{$e->month}";
            if (!isset($monthlyMap[$key])) {
                $monthlyMap[$key] = [
                    'year'    => (int) $e->year,
                    'month'   => (int) $e->month,
                    'income'  => 0.0,
                    'expense' => (float) $e->total_expense,
                    'savings' => 0.0,
                ];
            } else {
                $monthlyMap[$key]['expense'] = (float) $e->total_expense;
            }
        }

        // compute savings
        foreach ($monthlyMap as $k => $v) {
            $monthlyMap[$k]['savings'] = $v['income'] - $v['expense'];
        }

        ksort($monthlyMap); // sort by key "Y-m"
        $monthlyDatas = collect(array_values($monthlyMap));

        // balances & health
        $currentBalance = $incomes->sum('revenue') - $expenses->sum('cost');

        $avgSavings = $monthlyDatas->isNotEmpty()
            ? (float) $monthlyDatas->avg(fn($m) => $m['income'] - $m['expense'])
            : null;

        $latestMonth = $monthlyDatas->last();
        $spendingHealth = 'Unknown';
        if ($latestMonth) {
            $incomeLatest  = max(0.0, (float) $latestMonth['income']);
            $expenseLatest = max(0.0, (float) $latestMonth['expense']);
            if ($incomeLatest <= 0.0 && $expenseLatest > 0.0) {
                $spendingHealth = 'Critical';
            } elseif ($expenseLatest === 0.0 && $incomeLatest === 0.0) {
                $spendingHealth = 'No activity';
            } else {
                $ratio = $expenseLatest / max(1.0, $incomeLatest);
                $spendingHealth = $ratio >= 1.0
                    ? 'Unhealthy'
                    : ($ratio >= 0.75
                        ? 'At Risk'
                        : ($ratio >= 0.5 ? 'Neutral' : 'Healthy'));
            }
        }

        $monthsUntilBroke = ($avgSavings !== null && $avgSavings < 0)
            ? max(0, (int) floor($currentBalance / abs($avgSavings)))
            : null;

        $savingsGoal = session('savingsGoal', null);

        // 12-month series for the "Income vs Expenses" chart
        $monthlyIncome = collect(range(1, 12))->map(
            fn($m) =>
            $incomes->whereBetween('created_at', [
                $now->copy()->month($m)->startOfMonth(),
                $now->copy()->month($m)->endOfMonth()
            ])->sum('revenue')
        );

        $monthlyExpenses = collect(range(1, 12))->map(
            fn($m) =>
            $expenses->whereBetween('created_at', [
                $now->copy()->month($m)->startOfMonth(),
                $now->copy()->month($m)->endOfMonth()
            ])->sum('cost')
        );

        return view('spenalytica.homePage', compact(
            'categories',
            'expenses',
            'highestExpenses',
            'incomes',
            'monthlyDatas',     // now contains 'month' and 'year'
            'spendingHealth',
            'monthsUntilBroke',
            'currentBalance',
            'avgSavings',
            'savingsGoal',
            'budgets',
            'monthlyIncome',    // sums 'revenue' correctly
            'monthlyExpenses',
            'months'            // short month names Jan..Dec
        ));
    }

    public function setSavingsGoal(Request $request)
    {
        $request->validate(['goal' => ['required', 'numeric', 'min:1']]);
        session(['savingsGoal' => (float) $request->input('goal')]);
        return Redirect::back()->with('status', 'savings-goal-set');
    }

    public function getForecastData()
    {
        $userId = Auth::id();

        // Current balance
        $totalIncome    = (float) DB::table('incomes')->where('userId', $userId)->sum('revenue');
        $totalExpense   = (float) DB::table('expenses')->where('userId', $userId)->sum('cost');
        $currentBalance = $totalIncome - $totalExpense;

        // Gather last up-to-6 months of (income - expense)
        $monthlySavings = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->copy()->subMonths($i)->startOfMonth();
            $end   = now()->copy()->subMonths($i)->endOfMonth();

            $income  = (float) DB::table('incomes')
                ->where('userId', $userId)
                ->whereBetween('created_at', [$start, $end])
                ->sum('revenue');

            $expense = (float) DB::table('expenses')
                ->where('userId', $userId)
                ->whereBetween('created_at', [$start, $end])
                ->sum('cost');

            $monthlySavings[] = $income - $expense;
        }

        // If there’s truly no history, average is 0
        $nonEmpty = array_filter($monthlySavings, fn($v) => $v !== null);
        $avgMonthlySavings = count($nonEmpty) ? array_sum($nonEmpty) / count($nonEmpty) : 0.0;

        // 6-month simple linear forecast
        $months = [];
        $forecast = [];
        for ($i = 1; $i <= 6; $i++) {
            $months[]   = now()->copy()->addMonths($i)->format('M Y');
            $forecast[] = $currentBalance + ($avgMonthlySavings * $i);
        }

        return response()->json([
            'months'            => $months,
            'forecast'          => $forecast,
            'currentBalance'    => $currentBalance,
            'avgMonthlySavings' => $avgMonthlySavings,
        ]);
    }
}
