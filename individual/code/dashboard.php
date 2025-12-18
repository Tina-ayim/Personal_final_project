<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login(); 

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];


$borrowed = $conn->query("SELECT COUNT(*) as c FROM rentals WHERE renter_id = $user_id")->fetch_assoc()['c'];
$listed = $conn->query("SELECT COUNT(*) as c FROM items WHERE owner_id = $user_id")->fetch_assoc()['c'];
$lent_res = $conn->query("SELECT COUNT(*) as c FROM rentals JOIN items ON rentals.item_id = items.id WHERE items.owner_id = $user_id");
$lent = ($lent_res) ? $lent_res->fetch_assoc()['c'] : 0;
$earnings = $conn->query("SELECT SUM(total_cost) as e FROM rentals JOIN items ON rentals.item_id = items.id WHERE items.owner_id = $user_id")->fetch_assoc()['e'];


$latest_items = $conn->query("SELECT * FROM items ORDER BY created_at DESC LIMIT 4");

require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Welcome Section -->
    <div class="mb-8">
        <?php
            $hour = date('H');
            if ($hour < 12) {
                $greeting = "Good Morning";
            } elseif ($hour < 18) {
                $greeting = "Good Afternoon";
            } else {
                $greeting = "Good Evening";
            }
        ?>
        <h1 class="text-2xl font-bold text-gray-900"><?php echo $greeting . ', ' . htmlspecialchars($user_name); ?>! 👋</h1>
        <p class="text-gray-500">Here's what's happening with your rentals today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Items Borrowed</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $borrowed; ?></h3>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg">
                <i class='bx bx-shopping-bag text-2xl text-blue-600'></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Items Lent</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $lent; ?></h3>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <i class='bx bx-store-alt text-2xl text-green-600'></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Earnings</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">GH₵<?php echo number_format($earnings ?? 0, 2); ?></h3>
            </div>
            <div class="p-3 bg-yellow-50 rounded-lg">
                <i class='bx bx-dollar-circle text-2xl text-yellow-600'></i>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-12">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Activity Balance (Borrowed vs. Lent)</h3>
        <p class="text-sm text-gray-500 mb-4">You are here &#9679;</p>
        <div class="w-full h-64">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('balanceChart').getContext('2d');
        const balanceChart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Your Activity Balance',
                    data: [{
                        x: <?php echo $borrowed; ?>,
                        y: <?php echo $lent; ?>
                    }],
                    backgroundColor: 'rgba(16, 185, 129, 1)',
                    pointRadius: 8,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: true, text: 'Items Borrowed' },
                        min: 0,
                        suggestedMax: 10,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        title: { display: true, text: 'Items Lent' },
                        min: 0,
                        suggestedMax: 10,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Borrowed: ${context.parsed.x}, Lent: ${context.parsed.y}`;
                            }
                        }
                    },
                    legend: { display: false }
                }
            }
        });
    </script>

    <!-- Fresh Listings -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Fresh Listings Nearby</h2>
        <a href="items_browse.php" class="text-primary-600 font-semibold hover:text-primary-700 text-sm">View All &rarr;</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php while($row = $latest_items->fetch_assoc()): ?>
        <a href="items_view.php?id=<?php echo $row['id']; ?>" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition duration-300">
            <div class="aspect-w-4 aspect-h-3 bg-gray-200 relative">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
            </div>
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-gray-900 truncate pr-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">GH₵<?php echo $row['price_per_day']; ?></span>
                </div>
                <!-- Optional: Add user/location here -->
            </div>
        </a>
        <?php endwhile; ?>
    </div>

</div>

<?php require 'layout_footer.php'; ?>