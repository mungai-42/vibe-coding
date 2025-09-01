<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($full_name) || empty($email)) {
        $error = 'Full name and email are required';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $result = $stmt->execute([$full_name, $email, $phone, $address, $user_id]);
            
            if ($result) {
                $message = 'Profile updated successfully!';
                $_SESSION['full_name'] = $full_name;
            } else {
                $error = 'Failed to update profile';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error = 'Error fetching user data: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Profile - DigiFarm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .profile-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #65b741 0%, #4a8c2f 100%);
            padding: 20px;
        }
        
        .profile-content {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .profile-header h1 {
            color: var(--green);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .profile-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: var(--green);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--black);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--green);
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .btn-update {
            background: var(--green);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        
        .btn-update:hover {
            background: var(--dark-green);
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-card h3 {
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <a href="dashboard.php" class="back-link">
            <i class="fi fi-rs-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="profile-content">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fi fi-rs-user"></i>
                </div>
                <h1><i class="fi fi-rs-user"></i> Farmer Profile</h1>
                <p>Manage your account information and settings</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="user_type">Account Type</label>
                    <input type="text" id="user_type" value="<?php echo ucfirst($user['user_type']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="created_at">Member Since</label>
                    <input type="text" id="created_at" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" readonly>
                </div>
                
                <button type="submit" class="btn-update">
                    <i class="fi fi-rs-check"></i> Update Profile
                </button>
            </form>
            
            <div class="profile-stats">
                <div class="stat-card">
                    <h3>Account Status</h3>
                    <p>Active</p>
                </div>
                <div class="stat-card">
                    <h3>User Type</h3>
                    <p><?php echo ucfirst($user['user_type']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Member Since</h3>
                    <p><?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
