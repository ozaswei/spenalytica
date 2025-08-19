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
            'label' => 'required',
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
            return redirect()->back()->with('success', 'Income added successfully.')->with('activeTab', 'addIncome');
        } else {
            return redirect()->back()->with('failed', 'Income failed to add.')->with('activeTab', 'addIncome');
        }
    }

    public function editIncome(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'icategoryId' => 'required',
            'mrr' => 'required',
            'revenue' => 'required',
        ]);
        $income = Income::findOrFail($request->incomeId);
        $income->categoryId = $request->icategoryId;
        $income->label = $request->label;
        $income->mrr = $request->mrr;
        $income->revenue = $request->revenue;
        $income->description = $request->idescription;
        if ($income->save()) {
            return redirect()->back()
                ->with('success', 'Income data updated successfully')
                ->with('activeTab', 'addIncome');
        } else {
            return redirect()->back()
                ->with('failed', 'Income data update failed')
                ->with('activeTab', 'addIncome');
        }
    }

    public function deleteIncome(Request $request)
    {
        $income = Income::find($request->incomeId);
        if ($income->delete()) {
            return redirect()->back()
                ->with('success', 'Income data deleted successfully')
                ->with('activeTab', 'addIncome');
        } else {
            return redirect()->back()
                ->with('failed', 'Income data deletion failed')
                ->with('activeTab', 'addIncome');
        }
    }
}
