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
            return redirect()->back()->with('success', 'Category added successfully.')->with('activeTab', 'category');
        } else {
            return redirect()->back()->with('failed', 'Category failed to add.')->with('activeTab', 'category');
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
        if ($category->save()) {
            return redirect()->back()
                ->with('success', 'Category updated successfully')
                ->with('activeTab', 'category');
        } else {
            return redirect()->back()
                ->with('failed', 'Category update failed')
                ->with('activeTab', 'category');
        }
    }
    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->categoryId);
        if ($category->delete()) {
            return redirect()->back()
                ->with('success', 'Category data deleted successfully')
                ->with('activeTab', 'category');
        } else {
            return redirect()->back()
                ->with('failed', 'Category data deletion failed')
                ->with('activeTab', 'category');
        }
    }
}
