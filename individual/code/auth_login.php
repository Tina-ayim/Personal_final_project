<?php
require_once 'config_db.php';
require_once 'helpers_security.php'; 
require_once 'config_db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    check_rate_limit('login_attempt', 5, 300); 

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    
    if (empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password_hash, profile_image FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password_hash'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                
                
                if (isset($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s',  time() + (86400 * 30)); 
                    
                    $tok_stmt = $conn->prepare("UPDATE user SET remember_token = ?, remember_expiry = ? WHERE id = ?");
                    $tok_stmt->bind_param("ssi", $token, $expiry, $user['id']);
                    $tok_stmt->execute();
                    
                    setcookie('remember_token', $token, time() + (86400 * 30), "/");
                }
    
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Community Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen">

    <!-- LEFT SIDE: Image -->
    <div class="hidden lg:flex w-1/2 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-emerald-900 bg-opacity-40 flex items-center justify-center">
            <div class="text-white text-center p-12">
                <h1 class="text-5xl font-bold mb-6">Welcome Back</h1>
                <p class="text-xl font-light">Access your premium rental dashboard and manage your listings with ease.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Sign in to your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Or <a href="auth_register.php" class="font-medium text-emerald-600 hover:text-emerald-500">create a new account</a>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4" role="alert">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="auth_login.php" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="email" class="sr-only">Email address</label>
                        <div class="relative">
                            <i class='bx bx-envelope absolute left-3 top-3 text-gray-400 text-xl'></i>
                            <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Email address">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <i class='bx bx-lock-alt absolute left-3 top-3 text-gray-400 text-xl'></i>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Password">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="auth_forgot.php" class="font-medium text-emerald-600 hover:text-emerald-500">Forgot your password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out shadow-lg transform hover:-translate-y-0.5">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class='bx bx-lock-open text-emerald-500 group-hover:text-emerald-400 text-lg'></i>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-4 gap-3">
                    <div>
                        <a href="auth_social.php?provider=facebook" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-facebook text-xl text-blue-600'></i>
                        </a>
                    </div>
                    <div>
                        <a href="auth_social.php?provider=google" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-google text-xl text-red-500'></i>
                        </a>
                    </div>
                    <div>
                        <a href="auth_social.php?provider=linkedin" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-linkedin text-xl text-blue-700'></i>
                        </a>
                    </div>
                    <div>
                        <a href="auth_social.php?provider=instagram" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-instagram text-xl text-pink-600'></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-400">
                <a href="terms.php" class="hover:underline">Terms of Service</a> &bull; <a href="privacy.php" class="hover:underline">Privacy Policy</a>
            </div>
        </div>
    </div>

</body>
</html>