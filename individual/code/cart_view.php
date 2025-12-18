<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login(); 

$user_id = $_SESSION['user_id'];
$cart_items = $conn->query("SELECT cart.id as cart_id, cart.days, items.title, items.price_per_day, items.image_url, (items.price_per_day * cart.days) as total_price FROM cart JOIN items ON cart.item_id = items.id WHERE cart.user_id = $user_id");

$grand_total = 0;
require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <?php if ($cart_items->num_rows > 0): ?>
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <ul class="divide-y divide-gray-100">
                        <?php while($row = $cart_items->fetch_assoc()): 
                            $grand_total += $row['total_price'];
                        ?>
                        <li class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center gap-6">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="w-20 h-20 rounded-lg object-cover bg-gray-100">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-bold mr-2">
                                            <?php echo $row['days']; ?> Days
                                        </span>
                                        Rate: GH₵<?php echo $row['price_per_day']; ?>/day
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900 mb-2">GH₵<?php echo number_format($row['total_price'], 2); ?></p>
                                <a href="cart_remove.php?id=<?php echo $row['cart_id']; ?>" class="text-red-500 hover:text-red-700 text-sm font-medium flex items-center justify-end gap-1">
                                    <i class='bx bx-trash'></i> Remove
                                </a>
                            </div>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>
                    
                    <div class="flex justify-between items-center mb-4 text-gray-600">
                        <span>Subtotal</span>
                        <span>GH₵<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-6 text-gray-600">
                        <span>Service Fee (5%)</span>
                        <span>GH₵<?php echo number_format($grand_total * 0.05, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-6 text-gray-600">
                        <span>Insurance (10%) <i class='bx bx-info-circle text-xs' title="Refundable if item returned in good condition"></i></span>
                        <span>GH₵<?php echo number_format($grand_total * 0.10, 2); ?></span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6 flex justify-between items-center mb-8">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-primary-600">GH₵<?php echo number_format($grand_total * 1.15, 2); ?></span>
                    </div>

                    <form action="cart_checkout.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Payment Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 transition">
                                    <input type="radio" name="payment_method" value="cash" checked class="text-primary-600 focus:ring-primary-500">
                                    <span class="ml-3 font-medium text-gray-900">Cash on Delivery</span>
                                    <i class='bx bx-money ml-auto text-xl text-gray-400'></i>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 transition">
                                    <input type="radio" name="payment_method" value="momo" class="text-primary-600 focus:ring-primary-500">
                                    <span class="ml-3 font-medium text-gray-900">MTN Mobile Money</span>
                                    <span class="ml-auto text-yellow-600 font-bold text-xs uppercase">MTN</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-black text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-1">
                            Checkout Now
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center text-xs text-gray-400">
                        <p class="flex items-center justify-center gap-1"><i class='bx bx-lock-alt'></i> Secure Checkout</p>
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="bg-gray-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                <i class='bx bx-cart text-4xl text-gray-300'></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
            <a href="items_browse.php" class="bg-primary-500 text-white px-8 py-3 rounded-full font-bold hover:bg-primary-600 transition shadow-md">
                Start Browsing
            </a>
        </div>
    <?php endif; ?>

</div>

<?php require 'layout_footer.php'; ?>