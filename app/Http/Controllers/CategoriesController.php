<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Categories';
        $categories = Category::with('parent')->latest()->paginate(10);
        return view('categories.index', compact('categories', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Category';
        $categoryOptions = Category::treeList();
        return view('categories._form', compact('categoryOptions', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        DB::beginTransaction();
        try {

            Category::create($request->only('name', 'parent_id'));
            DB::commit();

            return redirectBackWithSuccess('Category has been created successfully', 'categories.index');
        } catch (\Throwable $th) {
            return backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $pageTitle = 'Edit Category';
        $categoryOptions = Category::treeList();
        unset($categoryOptions[$category->id]);

        return view('categories._form', compact('category', 'categoryOptions', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
        ]);

        DB::beginTransaction();
        try {
            $category->update($request->only('name', 'parent_id'));
            DB::commit();
            return redirectBackWithSuccess('Category has been updated successfully', 'categories.index');
        } catch (\Throwable $th) {
            return backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
