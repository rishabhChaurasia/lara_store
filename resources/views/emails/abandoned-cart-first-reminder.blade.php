<x-mail::message>
# Your Cart is Waiting For You!

Hi there,

We noticed you left some items in your cart. Don't forget to complete your purchase.

<x-mail::table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cart->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ number_format($item->product->price / 100, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</x-mail::table>

<x-mail::button :url="route('checkout.index')">
Complete Your Purchase
</x-mail::button>

Your items will be reserved for a limited time. Complete your order now to secure them!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
