<div class="flex flex-wrap justify-between gap-3 pb-6 border-b border-gray-200 dark:border-gray-800">
    <div class="flex flex-col gap-2">
        <h1 class="text-4xl font-black text-black dark:text-white">Shipping Policy</h1>
        <p class="text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
    </div>
</div>


<div class="prose dark:prose-invert max-w-none">
    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">1. Processing Time</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        All orders are processed within 2-3 business days. Orders are not shipped or delivered on weekends or holidays. If we are experiencing a high volume of orders, shipments may be delayed by a few days. Please allow additional days in transit for delivery.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">2. Shipping Rates & Delivery Estimates</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        Shipping charges for your order will be calculated and displayed at checkout. We offer free standard shipping on orders over $50. Delivery times vary depending on your location and selected shipping method:
    </p>
    <ul class="text-gray-600 dark:text-gray-300 mt-3 space-y-2">
        <li>Standard Shipping: 5-7 business days</li>
        <li>Expedited Shipping: 2-3 business days</li>
        <li>Express Shipping: 1-2 business days</li>
    </ul>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">3. Shipment Confirmation & Order Tracking</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        You will receive a shipment confirmation email once your order has shipped containing your tracking number(s). The tracking number will be active within 24 hours.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">4. Damages</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        {{ config('app.name') }} is not liable for any products damaged or lost during shipping. If you received your order damaged, please contact the shipment carrier to file a claim. Please save all packaging materials and damaged goods before filing a claim.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">5. International Shipping</h2>
    <p class=" text-gray-600 dark:text-gray-300 leading-relaxed">
        We currently ship to select international destinations. International shipping times and costs vary by location. Additional customs fees and import duties may apply and are the responsibility of the customer.
    </p>
</div>
