<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $categories = Category::withCount('products')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }
    public function create() {
        return view('admin.categories.create');
    }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dibuat.');
    }
    public function edit(Category $category) {
        return view('admin.categories.edit', compact('category'));
    }
    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,' . $category->id]);
        $category->update($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }
    public function destroy(Category $category) {
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}