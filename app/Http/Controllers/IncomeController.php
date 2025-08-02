<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function addIncome(Request $request)
    {
        $request->validate([
            'label' => ['required', 'unique:' . Income::class],
            'icategoryId' => 'required',
            'mrr' => 'required',
            'revenue' => 'required',
        ]);

        if (Income::create([
            'label' => $request->label,
            'userId' => Auth::id(),
            'categoryId' => $request->icategoryId,
            'mrr' => $request->mrr,
            'revenue' => $request->revenue,
            'description' => $request->idescription
        ])) {
            return redirect()->back()->with('success', 'Income added successfully.');
        } else {
            return redirect()->back()->with('failed', 'Income failed to add.');
        }
    }
}
