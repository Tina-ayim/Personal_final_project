<?php require_once 'layout_header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">How it Works</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Rent anything you need, or earn money from things you own. It's simple, secure, and helpful.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
        <!-- For Renters -->
        <div>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class='bx bx-search text-2xl text-primary-600'></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">For Renters</h2>
            </div>
            
            <div class="space-y-8">
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-primary-500"></div>
                    <h3 class="text-lg font-bold mb-2">1. Find what you need</h3>
                    <p class="text-gray-600">Search for items in your neighborhood. Filter by price, category, and availability.</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-primary-500"></div>
                    <h3 class="text-lg font-bold mb-2">2. Book instantly</h3>
                    <p class="text-gray-600">Choose your dates and book. We hold the payment securely until the rental starts.</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-primary-500"></div>
                    <h3 class="text-lg font-bold mb-2">3. Pick up & Use</h3>
                    <p class="text-gray-600">Meet the owner to pick up the item. Check it over, and enjoy using it!</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-primary-500"></div>
                    <h3 class="text-lg font-bold mb-2">4. Return & Review</h3>
                    <p class="text-gray-600">Return the item on time and leave a review to help build trust in the community.</p>
                </div>
            </div>
        </div>

        <!-- For Owners -->
        <div>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class='bx bx-store text-2xl text-green-600'></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">For Owners</h2>
            </div>
            
            <div class="space-y-8">
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-green-500"></div>
                    <h3 class="text-lg font-bold mb-2">1. List your items</h3>
                    <p class="text-gray-600">Upload photos, write a description, and set your price. It takes less than 2 minutes.</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-green-500"></div>
                    <h3 class="text-lg font-bold mb-2">2. Accept requests</h3>
                    <p class="text-gray-600">You control who rents your stuff. Review profiles and accept bookings that work for you.</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-green-500"></div>
                    <h3 class="text-lg font-bold mb-2">3. Hand it over</h3>
                    <p class="text-gray-600">Meet the renter to hand over the item. Confirm the condition together.</p>
                </div>
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-green-500"></div>
                    <h3 class="text-lg font-bold mb-2">4. Get paid</h3>
                    <p class="text-gray-600">Payments are released to your account automatically after the rental concludes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="mt-20 text-center bg-gray-50 rounded-2xl p-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to get started?</h2>
        <p class="text-gray-600 mb-8">Join thousands of neighbors sharing more and wasting less.</p>
        <div class="flex justify-center gap-4">
            <a href="items_browse.php" class="bg-white border border-gray-300 text-gray-700 px-8 py-3 rounded-full font-bold hover:bg-gray-50 transition">Browse Items</a>
            <a href="auth_register.php" class="bg-primary-600 text-white px-8 py-3 rounded-full font-bold hover:bg-primary-700 transition">Join Now</a>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
