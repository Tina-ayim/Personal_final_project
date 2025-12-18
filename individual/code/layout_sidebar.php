<?php

$is_logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="brand">
        <h2 style="color:white; padding-left:10px;">Com.Rental</h2>
    </div>
    
    <ul class="nav-links">
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class='bx bxs-home'></i> Home</a>
        </li>
        <li><a href="items_browse.php" class="<?php echo ($current_page == 'items_browse.php') ? 'active' : ''; ?>">
            <i class='bx bx-search'></i> Browse Items</a>
        </li>

        <?php if ($is_logged_in): ?>
            
            <li><a href="items_manage.php" class="<?php echo ($current_page == 'items_manage.php') ? 'active' : ''; ?>">
                <i class='bx bx-store'></i> My Listings</a>
            </li>
            <li><a href="rentals_history.php" class="<?php echo ($current_page == 'rentals_history.php') ? 'active' : ''; ?>">
                <i class='bx bx-shopping-bag'></i> My Rentals</a>
            </li>
            <li><a href="cart_view.php" class="<?php echo ($current_page == 'cart_view.php') ? 'active' : ''; ?>">
                <i class='bx bx-cart'></i> Cart</a>
            </li>
            <li><a href="user_profile.php" class="<?php echo ($current_page == 'user_profile.php') ? 'active' : ''; ?>">
                <i class='bx bx-user'></i> Profile</a>
            </li>
            <li><a href="user_settings.php" class="<?php echo ($current_page == 'user_settings.php') ? 'active' : ''; ?>">
                <i class='bx bx-cog'></i> Settings</a>
            </li>
            <li><a href="auth_logout.php" style="color:#ff6b6b;">
                <i class='bx bx-log-out'></i> Logout</a>
            </li>

        <?php else: ?>
            
            <li style="margin-top: auto;">
                <a href="auth_login.php" style="background: rgba(255,255,255,0.1); color: white;">
                    <i class='bx bx-log-in'></i> Login
                </a>
            </li>

            <li style="margin-top: auto;">
                <a href="auth_login.php" style="background: rgba(255,255,255,0.1); color: white;">
                    <i class='bx bx-log-in'></i> Register
                </a>
            </li>

        <?php endif; ?>
    </ul>
</div>