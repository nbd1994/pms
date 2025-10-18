<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required','string','max:255','unique:categories,name']]);
        $cat = Category::create($data);
        return response()->json(['ok' => true, 'category' => $cat], 201);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name,'.$category->id]
        ]);
        $category->update($data);
        return response()->json(['ok' => true, 'category' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['ok' => true]);
    }
}