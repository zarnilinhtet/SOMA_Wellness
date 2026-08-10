<?php


namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // အသစ်ထည့်ထားသော Category များကို အပေါ်ဆုံးမှ ပြရန် latest() သုံးထားသည်
        $categories = Category::latest()->get();
        return view('backends.categories.categories_index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            // နာမည်တူရှိမရှိ စစ်ဆေးမည် (မိမိကိုယ်တိုင်၏ ID ကိုမူ ချန်လှပ်ထားမည်)
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}