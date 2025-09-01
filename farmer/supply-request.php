<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supply_id = $_POST['supply_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $request_details = trim($_POST['request_details'] ?? '');
    
    if (empty($supply_id) || empty($quantity) || $quantity <= 0) {
        $error = 'Please select a supply and specify a valid quantity';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO supply_requests (user_id, supply_id, quantity, request_details) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $supply_id, $quantity, $request_details]);
            $message = 'Supply request submitted successfully!';
        } catch (PDOException $e) {
            $error = 'Error submitting request. Please try again.';
        }
    }
}

// Get available supplies
$supplies = [];
try {
    $stmt = $pdo->query("SELECT * FROM input_supplies WHERE stock_quantity > 0 ORDER BY category, name");
    $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error loading supplies.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Input Supplies - DigiFarm</title>
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
        
        .supply-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #4CAF50;
        }
        
        .supply-info h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .supply-info p {
            color: #666;
            margin: 2px 0;
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .tools { background: #e3f2fd; color: #1976d2; }
        .fertilizers { background: #e8f5e8; color: #388e3c; }
        .seeds { background: #fff3e0; color: #f57c00; }
        
        .price-info {
            color: #4CAF50;
            font-weight: 600;
        }
        
        .stock-info {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request Input Supplies</h1>
            <p>Order tools, fertilizers, and quality seeds for your farm</p>
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
                    <label for="supply_id">Select Supply:</label>
                    <select name="supply_id" id="supply_id" required>
                        <option value="">Choose a supply...</option>
                        <?php foreach ($supplies as $supply): ?>
                            <option value="<?php echo $supply['id']; ?>">
                                <?php echo htmlspecialchars($supply['name']); ?> 
                                <span class="category-badge <?php echo $supply['category']; ?>">
                                    <?php echo ucfirst($supply['category']); ?>
                                </span>
                                - $<?php echo number_format($supply['price'], 2); ?>
                                (Stock: <?php echo $supply['stock_quantity']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" placeholder="Enter quantity needed" required>
                </div>
                
                <div class="form-group">
                    <label for="request_details">Additional Details (Optional):</label>
                    <textarea name="request_details" id="request_details" placeholder="Any special requirements, delivery preferences, or additional notes..."></textarea>
                </div>
                
                <button type="submit" class="btn">Submit Request</button>
            </form>
            
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
