<?php

namespace App\Http\Controllers;

use App\Models\ProductsCategory;
use Illuminate\Http\Request;

class ProductsCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductsCategory::all();

        return response()->json($categories);
    }

    public function show($id)
    {
        $category = ProductsCategory::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = ProductsCategory::create($data);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = ProductsCategory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = ProductsCategory::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
