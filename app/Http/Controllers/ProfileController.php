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
        $nowEom = now()->endOfMonth(); // cap recurrences at current month

        // Short month labels
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // Data (eager-load to avoid N+1 in Blade)
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::with('category')->where('userId', $userId)->get();
        $incomes    = Income::with('category')->where('userId', $userId)->get();

        // Budgets attached to categories
        $budgets = \App\Models\Budget::where('userId', $userId)->get()->keyBy('categoryId');
        foreach ($categories as $cat) {
            $cat->budget = optional($budgets->get($cat->id))->amount ?? 0;
        }

        // Highest expenses (raw rows)
        $highestExpenses = $expenses->sortByDesc('cost')->take(5);

        /**
         * Expense monthly aggregation with rules:
         *  - If subscription == 1: add cost each month from created_at (inclusive) through current month (inclusive).
         *  - If subscription == 0 AND updated_at > created_at: add cost each month from created_at through updated_at (inclusive).
         *  - Else: count only the created_at month.
         */
        $expenseByKey = [];        // "YYYY-MM" => total expense
        $expenseByKeyCat = [];     // "YYYY-MM" => [categoryId => total]
        foreach ($expenses as $e) {
            $cost = (float) $e->cost;
            if ($cost <= 0) continue;

            $start = $e->created_at ? $e->created_at->copy()->startOfMonth() : now()->copy()->startOfMonth();
            $end   = $start->copy()->endOfMonth(); // default: single month

            if ((int) $e->subscription === 1) {
                $end = $nowEom->copy(); // recur through current month
            } else {
                if ($e->updated_at && $e->updated_at->gt($e->created_at)) {
                    $end = $e->updated_at->copy()->endOfMonth(); // span between created and updated
                }
            }

            if ($end->gt($nowEom)) $end = $nowEom->copy();
            if ($end->lt($start))  $end = $start->copy();

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

        // Income monthly aggregation (DB group-by is fine)
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

        // Union of months present in revenue/expense
        $allKeys = collect(array_unique(array_merge(array_keys($revenueByKey), array_keys($expenseByKey))))
            ->sort()
            ->values();

        // monthlyDatas for charts
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

        // ✅ ADD THIS: compute $spendingHealth so it's defined for compact()
        $spendingHealth = 'Unknown';
        if ($monthlyDatas->isNotEmpty()) {
            $latest = $monthlyDatas->last();
            $incomeLatest  = max(0.0, (float) ($latest['income']  ?? 0.0));
            $expenseLatest = max(0.0, (float) ($latest['expense'] ?? 0.0));
            if ($incomeLatest <= 0.0 && $expenseLatest > 0.0) {
                $spendingHealth = 'Critical';
            } elseif ($expenseLatest === 0.0 && $incomeLatest === 0.0) {
                $spendingHealth = 'No activity';
            } else {
                $ratio = $expenseLatest / max(1.0, $incomeLatest);
                $spendingHealth = $ratio >= 1.0 ? 'Unhealthy'
                    : ($ratio >= 0.75 ? 'At Risk'
                        : ($ratio >= 0.5 ? 'Neutral' : 'Healthy'));
            }
        }

        // Current balance from actual records (not simulated expansion)
        $currentBalance = (float) $incomes->sum('revenue') - (float) $expenses->sum('cost');

        // --- Robust avg helper (trimmed mean) ---
        $robustAvg = function (array $values): ?float {
            $vals = array_values(array_filter($values, fn($v) => is_numeric($v)));
            $n = count($vals);
            if ($n === 0) return null;
            sort($vals);
            if ($n >= 3) {
                array_shift($vals);
                array_pop($vals);
            } // drop min & max
            return count($vals) ? array_sum($vals) / count($vals) : null;
        };

        // 1) Separate recurring components
        $recurringIncome  = (float) $incomes->where('mrr', 1)->sum('revenue');       // MRR-like income
        $recurringExpense = (float) $expenses->where('subscription', 1)->sum('cost'); // subscriptions

        // 2) Variable components (robust historical averages from monthlyDatas)
        $incomeSeries  = $monthlyDatas->pluck('income')->all();
        $expenseSeries = $monthlyDatas->pluck('expense')->all();

        $incomeAvg  = $robustAvg($incomeSeries)  ?? 0.0;
        $expenseAvg = $robustAvg($expenseSeries) ?? 0.0;

        // Variable portions (strip recurring from averages)
        $variableIncomeAvg  = max(0.0, $incomeAvg  - $recurringIncome);
        $variableExpenseAvg = max(0.0, $expenseAvg - $recurringExpense);

        // 3) Projected monthly savings going forward (recurring + variable)
        $projectedMonthlySavings = ($recurringIncome - $recurringExpense)
            + ($variableIncomeAvg - $variableExpenseAvg);

        // 4) Always-computable Months Until Broke
        $epsilon = 1e-6;
        if ($projectedMonthlySavings < -$epsilon) {
            $monthsUntilBroke = max(0, (int) ceil($currentBalance / abs($projectedMonthlySavings)));
        } else {
            $monthsUntilBroke = null; // healthy or flat trajectory
        }

        // Optional: still show the historic average savings
        $avgSavings = $monthlyDatas->isNotEmpty()
            ? (float) $monthlyDatas->avg(fn($m) => $m['income'] - $m['expense'])
            : null;

        // 12-month series for the "Income vs Expenses" chart (current year)
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

        $savingsGoal = session('savingsGoal', null);

        return view('spenalytica.homePage', compact(
            'categories',
            'expenses',
            'highestExpenses',
            'incomes',
            'monthlyDatas',
            'spendingHealth',        // ✅ now defined
            'monthsUntilBroke',
            'currentBalance',
            'avgSavings',
            'savingsGoal',
            'budgets',
            'monthlyIncome',
            'monthlyExpenses',
            'months',
            'spentByCategoryThisMonth'
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

        // Last up-to-6 months of (income - expense)
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
