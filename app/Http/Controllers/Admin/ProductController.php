<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller {
    public function index() {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }
    public function create() {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        $path = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;
        Product::create($request->except('image') + ['image_path' => $path]);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dibuat.');
    }
    public function edit(Product $product) {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        $path = $product->image_path;
        if ($request->hasFile('image')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('image')->store('products', 'public');
        }
        $product->update($request->except('image') + ['image_path' => $path]);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }
    public function destroy(Product $product) {
        if ($product->image_path) Storage::disk('public')->delete($product->image_path);
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }
}