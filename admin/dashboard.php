<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'farmer'");
$total_farmers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM experts");
$total_experts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$total_products = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM input_supplies");
$total_supplies = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM extensive_services");
$total_services = $stmt->fetch()['total'];

// Get recent activities
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
$recent_products = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM service_requests ORDER BY created_at DESC LIMIT 5");
$recent_requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DigiFarm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--black);
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
            color: var(--green);
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
            background: var(--green);
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-gray);
            font-weight: 500;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .action-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .action-card h3 {
            color: var(--green);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: var(--green);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 1rem;
        }
        
        .action-btn:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }
        
        .recent-activities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        .activity-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .activity-card h3 {
            color: var(--green);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .logout-btn {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fi fi-rs-plant-care"></i> DigiFarm</h2>
                <p>Admin Dashboard</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fi fi-rs-home"></i>
                    Dashboard
                </a>
                <a href="manage-experts.php" class="nav-item">
                    <i class="fi fi-rs-user-graduate"></i>
                    Manage Experts
                </a>
                <a href="manage-supplies.php" class="nav-item">
                    <i class="fi fi-rs-tools"></i>
                    Manage Supplies
                </a>
                <a href="manage-services.php" class="nav-item">
                    <i class="fi fi-rs-settings"></i>
                    Manage Services
                </a>
                <a href="manage-farmers.php" class="nav-item">
                    <i class="fi fi-rs-users"></i>
                    Manage Farmers
                </a>
                <a href="manage-products.php" class="nav-item">
                    <i class="fi fi-rs-shopping-cart"></i>
                    Manage Products
                </a>
                <a href="reports.php" class="nav-item">
                    <i class="fi fi-rs-chart-line"></i>
                    Reports
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
                <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p>Manage the DigiFarm platform and monitor all activities</p>
            </div>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_farmers; ?></div>
                    <div class="stat-label">Registered Farmers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_experts; ?></div>
                    <div class="stat-label">Expert Advisors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_products; ?></div>
                    <div class="stat-label">Products Listed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_supplies; ?></div>
                    <div class="stat-label">Input Supplies</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_services; ?></div>
                    <div class="stat-label">Services Available</div>
                </div>
            </div>
            
            <!-- Admin Actions -->
            <div class="admin-actions">
                <div class="action-card">
                    <h3><i class="fi fi-rs-user-graduate"></i> Add Expert</h3>
                    <p>Add new agricultural experts to the platform</p>
                    <a href="add-expert.php" class="action-btn">Add Expert</a>
                </div>
                
                <div class="action-card">
                    <h3><i class="fi fi-rs-tools"></i> Add Supply</h3>
                    <p>Add new farming tools, fertilizers, or seeds</p>
                    <a href="add-supply.php" class="action-btn">Add Supply</a>
                </div>
                
                <div class="action-card">
                    <h3><i class="fi fi-rs-settings"></i> Add Service</h3>
                    <p>Add new extensive services for farmers</p>
                    <a href="add-service.php" class="action-btn">Add Service</a>
                </div>
                
                <div class="action-card">
                    <h3><i class="fi fi-rs-chart-line"></i> View Reports</h3>
                    <p>Generate and view platform statistics</p>
                    <a href="reports.php" class="action-btn">View Reports</a>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="recent-activities">
                <div class="activity-card">
                    <h3><i class="fi fi-rs-shopping-cart"></i> Recent Products</h3>
                    <?php if (empty($recent_products)): ?>
                        <p>No products added yet.</p>
                    <?php else: ?>
                        <?php foreach ($recent_products as $product): ?>
                            <div class="activity-item">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p>$<?php echo number_format($product['price'], 2); ?></p>
                                <small>Added: <?php echo date('M d, Y H:i', strtotime($product['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="activity-card">
                    <h3><i class="fi fi-rs-settings"></i> Recent Service Requests</h3>
                    <?php if (empty($recent_requests)): ?>
                        <p>No service requests yet.</p>
                    <?php else: ?>
                        <?php foreach ($recent_requests as $request): ?>
                            <div class="activity-item">
                                <h4>Request #<?php echo $request['id']; ?></h4>
                                <p><?php echo htmlspecialchars(substr($request['request_details'], 0, 50)) . '...'; ?></p>
                                <small>Requested: <?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
