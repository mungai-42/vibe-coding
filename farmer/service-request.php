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
    $service_id = $_POST['service_id'] ?? '';
    $request_details = trim($_POST['request_details'] ?? '');
    $preferred_date = $_POST['preferred_date'] ?? '';
    
    if (empty($service_id) || empty($request_details)) {
        $error = 'Service and request details are required';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, service_id, request_details, preferred_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $service_id, $request_details, $preferred_date ?: null]);
            $message = 'Service request submitted successfully!';
        } catch (PDOException $e) {
            $error = 'Error submitting request. Please try again.';
        }
    }
}

// Get available services
$services = [];
try {
    $stmt = $pdo->query("SELECT * FROM extensive_services ORDER BY name");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error loading services.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Services - DigiFarm</title>
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
            min-height: 120px;
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
        
        .service-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #4CAF50;
        }
        
        .service-info h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .service-info p {
            color: #666;
            margin: 2px 0;
        }
        
        .service-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .veterinary { background: #e3f2fd; color: #1976d2; }
        .agronomy { background: #e8f5e8; color: #388e3c; }
        .machinery { background: #fff3e0; color: #f57c00; }
        .other { background: #f3e5f5; color: #7b1fa2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request Services</h1>
            <p>Get professional farming services and support</p>
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
                    <label for="service_id">Select Service:</label>
                    <select name="service_id" id="service_id" required>
                        <option value="">Choose a service...</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['id']; ?>">
                                <?php echo htmlspecialchars($service['name']); ?> 
                                <span class="service-type <?php echo $service['service_type']; ?>">
                                    <?php echo ucfirst($service['service_type']); ?>
                                </span>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="request_details">Service Details:</label>
                    <textarea name="request_details" id="request_details" placeholder="Describe the service you need, your requirements, and any specific details..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="preferred_date">Preferred Date (Optional):</label>
                    <input type="date" name="preferred_date" id="preferred_date" min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <button type="submit" class="btn">Submit Request</button>
            </form>
            
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
