@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="relative flex w-full" style="font-family: 'Inter', sans-serif;">
    <!-- Fixed Vertical Sidebar -->
    <aside class="sticky top-0 flex h-screen w-20 flex-col items-center bg-white py-8 dark:border-gray-700/50 dark:bg-[#101010] lg:w-64 lg:items-stretch lg:px-6 rounded-2xl mt-5 ml-5">
        <div class="flex flex-col gap-8 lg:gap-6">
            <div class="flex items-center gap-3 justify-center lg:justify-start">
                <span class="material-symbols-outlined text-gray-800 dark:text-white text-3xl">storefront</span>
                <span class="hidden text-xl font-bold text-gray-800 dark:text-white lg:inline">LUXE</span>
            </div>
            <div class="flex items-center gap-4 justify-center lg:justify-start">
                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12 flex items-center justify-center" style="background-color: rgba(192, 161, 114, 0.3);">
                    <span class="material-symbols-outlined text-2xl" style="color: #C0A172;">person</span>
                </div>
                <div class="hidden lg:flex lg:flex-col">
                    <h1 class="text-gray-800 dark:text-white text-base font-medium leading-normal">{{ Auth::user()->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Welcome</p>
                </div>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50" href="{{ route('account.dashboard') }}">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">person</span>
                    <p class="hidden text-gray-600 dark:text-gray-400 text-sm font-medium leading-normal lg:inline group-hover:text-primary-gold">Profile</p>
                </a>
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50" href="{{ route('account.orders') }}">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">receipt_long</span>
                    <p class="hidden text-gray-600 dark:text-gray-400 text-sm font-medium leading-normal lg:inline group-hover:text-primary-gold">Orders</p>
                </a>
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 dark:bg-opacity-20" style="background-color: rgba(192, 161, 114, 0.1);" href="{{ route('account.wishlist') }}">
                    <span class="material-symbols-outlined" style="color: #C0A172;">favorite</span>
                    <p class="hidden text-sm font-medium leading-normal lg:inline" style="color: #C0A172;">Wishlist</p>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 lg:p-10 bg-gray-50 dark:bg-[#020202]">
        <div class="mx-auto max-w-7xl">
            <!-- Page Heading -->
            <div class="mb-8">
                <div class="flex min-w-72 flex-col gap-2">
                    <p class="text-gray-800 dark:text-white text-4xl font-bold leading-tight tracking-tight">Wishlist</p>
                    <p class="text-gray-600 dark:text-gray-400 text-base font-normal leading-normal">{{ $wishlistItems->count() }} {{ $wishlistItems->count() === 1 ? 'item' : 'items' }}</p>
                </div>
            </div>

            @if($wishlistItems->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($wishlistItems as $item)
                        <div class="group rounded-lg border border-gray-200 dark:border-gray-700/50 bg-white dark:bg-[#101010] overflow-hidden transition-all hover:shadow-md" x-data="{ removing: false }" x-show="!removing" x-transition>
                            <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-zinc-900">
                                <a href="{{ route('product.show', $item->product->slug) }}">
                                    @if($item->product->image_path)
                                        <img 
                                            class="h-full w-full object-cover object-center transition-transform group-hover:scale-105" 
                                            src="{{ asset('storage/' . $item->product->image_path) }}" 
                                            alt="{{ $item->product->name }}"
                                        />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <span class="text-sm text-gray-400 dark:text-gray-600">No image</span>
                                        </div>
                                    @endif
                                </a>
                                <button 
                                    @click.prevent="
                                        removing = true;
                                        fetch('{{ route('wishlist.remove', $item->product) }}', {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => setTimeout(() => location.reload(), 300));
                                    "
                                    class="absolute right-2 top-2 rounded-md bg-white/90 dark:bg-zinc-900/90 p-2 text-gray-600 dark:text-gray-400 shadow-sm transition-colors hover:bg-white dark:hover:bg-zinc-900 hover:text-red-600 dark:hover:text-red-500"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="space-y-1">
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="block">
                                        <h3 class="text-sm font-medium leading-tight text-gray-900 dark:text-white line-clamp-2 hover:text-gray-700 dark:hover:text-gray-300">{{ $item->product->name }}</h3>
                                    </a>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($item->product->price / 100, 2) }}</p>
                                </div>
                                <form action="{{ route('cart.add') }}" method="POST" class="w-full" onsubmit="return moveToCart(event, {{ $item->product->id }})">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button 
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-md bg-gray-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-gray-900 transition-colors hover:bg-gray-800 dark:hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50"
                                        {{ $item->product->stock_quantity <= 0 ? 'disabled' : '' }}
                                    >
                                        {{ $item->product->stock_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                                    </button>
                                </form>
                                <form id="remove-wishlist-{{ $item->product->id }}" action="{{ route('wishlist.remove', $item->product) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($wishlistItems->hasPages())
                    <div class="mt-8">
                        {{ $wishlistItems->links() }}
                    </div>
                @endif
            @else
                <div class="flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 dark:border-gray-700/50 p-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-900">
                        <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No items in wishlist</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">You haven't added any items to your wishlist yet.</p>
                    <a href="{{ route('shop.index') }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-gray-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-gray-900 transition-colors hover:bg-gray-800 dark:hover:bg-gray-100">
                        Browse Products
                    </a>
                </div>
            @endif
        </div>
    </main>
</div>

<script>
function moveToCart(event, productId) {
    event.preventDefault();
    const cartForm = event.target;
    const removeForm = document.getElementById('remove-wishlist-' + productId);
    
    fetch(cartForm.action, {
        method: 'POST',
        body: new FormData(cartForm)
    })
    .then(() => {
        removeForm.submit();
    })
    .catch(err => console.error('Error:', err));
    
    return false;
}
</script>
@endsection
