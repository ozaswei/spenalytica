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
        $now = now()->endOfMonth(); // cap any recurrences at the current month

        // Short month names for charts
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // Data (eager-load to avoid N+1 in Blade)
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::with('category')->where('userId', $userId)->get();
        $incomes    = Income::with('category')->where('userId', $userId)->get();

        $budgets = \App\Models\Budget::where('userId', $userId)->get()->keyBy('categoryId');

        // Attach category budgets
        foreach ($categories as $cat) {
            $cat->budget = optional($budgets->get($cat->id))->amount ?? 0;
        }

        // Highest expenses (raw rows)
        $highestExpenses = $expenses->sortByDesc('cost')->take(5);

        /**
         * -----------------------------
         * Expense monthly aggregation with rules:
         *  - If subscription == 1: add the cost for EACH month from created_at (inclusive)
         *    through the current month (inclusive).
         *  - If subscription == 0 AND updated_at > created_at: add cost for EACH month
         *    from created_at (inclusive) through updated_at (inclusive).
         *  - Else (non-subscription, no span): count only the created_at month.
         *  - Guardrails:
         *      * Skip if cost <= 0
         *      * Never go beyond current month
         *      * If updated_at < created_at, treat as single month at created_at
         * -----------------------------
         */
        $expenseByKey = [];                    // "YYYY-MM" => total
        $expenseByKeyCat = [];                 // "YYYY-MM" => [categoryId => total]
        foreach ($expenses as $e) {
            $cost = (float) $e->cost;
            if ($cost <= 0) continue;

            $start = $e->created_at ? $e->created_at->copy()->startOfMonth() : now()->copy()->startOfMonth();
            $end   = $start->copy()->endOfMonth(); // default: single month

            if ((int)$e->subscription === 1) {
                $end = $now->copy(); // recur through current month
            } else {
                if ($e->updated_at && $e->updated_at->gt($e->created_at)) {
                    $end = $e->updated_at->copy()->endOfMonth(); // span between created and updated
                }
            }

            // never go past current month
            if ($end->gt($now)) $end = $now->copy();
            // if somehow end < start, collapse to start month
            if ($end->lt($start)) $end = $start->copy();

            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');
                // per-month total
                if (!isset($expenseByKey[$key])) $expenseByKey[$key] = 0.0;
                $expenseByKey[$key] += $cost;

                // per-month-per-category (for budget view)
                $cid = (int) $e->categoryId;
                if (!isset($expenseByKeyCat[$key])) $expenseByKeyCat[$key] = [];
                if (!isset($expenseByKeyCat[$key][$cid])) $expenseByKeyCat[$key][$cid] = 0.0;
                $expenseByKeyCat[$key][$cid] += $cost;

                $cursor->addMonth();
            }
        }

        // Income monthly aggregation (db group-by is fine)
        $monthlyRevenues = Income::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(revenue) as total_revenue')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Build revenue map "YYYY-MM" => total
        $revenueByKey = [];
        foreach ($monthlyRevenues as $r) {
            $key = sprintf('%04d-%02d', (int) $r->year, (int) $r->month);
            $revenueByKey[$key] = (float) $r->total_revenue;
        }

        // Union of all months present in revenue/expense
        $allKeys = collect(array_unique(array_merge(array_keys($revenueByKey), array_keys($expenseByKey))))
            ->sort()
            ->values();

        // Build monthlyDatas for charts
        $monthlyMap = [];
        foreach ($allKeys as $key) {
            [$year, $month] = explode('-', $key);
            $income  = $revenueByKey[$key] ?? 0.0;
            $expense = $expenseByKey[$key] ?? 0.0;
            $monthlyMap[$key] = [
                'year'    => (int) $year,
                'month'   => (int) $month,
                'income'  => (float) $income,
                'expense' => (float) $expense,
                'savings' => (float) ($income - $expense),
            ];
        }
        $monthlyDatas = collect(array_values($monthlyMap));

        // Balances & health
        $currentBalance = $incomes->sum('revenue') - ($expenseByKey[$now->format('Y-m')] ?? 0) - // current month expense from recurrence
            // plus past months (already in charts, but for net balance we include all historic):
            (collect($expenseByKey)->sum() - ($expenseByKey[$now->format('Y-m')] ?? 0));

        // Average monthly savings across observed months (or null if none)
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
                $spendingHealth = $ratio >= 1.0 ? 'Unhealthy' : ($ratio >= 0.75 ? 'At Risk' : ($ratio >= 0.5 ? 'Neutral' : 'Healthy'));
            }
        }

        $monthsUntilBroke = ($avgSavings !== null && $avgSavings < 0)
            ? max(0, (int) floor(($incomes->sum('revenue') - collect($expenseByKey)->sum()) / abs($avgSavings)))
            : null;

        $savingsGoal = session('savingsGoal', null);

        // 12-month series for the "Income vs Expenses" chart for current year
        $currentYear = now()->year;
        $monthlyIncome = collect(range(1, 12))->map(function ($m) use ($currentYear, $revenueByKey) {
            $key = sprintf('%04d-%02d', $currentYear, $m);
            return (float) ($revenueByKey[$key] ?? 0.0);
        });
        $monthlyExpenses = collect(range(1, 12))->map(function ($m) use ($currentYear, $expenseByKey) {
            $key = sprintf('%04d-%02d', $currentYear, $m);
            return (float) ($expenseByKey[$key] ?? 0.0);
        });

        // Spent this month per category (for the budget card)
        $keyThisMonth = now()->format('Y-m');
        $spentByCategoryThisMonth = $expenseByKeyCat[$keyThisMonth] ?? [];

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
            'budgets',
            'monthlyIncome',
            'monthlyExpenses',
            'months',
            'spentByCategoryThisMonth' // <- NEW
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
