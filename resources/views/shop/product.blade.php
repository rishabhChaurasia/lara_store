@extends('layouts.app')

@section('title', $product->name)
@section('header', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
            <!-- Product Images Gallery -->
            <div>
                <div class="mb-4">
                    @if($product->image_path)
                        <img 
                            id="mainImage" 
                            src="{{ asset('storage/' . $product->image_path) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-96 object-contain rounded-lg"
                        >
                    @else
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-96 flex items-center justify-center text-gray-500">
                            No Image Available
                        </div>
                    @endif
                </div>
                
                <!-- Thumbnail Images -->
                <div class="grid grid-cols-4 gap-2">
                    <!-- For now, just showing the main image as a placeholder -->
                    @if($product->image_path)
                        <img 
                            src="{{ asset('storage/' . $product->image_path) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-blue-500"
                            onclick="changeMainImage(this.src)"
                        >
                    @else
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-20 flex items-center justify-center text-gray-500">
                            No Image
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Product Details -->
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                class="h-5 w-5 {{ $i <= round($averageRating ?: 0) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                viewBox="0 0 20 20" 
                                fill="currentColor"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-gray-600">({{ $reviews->count() }} reviews)</span>
                </div>
                
                <div class="text-2xl font-bold text-blue-600 mb-4">
                    ${{ number_format($product->price / 100, 2) }}
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-lg text-gray-500 line-through ml-2">${{ number_format($product->sale_price / 100, 2) }}</span>
                    @endif
                </div>
                
                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                    <div class="text-orange-600 font-semibold mb-4">
                        Only {{ $product->stock_quantity }} left in stock!
                    </div>
                @elseif($product->stock_quantity <= 0)
                    <div class="text-red-600 font-semibold mb-4">
                        Out of Stock
                    </div>
                @endif
                
                <p class="text-gray-700 mb-6">{{ $product->description }}</p>
                
                <div class="mb-6">
                    <p class="font-semibold">Category: 
                        <span class="text-gray-600">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </p>
                    <p class="font-semibold">SKU: 
                        <span class="text-gray-600">{{ $product->sku }}</span>
                    </p>
                </div>
                
                <!-- Add to Cart and Wishlist -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1 min-w-[200px]">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                name="quantity" 
                                value="1" 
                                min="1" 
                                max="{{ $product->stock_quantity ?: 10 }}"
                                class="border border-gray-300 rounded-l py-2 px-4 w-20 text-center"
                                {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}
                            >
                            <button 
                                type="submit" 
                                class="bg-blue-600 text-white py-2 px-4 rounded-r hover:bg-blue-700 transition duration-300 {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}
                            >
                                Add to Cart
                            </button>
                        </div>
                    </form>
                    
                    @auth
                        <form 
                            action="{{ route('wishlist.toggle', $product) }}" 
                            method="POST" 
                            class="inline"
                        >
                            @csrf
                            <button 
                                type="submit" 
                                class="flex items-center justify-center border border-gray-300 rounded px-4 py-2 hover:bg-gray-100 transition duration-300"
                            >
                                <svg 
                                    xmlns="http://www.w3.org/2000/svg" 
                                    class="h-5 w-5 mr-1 {{ $inWishlist ? 'text-red-500 fill-current' : 'text-gray-500' }}" 
                                    viewBox="0 0 20 20" 
                                    fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                                >
                                    <path 
                                        fill-rule="evenodd" 
                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" 
                                        clip-rule="evenodd" 
                                    />
                                </svg>
                                {{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                            </button>
                        </form>
                    @endauth
                    
                    @guest
                        <a 
                            href="{{ route('login') }}" 
                            class="flex items-center justify-center border border-gray-300 rounded px-4 py-2 hover:bg-gray-100 transition duration-300"
                        >
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                class="h-5 w-5 mr-1 text-gray-500" 
                                viewBox="0 0 20 20" 
                                fill="none"
                                stroke="currentColor"
                            >
                                <path 
                                    fill-rule="evenodd" 
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" 
                                    clip-rule="evenodd" 
                                />
                            </svg>
                            Add to Wishlist
                        </a>
                    @endguest
                </div>
                
                <!-- Share Buttons -->
                <div>
                    <p class="font-semibold mb-2">Share:</p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-blue-400 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="text-blue-700 hover:text-blue-900">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Reviews Section -->
    <div class="mt-12 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>
        
        @if($reviews->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400 mr-4">
                        @for($i = 1; $i <= 5; $i++)
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                class="h-6 w-6 {{ $i <= round($averageRating ?: 0) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                viewBox="0 0 20 20" 
                                fill="currentColor"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-lg font-semibold">{{ number_format($averageRating, 1) }} out of 5</span>
                    <span class="text-gray-600 ml-2">({{ $reviews->count() }} reviews)</span>
                </div>
                
                <!-- Rating distribution -->
                <div class="mb-6">
                    @for($i = 5; $i >= 1; $i--)
                        <div class="flex items-center mb-1">
                            <span class="w-10 text-sm">{{ $i }} stars</span>
                            <div class="w-48 bg-gray-200 rounded-full h-2 mx-2">
                                <div 
                                    class="bg-yellow-400 h-2 rounded-full" 
                                    style="width: {{ $reviews->count() > 0 ? ($ratingCounts[$i] / $reviews->count()) * 100 : 0 }}%"
                                ></div>
                            </div>
                            <span class="text-sm w-8">{{ $ratingCounts[$i] }}</span>
                        </div>
                    @endfor
                </div>
            </div>
            
            <!-- Reviews List -->
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                        viewBox="0 0 20 20" 
                                        fill="currentColor"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="font-semibold">{{ $review->user->name }}</span>
                        </div>
                        <p class="text-gray-600">{{ $review->comment }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ $review->created_at->format('F j, Y') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No reviews yet. Be the first to review this product!</p>
        @endif
    </div>
    
    <!-- Related Products Section -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                        <a href="{{ route('product.show', $relatedProduct->slug) }}">
                            @if($relatedProduct->image_path)
                                <img src="{{ asset('storage/' . $relatedProduct->image_path) }}" alt="{{ $relatedProduct->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-48 flex items-center justify-center text-gray-500">
                                    No Image
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-1">{{ $relatedProduct->name }}</h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-blue-600">
                                        ${{ number_format($relatedProduct->price / 100, 2) }}
                                    </span>
                                    @if($relatedProduct->stock_quantity <= 5 && $relatedProduct->stock_quantity > 0)
                                        <span class="text-xs text-orange-600">Low Stock</span>
                                    @elseif($relatedProduct->stock_quantity <= 0)
                                        <span class="text-xs text-red-600">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }
    
    // Update wishlist button on click
    document.addEventListener('DOMContentLoaded', function() {
        const wishlistForms = document.querySelectorAll('form[action*="wishlist.toggle"]');

        wishlistForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get the CSRF token from the meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Create form data from the form
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        const button = form.querySelector('button');
                        const svg = button.querySelector('svg');
                        const text = button.querySelector('svg + span') || button.querySelector('span span'); // Adjusted selector

                        if(data.action === 'added') {
                            svg.setAttribute('fill', 'currentColor');
                            svg.classList.add('text-red-500');
                            if(text) text.textContent = 'Remove from Wishlist';
                        } else {
                            svg.setAttribute('fill', 'none');
                            svg.classList.remove('text-red-500');
                            svg.classList.add('text-gray-500');
                            if(text) text.textContent = 'Add to Wishlist';
                        }

                        // Show temporary message
                        const tempMessage = document.createElement('div');
                        tempMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                        tempMessage.textContent = data.message;
                        document.body.appendChild(tempMessage);

                        setTimeout(() => {
                            tempMessage.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>
@endpush
@endsection