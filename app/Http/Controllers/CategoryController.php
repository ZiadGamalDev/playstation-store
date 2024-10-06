<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return $this->respondWithData('Categories retrieved successfully', $categories, 200);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $category = Category::create($data);

        return $this->respondWithData('Category created successfully', $category, 201);
    }

    public function show(Category $category)
    {
        return $this->respondWithData('Category retrieved successfully', $category, 200);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $category->update($data);
        
        return $this->respondWithData('Category updated successfully', $category, 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        
        return $this->successResponse('Category deleted successfully', 200);
    }
}
