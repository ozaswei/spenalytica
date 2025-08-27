<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('spenalytica.frontPage');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/home', [ProfileController::class, 'homePage'])->name('homePage');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/addExpense', [ExpenseController::class, 'addExpense'])->name('addExpense');
    Route::post('/editExpense', [ExpenseController::class, 'editExpense'])->name('editExpense');
    Route::post('/deleteExpense', [ExpenseController::class, 'deleteExpense'])->name('deleteExpense');
    Route::post('/addIncome', [IncomeController::class, 'addIncome'])->name('addIncome');
    Route::post('/editIncome', [IncomeController::class, 'editIncome'])->name('editIncome');
    Route::post('/deleteIncome', [IncomeController::class, 'deleteIncome'])->name('deleteIncome');
    Route::post('/addCategory', [CategoryController::class, 'addCategory'])->name('addCategory');
    Route::post('/editCategory', [CategoryController::class, 'editCategory'])->name('editCategory');
    Route::post('/deleteCategory', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
    // savings goal setter (POST)
    Route::post('/set-savings-goal', [App\Http\Controllers\ProfileController::class, 'setSavingsGoal'])->name('setSavingsGoal');
    //budgets
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
});

require __DIR__ . '/auth.php';
