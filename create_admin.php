<?php
// Script to create admin user if it doesn't exist
require_once 'config/database.php';

echo "<h2>Create Admin User</h2>";

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'><strong>Admin user already exists!</strong></p>";
        echo "<p>Username: " . $admin['username'] . "</p>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        echo "<p>User Type: " . $admin['user_type'] . "</p>";
        echo "<p><strong>You can now login with:</strong></p>";
        echo "<ul>";
        echo "<li>Username: admin</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
    } else {
        // Create admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, user_type) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute(['admin', 'admin@digifarm.com', $hashed_password, 'System Administrator', '+1234567890', 'admin']);
        
        if ($result) {
            echo "<p style='color: green;'><strong>Admin user created successfully!</strong></p>";
            echo "<p>Username: admin</p>";
            echo "<p>Email: admin@digifarm.com</p>";
            echo "<p>Password: admin123</p>";
            echo "<p>User Type: admin</p>";
            echo "<p><strong>You can now login to the admin dashboard!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>Failed to create admin user!</strong></p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration.</p>";
}

echo "<br><br>";
echo "<a href='login.php' style='background: #65b741; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
echo "&nbsp;&nbsp;";
echo "<a href='test_admin.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Admin Login</a>";
?>
