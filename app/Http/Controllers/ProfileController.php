<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
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
        $nowEom = $now->copy()->endOfMonth();

        // Short month labels (always available)
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // Base data (always collections, even if empty)
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::with('category')->where('userId', $userId)->get();
        $incomes    = Income::with('category')->where('userId', $userId)->get();

        // Budgets keyed by category
        $budgets = \App\Models\Budget::where('userId', $userId)->get()->keyBy('categoryId');
        foreach ($categories as $cat) {
            $cat->budget = optional($budgets->get($cat->id))->amount ?? 0;
        }

        // Highest expenses (up to 5)
        $highestExpenses = $expenses->sortByDesc('cost')->take(5);

        /**
         * Expand expenses into monthly buckets with subscription rules:
         * - subscription == 1: cost repeats monthly from created_at through current month
         * - subscription == 0 and updated_at > created_at: repeat until updated_at month
         * - otherwise: only created_at month
         */
        $expenseByKey = [];        // "YYYY-MM" => total
        $expenseByKeyCat = [];     // "YYYY-MM" => [categoryId => total]
        foreach ($expenses as $e) {
            $cost = (float) $e->cost;
            if ($cost <= 0) continue;

            $start = $e->created_at ? $e->created_at->copy()->startOfMonth() : $now->copy()->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            if ((int) $e->subscription === 1) {
                $end = $nowEom->copy();
            } elseif ($e->updated_at && $e->updated_at->gt($e->created_at)) {
                $end = $e->updated_at->copy()->endOfMonth();
            }

            if ($end->gt($nowEom)) $end = $nowEom->copy();
            if ($end->lt($start))  $end = $start->copy();

            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');

                $expenseByKey[$key] = ($expenseByKey[$key] ?? 0.0) + $cost;

                $cid = (int) ($e->categoryId ?? 0);
                if (!isset($expenseByKeyCat[$key])) $expenseByKeyCat[$key] = [];
                $expenseByKeyCat[$key][$cid] = ($expenseByKeyCat[$key][$cid] ?? 0.0) + $cost;

                $cursor->addMonth();
            }
        }

        // Income monthly aggregation (grouped in DB)
        $monthlyRevenues = Income::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(revenue) as total_revenue')
            ->where('userId', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $revenueByKey = [];
        foreach ($monthlyRevenues as $r) {
            $key = sprintf('%04d-%02d', (int) $r->year, (int) $r->month);
            $revenueByKey[$key] = (float) $r->total_revenue;
        }

        // Union of month keys
        $allKeys = collect(array_unique(array_merge(array_keys($revenueByKey), array_keys($expenseByKey))))
            ->sort()
            ->values();

        // Monthly data for charts
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
        $monthlyDatas = collect(array_values($monthlyMap)); // may be empty

        // Spending health (safe defaults)
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

        // Balances
        $currentBalance = (float) $incomes->sum('revenue') - (float) $expenses->sum('cost');

        // Robust average savings (null if no months)
        $avgSavings = $monthlyDatas->isNotEmpty()
            ? (float) $monthlyDatas->avg(fn($m) => ($m['income'] ?? 0) - ($m['expense'] ?? 0))
            : null;

        // Months until broke (always computable, but null if not negative trajectory)
        $robustAvg = function (array $values): ?float {
            $vals = array_values(array_filter($values, fn($v) => is_numeric($v)));
            $n = count($vals);
            if ($n === 0) return null;
            sort($vals);
            if ($n >= 3) {
                array_shift($vals);
                array_pop($vals);
            }
            return count($vals) ? array_sum($vals) / count($vals) : null;
        };

        $recurringIncome  = (float) $incomes->where('mrr', 1)->sum('revenue');
        $recurringExpense = (float) $expenses->where('subscription', 1)->sum('cost');

        $incomeSeries  = $monthlyDatas->pluck('income')->all();
        $expenseSeries = $monthlyDatas->pluck('expense')->all();
        $incomeAvg  = $robustAvg($incomeSeries)  ?? 0.0;
        $expenseAvg = $robustAvg($expenseSeries) ?? 0.0;

        $variableIncomeAvg  = max(0.0, $incomeAvg  - $recurringIncome);
        $variableExpenseAvg = max(0.0, $expenseAvg - $recurringExpense);

        $projectedMonthlySavings = ($recurringIncome - $recurringExpense) + ($variableIncomeAvg - $variableExpenseAvg);

        $epsilon = 1e-6;
        $monthsUntilBroke = ($projectedMonthlySavings < -$epsilon)
            ? max(0, (int) ceil($currentBalance / abs($projectedMonthlySavings)))
            : null;

        // 12-month series (current year), always numbers
        $currentYear = $now->year;
        $monthlyIncome = collect(range(1, 12))->map(function ($m) use ($currentYear, $revenueByKey) {
            $key = sprintf('%04d-%02d', $currentYear, $m);
            return (float) ($revenueByKey[$key] ?? 0.0);
        });
        $monthlyExpenses = collect(range(1, 12))->map(function ($m) use ($currentYear, $expenseByKey) {
            $key = sprintf('%04d-%02d', $currentYear, $m);
            return (float) ($expenseByKey[$key] ?? 0.0);
        });

        // Spent this month per category (for budgets & pie current month)
        $keyThisMonth = $now->format('Y-m');
        $spentByCategoryThisMonth = $expenseByKeyCat[$keyThisMonth] ?? [];

        // Build subscription-aware pie data:
        // Prefer current month (if any values > 0), else fall back to all-time totals.
        $categoryIds = $categories->pluck('id')->all();
        $catNamesById = $categories->pluck('category', 'id')->all();

        $currentMonthTotals = [];
        foreach ($categoryIds as $cid) {
            $currentMonthTotals[$cid] = (float) ($spentByCategoryThisMonth[$cid] ?? 0.0);
        }
        $hasCurrent = array_sum($currentMonthTotals) > 0;

        $allTimeTotals = array_fill_keys($categoryIds, 0.0);
        foreach ($expenseByKeyCat as $monthTotals) {
            foreach ($monthTotals as $cid => $v) {
                if (!array_key_exists($cid, $allTimeTotals)) $allTimeTotals[$cid] = 0.0;
                $allTimeTotals[$cid] += (float) $v;
            }
        }
        $hasAllTime = array_sum($allTimeTotals) > 0;

        $pieSource = $hasCurrent ? $currentMonthTotals : ($hasAllTime ? $allTimeTotals : []);
        $pieIsCurrentMonth = $hasCurrent;

        // Labels/data arrays (only non-zero slices to avoid a pie of zeros)
        $pieLabels = [];
        $pieData   = [];
        foreach ($pieSource as $cid => $val) {
            if ($val > 0 && isset($catNamesById[$cid])) {
                $pieLabels[] = $catNamesById[$cid];
                $pieData[]   = round((float) $val, 2);
            }
        }
        // If still empty, add a placeholder "No data"
        if (empty($pieLabels)) {
            $pieLabels = ['No data'];
            $pieData   = [1]; // tiny placeholder to render something
            $pieIsCurrentMonth = true;
        }

        $savingsGoal = session('savingsGoal', null);

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
            'spentByCategoryThisMonth',
            'pieLabels',
            'pieData',
            'pieIsCurrentMonth'
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

        $totalIncome    = (float) DB::table('incomes')->where('userId', $userId)->sum('revenue');
        $totalExpense   = (float) DB::table('expenses')->where('userId', $userId)->sum('cost');
        $currentBalance = $totalIncome - $totalExpense;

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

        $nonEmpty = array_filter($monthlySavings, fn($v) => is_numeric($v));
        $avgMonthlySavings = count($nonEmpty) ? array_sum($nonEmpty) / count($nonEmpty) : 0.0;

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
