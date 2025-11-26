<x-mail::message>
# Last Chance to Complete Your Purchase!

Last call!

This is your final reminder about the items in your shopping cart.

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
Complete Your Purchase Now
</x-mail::button>

@if($discountCode)
**Special Offer**: As a final attempt to win back your business, we're offering you an extra special discount! Use code **{{ $discountCode }}** for 10% off your order.
@endif

These items might be released soon if you don't complete your order. Don't let them get away!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
