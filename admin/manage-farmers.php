<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$error = '';

// Handle farmer deletion
if (isset($_POST['delete_farmer'])) {
    $farmer_id = $_POST['farmer_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND user_type = 'farmer'");
        $result = $stmt->execute([$farmer_id]);
        if ($result) {
            $message = 'Farmer deleted successfully!';
        } else {
            $error = 'Failed to delete farmer';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch all farmers
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE user_type = 'farmer' ORDER BY created_at DESC");
    $farmers = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error fetching farmers: ' . $e->getMessage();
    $farmers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Farmers - DigiFarm Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .manage-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #65b741 0%, #4a8c2f 100%);
            padding: 20px;
        }
        
        .manage-content {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .manage-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .manage-header h1 {
            color: var(--green);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .manage-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .btn-add {
            background: var(--green);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: background 0.3s ease;
        }
        
        .btn-add:hover {
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
        
        .farmers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .farmer-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .farmer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .farmer-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .farmer-avatar {
            width: 60px;
            height: 60px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .farmer-info h3 {
            color: var(--green);
            margin: 0;
            font-size: 1.2rem;
        }
        
        .farmer-info p {
            color: #666;
            margin: 0.2rem 0 0 0;
            font-size: 0.9rem;
        }
        
        .farmer-details {
            margin-bottom: 1rem;
        }
        
        .farmer-details p {
            margin: 0.3rem 0;
            color: #555;
        }
        
        .farmer-details strong {
            color: var(--green);
        }
        
        .farmer-status {
            background: #d4edda;
            color: #155724;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .farmer-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .no-farmers {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-farmers i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="manage-container">
        <a href="dashboard.php" class="back-link">
            <i class="fi fi-rs-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="manage-content">
            <div class="manage-header">
                <h1><i class="fi fi-rs-user"></i> Manage Farmers</h1>
                <p>View, edit, and manage farmer accounts</p>
                <a href="../register.php" class="btn-add">
                    <i class="fi fi-rs-plus"></i> Add New Farmer
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (empty($farmers)): ?>
                <div class="no-farmers">
                    <i class="fi fi-rs-user"></i>
                    <h3>No Farmers Found</h3>
                    <p>No farmer accounts have been registered yet.</p>
                    <a href="../register.php" class="btn-add">Add Farmer</a>
                </div>
            <?php else: ?>
                <div class="farmers-grid">
                    <?php foreach ($farmers as $farmer): ?>
                        <div class="farmer-card">
                            <div class="farmer-header">
                                <div class="farmer-avatar">
                                    <i class="fi fi-rs-user"></i>
                                </div>
                                <div class="farmer-info">
                                    <h3><?php echo htmlspecialchars($farmer['full_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($farmer['username']); ?></p>
                                </div>
                            </div>
                            
                            <div class="farmer-status">
                                <strong>Status:</strong> Active
                            </div>
                            
                            <div class="farmer-details">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($farmer['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($farmer['phone'] ?? 'Not provided'); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($farmer['address'] ?? 'Not provided'); ?></p>
                                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($farmer['created_at'])); ?></p>
                            </div>
                            
                            <div class="farmer-actions">
                                <a href="edit-farmer.php?id=<?php echo $farmer['id']; ?>" class="btn-edit">
                                    <i class="fi fi-rs-edit"></i> Edit
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this farmer account? This action cannot be undone.');">
                                    <input type="hidden" name="farmer_id" value="<?php echo $farmer['id']; ?>">
                                    <button type="submit" name="delete_farmer" class="btn-delete">
                                        <i class="fi fi-rs-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
