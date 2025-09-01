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
    $expert_id = $_POST['expert_id'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    if (empty($expert_id) || empty($subject) || empty($message_text)) {
        $error = 'All fields are required';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO expert_enquiries (user_id, expert_id, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $expert_id, $subject, $message_text]);
            $message = 'Expert enquiry submitted successfully!';
        } catch (PDOException $e) {
            $error = 'Error submitting enquiry. Please try again.';
        }
    }
}

// Get available experts
$experts = [];
try {
    $stmt = $pdo->query("SELECT * FROM experts ORDER BY name");
    $experts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error loading experts.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Expert Insights - DigiFarm</title>
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
        
        .expert-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #4CAF50;
        }
        
        .expert-info h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .expert-info p {
            color: #666;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request Expert Insights</h1>
            <p>Get professional advice from agricultural experts</p>
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
                    <label for="expert_id">Select Expert:</label>
                    <select name="expert_id" id="expert_id" required>
                        <option value="">Choose an expert...</option>
                        <?php foreach ($experts as $expert): ?>
                            <option value="<?php echo $expert['id']; ?>">
                                <?php echo htmlspecialchars($expert['name']); ?> - <?php echo htmlspecialchars($expert['specialization']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" name="subject" id="subject" placeholder="Brief description of your enquiry" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Detailed Message:</label>
                    <textarea name="message" id="message" placeholder="Describe your farming issue or question in detail..." required></textarea>
                </div>
                
                <button type="submit" class="btn">Submit Enquiry</button>
            </form>
            
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
