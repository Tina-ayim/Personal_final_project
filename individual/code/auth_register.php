<?php
require_once 'config_db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $error = "Invalid email format. Please use a valid domain (e.g., .com, .net).";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 alphanumeric characters.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 8 characters with letters and numbers.";
    } else {
        
        $check = $conn->prepare("SELECT id FROM user WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email or Username already exists.";
        } else {
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $username;
                $_SESSION['user_email'] = $email;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Com.Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen">

    <!-- LEFT SIDE: Image (Alternated position logic could be applied here if desired, but consistency is often better. Let's keep Left Image for branding) -->
    <div class="hidden lg:flex w-1/2 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1522273400909-fd1a8f77637e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-emerald-900 bg-opacity-40 flex items-center justify-center">
            <div class="text-white text-center p-12">
                <h1 class="text-5xl font-bold mb-6">Join Our Community</h1>
                <p class="text-xl font-light">Start your journey with us today. Rent or list properties with confidence.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account? <a href="auth_login.php" class="font-medium text-emerald-600 hover:text-emerald-500">Sign in</a>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4" role="alert">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="auth_register.php" method="POST">
                
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="username" class="sr-only">Full Name</label>
                        <div class="relative">
                            <i class='bx bx-user absolute left-3 top-3 text-gray-400 text-xl'></i>
                            <input id="username" name="username" type="text" autocomplete="name" required class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Username (3-20 chars)">
                        </div>
                    </div>
                
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
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Password (8+ chars, letter & number)">
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the <a href="#" class="text-emerald-600 hover:text-emerald-500">Terms of Service</a> and <a href="#" class="text-emerald-600 hover:text-emerald-500">Privacy Policy</a>
                    </label>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out shadow-lg transform hover:-translate-y-0.5">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class='bx bx-user-plus text-emerald-500 group-hover:text-emerald-400 text-xl'></i>
                        </span>
                        Sign Up
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div>
                        <a href="#" onclick="alert('Social login is under development. Please login/signup with your email manually.'); return false;" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-facebook text-xl text-blue-600'></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" onclick="alert('Social login is under development. Please login/signup with your email manually.'); return false;" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-google text-xl text-red-500'></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" onclick="alert('Social login is under development. Please login/signup with your email manually.'); return false;" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class='bx bxl-linkedin text-xl text-blue-700'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>