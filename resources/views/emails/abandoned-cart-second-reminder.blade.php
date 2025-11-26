<x-mail::message>
# Your Items Are Still Waiting!

Hi again,

Your shopping cart still has items waiting for you. Don't miss out on these products.

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
Return to Checkout
</x-mail::button>

@if($discountCode)
**Special Offer**: As a valued customer, we're offering you a special discount! Use code **{{ $discountCode }}** for 5% off your order.
@endif

We're holding these items for you, but they might sell out soon. Complete your order before it's too late!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
