<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // Import the Category model
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest; // Import the StoreCategoryRequest
use Illuminate\Support\Str; // For slug generation
use Illuminate\Support\Facades\Storage; // For image upload

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(10); // Fetch categories with pagination
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all(); // For parent category selection
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('categories', 'public');
        }

        // Explicitly set is_active based on checkbox presence
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Generate slug from name and ensure uniqueness
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        Category::create($data);

        // Clear related caches when creating a new category
        cache()->forget('featured_categories');
        cache()->forget('all_categories');

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get(); // Exclude self for parent selection
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            // Delete old image if it exists
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('categories', 'public');
        } else {
             // If no new image is uploaded, retain the existing one
            $data['image_path'] = $category->image_path;
        }

        // Explicitly set is_active based on checkbox presence
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Update slug from name only if name has changed
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        } else {
            // Keep the existing slug if name hasn't changed
            $data['slug'] = $category->slug;
        }

        $category->update($data);

        // Clear related caches when updating a category
        cache()->forget('featured_categories');
        cache()->forget('all_categories');

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Delete associated image if it exists
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        // Delete all child categories (recursive deletion)
        $category->children()->delete();

        // Optionally, handle products associated with this category
        // If products should not be deleted when category is deleted, just remove the category association
        // $category->products()->update(['category_id' => null]);
        // Or if products should be deleted with the category:
        // $category->products()->delete();

        // For this implementation, I'll assume products should have their category_id set to null
        // rather than being deleted, but you can adjust based on your business logic
        $category->products()->update(['category_id' => null]);

        $category->delete();

        // Clear related caches when deleting a category
        cache()->forget('featured_categories');
        cache()->forget('all_categories');

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    /**
     * Generate a unique slug for the category
     */
    private function generateUniqueSlug($name, $exceptId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        // Check for existing slugs and add counter if needed
        while (true) {
            $query = Category::where('slug', $slug);
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
