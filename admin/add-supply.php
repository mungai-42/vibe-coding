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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $supplier = trim($_POST['supplier'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    
    if (empty($name) || empty($category) || empty($price) || $price <= 0 || $stock_quantity < 0) {
        $error = 'Please fill in all required fields with valid values';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO input_supplies (name, category, price, description, supplier, contact, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $price, $description, $supplier, $contact, $stock_quantity]);
            $message = 'Supply added successfully!';
        } catch (PDOException $e) {
            $error = 'Error adding supply. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supply - DigiFarm Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        select, input, textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Add Supply</h1>
            <p>Add new input supplies to the system</p>
        </div>
        
        <div class="form-container">
            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Supply Name:</label>
                    <input type="text" name="name" id="name" placeholder="Enter supply name" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" required>
                        <option value="">Select category...</option>
                        <option value="tools">Tools</option>
                        <option value="fertilizers">Fertilizers</option>
                        <option value="seeds">Quality Seeds</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity:</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="0" placeholder="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" placeholder="Describe the supply, its features, usage instructions, etc..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="supplier">Supplier Name:</label>
                        <input type="text" name="supplier" id="supplier" placeholder="Supplier or manufacturer name">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Supplier Contact:</label>
                        <input type="text" name="contact" id="contact" placeholder="Phone or email">
                    </div>
                </div>
                
                <button type="submit" class="btn">Add Supply</button>
            </form>
            
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
