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

// Handle supply deletion
if (isset($_POST['delete_supply'])) {
    $supply_id = $_POST['supply_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM input_supplies WHERE id = ?");
        $result = $stmt->execute([$supply_id]);
        if ($result) {
            $message = 'Supply deleted successfully!';
        } else {
            $error = 'Failed to delete supply';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch all supplies
try {
    $stmt = $pdo->query("SELECT * FROM input_supplies ORDER BY created_at DESC");
    $supplies = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error fetching supplies: ' . $e->getMessage();
    $supplies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplies - DigiFarm Admin</title>
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
        
        .supplies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .supply-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .supply-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .supply-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .supply-icon {
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
        
        .supply-info h3 {
            color: var(--green);
            margin: 0;
            font-size: 1.2rem;
        }
        
        .supply-info p {
            color: #666;
            margin: 0.2rem 0 0 0;
            font-size: 0.9rem;
        }
        
        .supply-details {
            margin-bottom: 1rem;
        }
        
        .supply-details p {
            margin: 0.3rem 0;
            color: #555;
        }
        
        .supply-details strong {
            color: var(--green);
        }
        
        .supply-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .supply-stock {
            background: #e9ecef;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .supply-actions {
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
        
        .no-supplies {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-supplies i {
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
                <h1><i class="fi fi-rs-tools"></i> Manage Supplies</h1>
                <p>View, edit, and manage input supplies</p>
                <a href="add-supply.php" class="btn-add">
                    <i class="fi fi-rs-plus"></i> Add New Supply
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (empty($supplies)): ?>
                <div class="no-supplies">
                    <i class="fi fi-rs-tools"></i>
                    <h3>No Supplies Found</h3>
                    <p>Add the first supply to get started!</p>
                    <a href="add-supply.php" class="btn-add">Add Supply</a>
                </div>
            <?php else: ?>
                <div class="supplies-grid">
                    <?php foreach ($supplies as $supply): ?>
                        <div class="supply-card">
                            <div class="supply-header">
                                <div class="supply-icon">
                                    <i class="fi fi-rs-tools"></i>
                                </div>
                                <div class="supply-info">
                                    <h3><?php echo htmlspecialchars($supply['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($supply['category']); ?></p>
                                </div>
                            </div>
                            
                            <div class="supply-price">
                                $<?php echo number_format($supply['price'], 2); ?>
                            </div>
                            
                            <div class="supply-stock">
                                <strong>Stock:</strong> <?php echo htmlspecialchars($supply['stock_quantity']); ?> available
                            </div>
                            
                            <div class="supply-details">
                                <p><strong>Supplier:</strong> <?php echo htmlspecialchars($supply['supplier']); ?></p>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($supply['contact']); ?></p>
                                <?php if ($supply['description']): ?>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($supply['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="supply-actions">
                                <a href="edit-supply.php?id=<?php echo $supply['id']; ?>" class="btn-edit">
                                    <i class="fi fi-rs-edit"></i> Edit
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this supply?');">
                                    <input type="hidden" name="supply_id" value="<?php echo $supply['id']; ?>">
                                    <button type="submit" name="delete_supply" class="btn-delete">
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
