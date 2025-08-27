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

        // --- load data
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::where('userId', $userId)->get();
        $incomes    = Income::where('userId', $userId)->get();

        // budgets
        $budgets = \App\Models\Budget::where('userId', $userId)->get();

        // top 5 highest expenses
        $highestExpenses = Expense::where('userId', $userId)->orderBy('cost', 'desc')->take(5)->get();

        // monthly revenues (as before)
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

        // monthly expenses (original raw sums - we'll also compute expanded subscription contribution below)
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

        // --- Build monthlyMap (similar to before)
        $monthlyMap = [];
        foreach ($monthlyRevenues as $r) {
            $key = "{$r->year}-{$r->month}";
            $monthlyMap[$key] = [
                'year' => (int)$r->year,
                'month' => (int)$r->month,
                'income' => (float)$r->total_revenue,
                'expense' => 0.0,
                'savings' => 0.0,
            ];
        }
        foreach ($monthlyExpenses as $e) {
            $key = "{$e->year}-{$e->month}";
            if (!isset($monthlyMap[$key])) {
                $monthlyMap[$key] = [
                    'year' => (int)$e->year,
                    'month' => (int)$e->month,
                    'income' => 0.0,
                    'expense' => (float)$e->total_expense,
                    'savings' => 0.0,
                ];
            } else {
                $monthlyMap[$key]['expense'] = (float)$e->total_expense;
            }
        }

        // -------------------------------
        // Expand subscription expenses across months:
        // - If $expense->subscription == true and created_at == updated_at:
        //      => treat it as recurring starting from created_at and continuing through current month (monthly)
        // - If subscription == false and created_at != updated_at:
        //      => treat it as one-off appearing between created_at and updated_at inclusive (repeat for each month)
        // Additional safe guards: don't add future months, only add while budget active, etc.
        // -------------------------------

        // We'll aggregate subscription contributions per year-month to add into monthlyMap
        $subscriptionContrib = [];

        foreach ($expenses as $exp) {
            // parse created/updated as Carbon
            $created = \Carbon\Carbon::parse($exp->created_at)->startOfDay();
            $updated = \Carbon\Carbon::parse($exp->updated_at)->startOfDay();
            $isSubscription = (bool)$exp->subscription;
            $cost = (float)$exp->cost;

            if ($isSubscription && $created->eq($updated)) {
                // recurring monthly from created -> current month (inclusive)
                $start = $created->copy()->startOfMonth();
                $end = $currentDate->copy()->endOfMonth();
                // loop month by month
                while ($start->lte($end)) {
                    $y = $start->year;
                    $m = $start->month;
                    $key = "{$y}-{$m}";
                    $subscriptionContrib[$key] = ($subscriptionContrib[$key] ?? 0) + $cost;
                    $start->addMonth();
                }
            } elseif (!$isSubscription && !$created->eq($updated)) {
                // occurred between created and updated -> add once per month between those dates inclusive
                $start = $created->copy()->startOfMonth();
                $end = $updated->copy()->startOfMonth();
                // only add if start <= end
                while ($start->lte($end)) {
                    $y = $start->year;
                    $m = $start->month;
                    $key = "{$y}-{$m}";
                    $subscriptionContrib[$key] = ($subscriptionContrib[$key] ?? 0) + $cost;
                    $start->addMonth();
                }
            } else {
                // default: single month (created month) if none of the above conditions match
                $key = $created->year . '-' . $created->month;
                $subscriptionContrib[$key] = ($subscriptionContrib[$key] ?? 0) + $cost;
            }
        }

        // add subscriptionContrib into monthlyMap->expense (merging with already-aggregated DB sums)
        foreach ($subscriptionContrib as $key => $amount) {
            if (!isset($monthlyMap[$key])) {
                // if we don't have that month entry create it with zero income
                [$y, $m] = explode('-', $key);
                $monthlyMap[$key] = [
                    'year' => (int)$y,
                    'month' => (int)$m,
                    'income' => 0.0,
                    'expense' => (float)$amount,
                    'savings' => 0.0,
                ];
            } else {
                // increment expense
                $monthlyMap[$key]['expense'] = (float)$monthlyMap[$key]['expense'] + (float)$amount;
            }
        }

        // compute savings
        foreach ($monthlyMap as $k => $v) {
            $monthlyMap[$k]['savings'] = $v['income'] - $v['expense'];
        }

        ksort($monthlyMap);
        $monthlyDatas = collect(array_values($monthlyMap));

        // Recompute aggregates
        $totalIncome = $incomes->sum('revenue');
        $totalExpense = 0;
        // For total expense we can't simply sum expenses table because subscriptions expanded;
        // Instead sum latest monthlyMap expenses (or fallback)
        foreach ($monthlyDatas as $m) {
            $totalExpense += (float)$m['expense'];
        }

        $currentBalance = $totalIncome - $totalExpense;

        // avgSavings as signed monthly average
        $avgSavings = 0.0;
        if ($monthlyDatas->count() > 0) {
            $signedMonthly = collect($monthlyMap)->map(function ($m) {
                return ($m['income'] - $m['expense']);
            })->values();
            $avgSavings = (float)$signedMonthly->avg();
        }

        // spendingHealth calculation (latest month)
        $spendingHealth = 'Unknown';
        $latestMonth = $monthlyDatas->last();
        if ($latestMonth) {
            $incomeLatest = max(0, (float)$latestMonth['income']);
            $expenseLatest = max(0, (float)$latestMonth['expense']);
            if ($incomeLatest <= 0 && $expenseLatest > 0) {
                $spendingHealth = 'Critical';
            } elseif ($expenseLatest === 0 && $incomeLatest === 0) {
                $spendingHealth = 'No activity';
            } else {
                $ratio = $expenseLatest / max(1, $incomeLatest);
                if ($ratio >= 1.0) $spendingHealth = 'Unhealthy';
                elseif ($ratio >= 0.75) $spendingHealth = 'At Risk';
                elseif ($ratio >= 0.5) $spendingHealth = 'Neutral';
                else $spendingHealth = 'Healthy';
            }
        }

        // months until broke
        $monthsUntilBroke = null;
        if ($avgSavings < 0) {
            if ($currentBalance <= 0) $monthsUntilBroke = 0;
            else $monthsUntilBroke = max(0, floor($currentBalance / abs($avgSavings)));
        }

        // Compute per-budget usage for the current month (or the budget's period)
        $budgetUsages = [];
        foreach ($budgets as $b) {
            // determine which month to check: current month (for monthly budgets)
            $checkYear = $currentYear;
            $checkMonth = $currentMonth;
            // sum expenses in that category and month from our monthlyMap
            $key = "{$checkYear}-{$checkMonth}";
            $catTotal = 0.0;
            if (isset($monthlyMap[$key])) {
                // if budget is category-scoped, try to compute category-specific monthly total:
                if ($b->categoryId) {
                    // calculate sum of expenses for this category in that month (including subscription expansion)
                    $start = \Carbon\Carbon::createFromDate($checkYear, $checkMonth, 1)->startOfMonth();
                    $end = $start->copy()->endOfMonth();
                    $catTotal = Expense::where('userId', $userId)
                        ->where('categoryId', $b->categoryId)
                        ->where(function ($q) use ($start, $end) {
                            // any expenses created in the month
                            $q->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                                // OR recurring ones: subscription true and created_at <= end
                                ->orWhere(function ($q2) use ($start, $end) {
                                    $q2->where('subscription', 1)
                                        ->whereDate('created_at', '<=', $end->toDateString());
                                })
                                // OR previously updated spanning months:
                                ->orWhere(function ($q3) use ($start, $end) {
                                    $q3->where('subscription', 0)
                                        ->whereColumn('created_at', '<>', 'updated_at')
                                        ->whereDate('created_at', '<=', $end->toDateString())
                                        ->whereDate('updated_at', '>=', $start->toDateString());
                                });
                        })->get()->sum(function ($x) {
                            return (float)$x->cost;
                        });
                } else {
                    // budget with no category -> use whole-month expense
                    $catTotal = $monthlyMap[$key]['expense'] ?? 0.0;
                }
            }

            $percent = $b->amount > 0 ? min(100, round(($catTotal / $b->amount) * 100, 1)) : null;
            $budgetUsages[$b->id] = [
                'budget' => $b,
                'spent' => $catTotal,
                'percent' => $percent,
            ];
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
            'budgetUsages'
        ));
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
}
