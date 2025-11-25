<div class="flex flex-wrap justify-between gap-3 pb-6 border-b border-gray-200 dark:border-gray-800">
    <div class="flex flex-col gap-2">
        <h1 class="text-4xl font-black text-black dark:text-white">Refund Policy</h1>
        <p class="text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
    </div>
</div>


<div class="prose dark:prose-invert max-w-none">
    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">1. Returns</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        Our policy lasts 30 days. If 30 days have gone by since your purchase, unfortunately we can't offer you a refund or exchange. To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">2. Refunds</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        Once your return is received and inspected, we will send you an email to notify you that we have received your returned item. We will also notify you of the approval or rejection of your refund. If you are approved, then your refund will be processed, and a credit will automatically be applied to your credit card or original method of payment, within a certain amount of days.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">3. Late or Missing Refunds</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        If you haven't received a refund yet, first check your bank account again. Then contact your credit card company, it may take some time before your refund is officially posted. Next contact your bank. There is often some processing time before a refund is posted. If you've done all of this and you still have not received your refund yet, please contact us.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">4. Sale Items</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        Only regular priced items may be refunded. Unfortunately, sale items cannot be refunded.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">5. Exchanges</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        We only replace items if they are defective or damaged. If you need to exchange it for the same item, send us an email at {{ env('ADMIN_MAIL', 'support@example.com') }} and send your item to the address provided.
    </p>
</div>
