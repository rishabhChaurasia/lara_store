<div class="flex flex-wrap justify-between gap-3 pb-6 border-b border-gray-200 dark:border-gray-800">
    <div class="flex flex-col gap-2">
        <h1 class="text-4xl font-black text-black dark:text-white">Terms of Service</h1>
        <p class="text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
    </div>
</div>


<div class="prose dark:prose-invert max-w-none">
    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">1. Acceptance of Terms</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        By accessing and using {{ config('app.name') }}, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">2. Use License</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        Permission is granted to temporarily download one copy of the materials on {{ config('app.name') }}'s website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not: modify or copy the materials; use the materials for any commercial purpose or for any public display; attempt to reverse engineer any software contained on {{ config('app.name') }}'s website; remove any copyright or other proprietary notations from the materials; or transfer the materials to another person or "mirror" the materials on any other server.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">3. Disclaimer</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        The materials on {{ config('app.name') }}'s website are provided on an 'as is' basis. {{ config('app.name') }} makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">4. Limitations</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        In no event shall {{ config('app.name') }} or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on {{ config('app.name') }}'s website.
    </p>

    <h2 class="text-2xl font-bold text-black dark:text-white mt-8 mb-4">5. Revisions</h2>
    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
        The materials appearing on {{ config('app.name') }}'s website could include technical, typographical, or photographic errors. {{ config('app.name') }} does not warrant that any of the materials on its website are accurate, complete or current. {{ config('app.name') }} may make changes to the materials contained on its website at any time without notice.
    </p>
</div>
