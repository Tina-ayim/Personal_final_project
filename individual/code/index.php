<?php require 'layout_header.php'; ?>

<!-- Hero Section -->
<section class="relative bg-secondary-500 overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')] bg-cover bg-center"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 flex flex-col items-center text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-6">
            Rent Anything, <span class="text-primary-500">Anywhere.</span>
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mb-10">
            Don't buy it if you only need it once. Join your local community to share tools, tech, and gear securely.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="items_browse.php" class="bg-primary-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:bg-primary-600 transform hover:-translate-y-1 transition duration-300">
                Browse Listings
            </a>
            <a href="auth_register.php" class="bg-white text-secondary-500 px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:bg-gray-100 transform hover:-translate-y-1 transition duration-300">
                Start Listing
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Com.Rental?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">We make sharing easy, secure, and rewarding for everyone involved.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Feature 1 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class='bx bx-shield-quarter text-3xl text-primary-600'></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-secondary-500">Secure & Verified</h3>
                <p class="text-gray-600">Every user is verified and every rental is insured. Rent with peace of mind knowing you're protected.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class='bx bx-money text-3xl text-primary-600'></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-secondary-500">Earn Extra Cash</h3>
                <p class="text-gray-600">Turn your idle assets into income. That drill collecting dust? It could be paying for your next dinner.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition duration-300 text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class='bx bx-planet text-3xl text-primary-600'></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-secondary-500">Eco-Friendly</h3>
                <p class="text-gray-600">Reduce waste by sharing what already exists. Validating the circular economy one rental at a time.</p>
            </div>
        </div>
    </div>
</section>

<!-- About / Call to Action -->
<section class="py-20 bg-gray-50 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-primary-600 font-bold uppercase tracking-wider text-sm">About Us</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-6">Built by Neighbors, for Neighbors.</h2>
            <p class="text-gray-600 mb-6 text-lg">
                We started Com.Rental with a simple mission: to bring communities closer together through sharing. 
                Whether you need a ladder for a day or want to rent out your camera gear, we provide the platform to make it happen safely.
            </p>
            <ul class="space-y-4 mb-8">
                <li class="flex items-center gap-3">
                    <i class='bx bx-check-circle text-primary-500 text-xl'></i>
                    <span class="text-gray-700">Zero hidden fees for renters</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class='bx bx-check-circle text-primary-500 text-xl'></i>
                    <span class="text-gray-700">24/7 Community Support</span>
                </li>
            </ul>
            <a href="about.php" class="text-primary-600 font-bold hover:text-primary-700 hover:underline">Learn More about our Mission &rarr;</a>
        </div>
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Community" class="rounded-2xl shadow-2xl">
            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-lg border hidden md:block">
                <p class="font-bold text-3xl text-primary-500">5k+</p>
                <p class="text-gray-500 text-sm">Active Rentals</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center bg-secondary-500 rounded-3xl p-12 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-4">Ready to get started?</h2>
            <p class="text-indigo-100 mb-8 max-w-lg mx-auto">Join thousands of others who are already renting and earning in your local area today.</p>
            <a href="auth_register.php" class="bg-white text-secondary-500 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition inline-block">Create Free Account</a>
        </div>
        
        <!-- Decoration bubbles -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-primary-500 rounded-full opacity-20 filter blur-xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-primary-500 rounded-full opacity-20 filter blur-xl"></div>
    </div>
</section>

<?php require 'layout_footer.php'; ?>