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

    public function editCategory(Request $request)
    {
        $request->validate([
            'categoryName' => 'required|string',
            'cdescription' => 'nullable|string',
        ]);
        // If success, update category
        $category = Category::findOrFail($request->categoryId);
        $category->category = $request->categoryName;
        $category->description = $request->cdescription;
        $category->save();

        return redirect()->back()
            ->with('success', 'Category updated successfully')
            ->with('activeTab', 'category');
    }
}
