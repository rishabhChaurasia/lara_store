@extends('layouts.admin')

@section('title', 'Create Product')
@section('header', 'Create Product')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (in cents)</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Enter amount in cents (e.g., $12.99 = 1299)</p>
                </div>

                <!-- Sale Price -->
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Sale Price (in cents, optional)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                    <div class="flex gap-2">
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button" id="generate-sku-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 whitespace-nowrap">
                            Generate SKU
                        </button>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Image -->
            <div class="mb-6">
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                <input id="image_path" type="file" name="image_path" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Is Active</label>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateSkuBtn = document.getElementById('generate-sku-btn');
        const skuInput = document.getElementById('sku');
        const nameInput = document.getElementById('name');

        generateSkuBtn.addEventListener('click', function() {
            let prefix = '';

            // If product name is available, use it to generate prefix
            if (nameInput && nameInput.value.trim() !== '') {
                const nameParts = nameInput.value.trim().split(/\s+/);
                if (nameParts.length >= 2) {
                    // Use first letter of first two words
                    prefix = (nameParts[0].charAt(0) + nameParts[1].charAt(0)).toUpperCase();
                } else {
                    // Use first 3 letters of the single word
                    prefix = nameParts[0].substring(0, 3).toUpperCase();
                }

                // Clean up the prefix to only contain letters
                prefix = prefix.replace(/[^A-Z]/g, '');
            } else {
                // Otherwise generate a random 3-letter prefix
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                for (let i = 0; i < 3; i++) {
                    prefix += chars.charAt(Math.floor(Math.random() * chars.length));
                }
            }

            // Add a 3-digit random number for uniqueness
            const randomNum = Math.floor(100 + Math.random() * 900); // 3-digit random number

            // Format as prefix-number (e.g., WGT-746)
            const sku = `${prefix}-${randomNum}`;

            skuInput.value = sku;
        });

        // Also generate SKU when product name changes, if SKU is empty
        if (nameInput) {
            nameInput.addEventListener('blur', function() {
                if (!skuInput.value.trim()) {
                    generateSkuBtn.click();
                }
            });
        }
    });
</script>

@endsection
