<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Get farmer's products
$stmt = $pdo->prepare("SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();

// Get farmer's requests
$stmt = $pdo->prepare("SELECT * FROM service_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$service_requests = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM supply_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$supply_requests = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM expert_enquiries WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$expert_enquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - DigiFarm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--green);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar-header h2 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
        }
        
        .sidebar-nav {
            padding: 2rem 0;
        }
        
        .nav-item {
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            background: #f5f5f5;
        }
        
        .dashboard-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .welcome-message {
            font-size: 2rem;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            color: var(--green);
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-btn {
            background: var(--green);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-btn:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }
        
        .list-item {
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .list-item:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .logout-btn {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fi fi-rs-plant-care"></i> DigiFarm</h2>
                <p>Farmer Dashboard</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fi fi-rs-home"></i>
                    Dashboard
                </a>
                <a href="marketplace.php" class="nav-item">
                    <i class="fi fi-rs-shopping-cart"></i>
                    My Products
                </a>
                <a href="expert-enquiry.php" class="nav-item">
                    <i class="fi fi-rs-user-graduate"></i>
                    Expert Consultation
                </a>
                <a href="service-request.php" class="nav-item">
                    <i class="fi fi-rs-settings"></i>
                    Service Requests
                </a>
                <a href="supply-request.php" class="nav-item">
                    <i class="fi fi-rs-tools"></i>
                    Input Supply
                </a>
                <a href="profile.php" class="nav-item">
                    <i class="fi fi-rs-user"></i>
                    Profile
                </a>
            </nav>
            
            <a href="../logout.php" class="logout-btn">
                <i class="fi fi-rs-sign-out"></i>
                Logout
            </a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h1 class="welcome-message">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p>Manage your farm activities and connect with experts</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add-product.php" class="action-btn">
                    <i class="fi fi-rs-plus"></i>
                    Add Product
                </a>
                <a href="expert-enquiry.php" class="action-btn">
                    <i class="fi fi-rs-user-graduate"></i>
                    Request Expert
                </a>
                <a href="service-request.php" class="action-btn">
                    <i class="fi fi-rs-settings"></i>
                    Request Service
                </a>
                <a href="supply-request.php" class="action-btn">
                    <i class="fi fi-rs-tools"></i>
                    Order Supplies
                </a>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- My Products -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fi fi-rs-shopping-cart"></i>
                        My Products
                    </div>
                    <?php if (empty($products)): ?>
                        <p>No products posted yet.</p>
                        <a href="add-product.php" class="action-btn" style="margin-top: 1rem;">Add First Product</a>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="list-item">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p>$<?php echo number_format($product['price'], 2); ?></p>
                                <small>Posted: <?php echo date('M d, Y', strtotime($product['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                        <a href="marketplace.php" style="color: var(--green); text-decoration: none; font-weight: 600;">View All Products →</a>
                    <?php endif; ?>
                </div>
                
                <!-- Service Requests -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fi fi-rs-settings"></i>
                        Service Requests
                    </div>
                    <?php if (empty($service_requests)): ?>
                        <p>No service requests yet.</p>
                        <a href="service-request.php" class="action-btn" style="margin-top: 1rem;">Request Service</a>
                    <?php else: ?>
                        <?php foreach ($service_requests as $request): ?>
                            <div class="list-item">
                                <h4>Service Request #<?php echo $request['id']; ?></h4>
                                <span class="status-badge status-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                                <small><?php echo date('M d, Y', strtotime($request['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                        <a href="service-request.php" style="color: var(--green); text-decoration: none; font-weight: 600;">View All Requests →</a>
                    <?php endif; ?>
                </div>
                
                <!-- Supply Requests -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fi fi-rs-tools"></i>
                        Supply Requests
                    </div>
                    <?php if (empty($supply_requests)): ?>
                        <p>No supply requests yet.</p>
                        <a href="supply-request.php" class="action-btn" style="margin-top: 1rem;">Order Supplies</a>
                    <?php else: ?>
                        <?php foreach ($supply_requests as $request): ?>
                            <div class="list-item">
                                <h4>Supply Request #<?php echo $request['id']; ?></h4>
                                <span class="status-badge status-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                                <small><?php echo date('M d, Y', strtotime($request['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                        <a href="supply-request.php" style="color: var(--green); text-decoration: none; font-weight: 600;">View All Orders →</a>
                    <?php endif; ?>
                </div>
                
                <!-- Expert Enquiries -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fi fi-rs-user-graduate"></i>
                        Expert Enquiries
                    </div>
                    <?php if (empty($expert_enquiries)): ?>
                        <p>No expert enquiries yet.</p>
                        <a href="expert-enquiry.php" class="action-btn" style="margin-top: 1rem;">Ask Expert</a>
                    <?php else: ?>
                        <?php foreach ($expert_enquiries as $enquiry): ?>
                            <div class="list-item">
                                <h4><?php echo htmlspecialchars($enquiry['subject']); ?></h4>
                                <span class="status-badge status-<?php echo $enquiry['status']; ?>">
                                    <?php echo ucfirst($enquiry['status']); ?>
                                </span>
                                <small><?php echo date('M d, Y', strtotime($enquiry['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                        <a href="expert-enquiry.php" style="color: var(--green); text-decoration: none; font-weight: 600;">View All Enquiries →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
