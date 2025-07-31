<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function addCategory(Request $request)
    {
        // $request->validate([
        //     'category' => ['required', 'unique:' . Category::class]
        // ]);
        if (Category::create([
            'category' => $request->categoryName,
            'userId' => Auth::id(),
            'description' => $request->cdescription,
        ])) {
            return redirect()->back()->with('success', 'Category added successfully.');
        } else {
            return redirect()->back()->with('failed', 'Category failed to add.');
        }
    }
}
