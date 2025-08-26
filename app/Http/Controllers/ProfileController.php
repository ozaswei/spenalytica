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

        $currentDate = Carbon::now();

        // --- load data
        $categories = Category::where('userId', $userId)->get();
        $expenses   = Expense::where('userId', $userId)->get();
        $incomes    = Income::where('userId', $userId)->get();

        // top 5 highest expenses
        $highestExpenses = Expense::where('userId', $userId)->orderBy('cost', 'desc')->take(5)->get();

        // monthly revenues
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

        // monthly expenses
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

        // Merge incomes and expenses to monthly dataset (ensuring months with only one side are included)
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

        // compute savings column
        foreach ($monthlyMap as $k => $v) {
            $monthlyMap[$k]['savings'] = max(0, $v['income'] - $v['expense']);
        }

        // sort by year-month ascending
        ksort($monthlyMap);
        $monthlyDatas = collect(array_values($monthlyMap));

        // --- aggregate values for health calculation
        $totalIncome = $incomes->sum('revenue');
        $totalExpense = $expenses->sum('cost');
        $currentBalance = $totalIncome - $totalExpense; // net money in the system

        // average monthly savings: use monthlyDatas savings if available, otherwise compute from incomes-expenses
        $avgSavings = null;
        if ($monthlyDatas->count() > 0) {
            // compute monthly savings (income - expense) as signed value (can be negative)
            $signedMonthly = collect($monthlyMap)->map(function ($m) {
                return ($m['income'] - $m['expense']);
            })->values();
            // average of signed monthly differences
            $avgSavings = $signedMonthly->avg();
        } else {
            // fallback to naive monthly average: (totalIncome - totalExpense) / monthsActive (if available)
            $avgSavings = 0;
        }
        // make sure numeric
        $avgSavings = (float) $avgSavings;

        // spending health (based on latest month) - more informative thresholds
        $spendingHealth = 'Unknown';
        $latestMonth = $monthlyDatas->last();

        if ($latestMonth) {
            $incomeLatest = (float)$latestMonth['income'];
            $expenseLatest = (float)$latestMonth['expense'];

            if ($incomeLatest <= 0 && $expenseLatest > 0) {
                $spendingHealth = 'Critical'; // spending with no income
            } elseif ($expenseLatest === 0 && $incomeLatest === 0) {
                $spendingHealth = 'No activity';
            } else {
                $ratio = $expenseLatest / max(1, $incomeLatest); // prevent division by 0
                if ($ratio >= 1.0) {
                    $spendingHealth = 'Unhealthy';
                } elseif ($ratio >= 0.75) {
                    $spendingHealth = 'At Risk';
                } elseif ($ratio >= 0.5) {
                    $spendingHealth = 'Neutral';
                } else {
                    $spendingHealth = 'Healthy';
                }
            }
        }

        // time until broke:
        // If avgSavings < 0 => user is losing money monthly. monthsUntilBroke = currentBalance / abs(avgSavings)
        // Protect against divide by zero and negative balance
        $monthsUntilBroke = null;
        if ($avgSavings < 0) {
            if ($currentBalance <= 0) {
                $monthsUntilBroke = 0;
            } else {
                $monthsUntilBroke = floor($currentBalance / abs($avgSavings));
                if ($monthsUntilBroke < 0) $monthsUntilBroke = 0;
            }
        } else {
            $monthsUntilBroke = null; // not losing money
        }

        // get a savings goal from session if set
        $savingsGoal = session('savingsGoal', null); // you can persist this elsewhere later

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
            'savingsGoal'
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
