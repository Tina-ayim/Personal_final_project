<?php require_once 'layout_header.php'; ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Help Center</h1>
        <p class="text-xl text-gray-600">Frequently asked questions and support.</p>
    </div>

    <!-- FAQ Section -->
    <div class="space-y-6 mb-16">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">How do I list an item?</h3>
            <p class="text-gray-600">Go to your dashboard or click "My Listings" and verify your account. Once verified, click "Add New Item" and follow the prompts.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Is insurance included?</h3>
            <p class="text-gray-600">Yes, a 10% insurance fee is applied to all rentals to cover accidental damage during the rental period.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">What if the owner cancels?</h3>
            <p class="text-gray-600">If an owner cancels your booking, you will receive a full refund immediately to your original payment method.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">How are payments handled?</h3>
            <p class="text-gray-600">We accept major credit cards and Mobile Money. Payments are securely processed and held until the rental starts.</p>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="bg-gray-50 rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Still need help? Contact Us</h2>
        <form action="#" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                <input type="text" class="w-full rounded-md border-gray-300 shadow-sm p-3 border" placeholder="John Doe">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                <input type="email" class="w-full rounded-md border-gray-300 shadow-sm p-3 border" placeholder="john@example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea rows="4" class="w-full rounded-md border-gray-300 shadow-sm p-3 border" placeholder="How can we help you?"></textarea>
            </div>
            <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-md font-bold hover:bg-primary-700 transition w-full md:w-auto">
                Send Message
            </button>
        </form>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
