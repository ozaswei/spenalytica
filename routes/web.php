<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController;
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
    Route::post('/addCategory',[CategoryController::class,'addCategory'])->name('addCategory');
    Route::post('/addExpense',[ExpenseController::class,'addExpense'])->name('addExpense');
    Route::post('/addIncome',[IncomeController::class,'addIncome'])->name('addIncome');
});

require __DIR__.'/auth.php';
