<?php
session_start();
require 'config_db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: auth_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$msg_type = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $bio = $_POST['bio'];
    $phone = $_POST['phone'];
    $avatar = $_POST['avatar'];
    
    
    $stmt = $conn->prepare("UPDATE user SET username = ?, bio = ?, phone = ?, profile_pic = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $fullname, $bio, $phone, $avatar, $user_id);
    
    if ($stmt->execute()) {
        $message = "Settings updated successfully!";
        $msg_type = "success";
    } else {
        $message = "Error updating settings: " . $conn->error;
        $msg_type = "error";
    }
    $stmt->close();
}



$sql = "SELECT * FROM user WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();


$avatar_url = !empty($user['profile_pic']) ? $user['profile_pic'] : "https://ui-avatars.com/api/?name=".$user['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .settings-container {
            background: white;
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 800px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--secondary); }
        .form-group input, .form-group textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-family: inherit;
        }
        .alert {
            padding: 15px; margin-bottom: 20px; border-radius: 8px;
        }
        .alert.success { background: #e0f2f1; color: var(--primary); border: 1px solid var(--primary); }
        .alert.error { background: #ffebee; color: #c62828; border: 1px solid #c62828; }
    </style>
</head>
<body>

    <?php include 'layout_sidebar.php'; ?>

    <div class="main-content">
        
        <div class="stat-card" style="padding:0; overflow:visible; position:relative; margin-bottom: 40px;">
            <div class="profile-hero" style="height: 120px;"></div>
            <img src="<?php echo $avatar_url; ?>" class="profile-avatar" style="width: 100px; height: 100px; bottom: -50px;">
            <div style="padding: 10px 40px 10px 180px; min-height: 80px; display: flex; align-items: center;">
                <div>
                    <h2 style="margin:0; font-size: 24px;"><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p style="color:#777; font-size: 14px;">Member since <?php echo date("M Y", strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <div class="settings-container">
            <h2 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Edit Profile</h2>

            <?php if($message): ?>
                <div class="alert <?php echo $msg_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="user_settings.php">
                
                <div class="form-group">
                    <label>Profile Picture (URL)</label>
                    <input type="text" name="avatar" value="<?php echo htmlspecialchars($user['profile_pic'] ?? ''); ?>" placeholder="Paste an image URL here...">
                    <small style="color:#888;">Tip: Use a URL from imgur or similar.</small>
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1 234 567 890">
                </div>

                <button type="submit" class="action-btn" style="width: 100%;">Save Changes</button>
            </form>
        </div>

    </div>

</body>
</html>