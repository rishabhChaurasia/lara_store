<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // Import the Category model
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest; // Import the StoreProductRequest
use Illuminate\Support\Str; // For slug generation
use Illuminate\Support\Facades\Storage; // For image upload

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(10); // Fetch products with pagination
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        // Generate slug from name and ensure uniqueness
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        Product::create($data);

        // Clear related caches when creating a new product
        cache()->forget('featured_categories');
        cache()->forget('all_categories');
        cache()->forget('new_arrivals');
        cache()->forget('admin_dashboard_low_stock');

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            // Delete old image if it exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('products', 'public');
        } else {
             // If no new image is uploaded, retain the existing one
            $data['image_path'] = $product->image_path;
        }

        // Update slug from name only if name has changed
        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
        } else {
            // Keep the existing slug if name hasn't changed
            $data['slug'] = $product->slug;
        }

        $product->update($data);

        // Clear related caches when updating a product
        cache()->forget('featured_categories');
        cache()->forget('all_categories');
        cache()->forget('new_arrivals');
        cache()->forget('admin_dashboard_low_stock');

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated image if it exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        // Clear related caches when deleting a product
        cache()->forget('featured_categories');
        cache()->forget('all_categories');
        cache()->forget('new_arrivals');
        cache()->forget('admin_dashboard_low_stock');

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Generate a unique slug for the product
     */
    private function generateUniqueSlug($name, $exceptId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        // Check for existing slugs and add counter if needed
        while (true) {
            $query = Product::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
