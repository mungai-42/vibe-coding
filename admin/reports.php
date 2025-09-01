<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';

// Fetch system statistics
try {
    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE user_type = 'farmer'");
    $total_users = $stmt->fetch()['total_users'];
    
    // Count total products
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
    $total_products = $stmt->fetch()['total_products'];
    
    // Count total experts
    $stmt = $pdo->query("SELECT COUNT(*) as total_experts FROM experts");
    $total_experts = $stmt->fetch()['total_experts'];
    
    // Count total supplies
    $stmt = $pdo->query("SELECT COUNT(*) as total_supplies FROM input_supplies");
    $total_supplies = $stmt->fetch()['total_supplies'];
    
    // Count total services
    $stmt = $pdo->query("SELECT COUNT(*) as total_services FROM extensive_services");
    $total_services = $stmt->fetch()['total_services'];
    
    // Count total expert enquiries
    $stmt = $pdo->query("SELECT COUNT(*) as total_enquiries FROM expert_enquiries");
    $total_enquiries = $stmt->fetch()['total_enquiries'];
    
    // Count total service requests
    $stmt = $pdo->query("SELECT COUNT(*) as total_service_requests FROM service_requests");
    $total_service_requests = $stmt->fetch()['total_service_requests'];
    
    // Count total supply requests
    $stmt = $pdo->query("SELECT COUNT(*) as total_supply_requests FROM supply_requests");
    $total_supply_requests = $stmt->fetch()['total_supply_requests'];
    
    // Recent activities
    $stmt = $pdo->query("
        SELECT 'product' as type, name, created_at, 'New product added' as description 
        FROM products 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_products = $stmt->fetchAll();
    
    $stmt = $pdo->query("
        SELECT 'user' as type, full_name as name, created_at, 'New farmer registered' as description 
        FROM users 
        WHERE user_type = 'farmer' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_users = $stmt->fetchAll();
    
    // Combine recent activities
    $recent_activities = array_merge($recent_products, $recent_users);
    usort($recent_activities, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recent_activities = array_slice($recent_activities, 0, 10);
    
} catch (PDOException $e) {
    $error = 'Error fetching statistics: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - DigiFarm Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .reports-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #65b741 0%, #4a8c2f 100%);
            padding: 20px;
        }
        
        .reports-content {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .reports-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .reports-header h1 {
            color: var(--green);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .reports-header p {
            color: #666;
            font-size: 1.1rem;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        .reports-section {
            margin-bottom: 2rem;
        }
        
        .reports-section h2 {
            color: var(--green);
            border-bottom: 2px solid var(--green);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .activities-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .activity-info h4 {
            margin: 0;
            color: var(--green);
            font-size: 1rem;
        }
        
        .activity-info p {
            margin: 0.2rem 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .activity-time {
            color: #999;
            font-size: 0.8rem;
        }
        
        .no-activities {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        .no-activities i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="reports-container">
        <a href="dashboard.php" class="back-link">
            <i class="fi fi-rs-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="reports-content">
            <div class="reports-header">
                <h1><i class="fi fi-rs-chart"></i> System Reports</h1>
                <p>Overview of DigiFarm system statistics and activities</p>
            </div>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-user"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Farmers</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-shopping-cart"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_products; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-user-graduate"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_experts; ?></div>
                    <div class="stat-label">Total Experts</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-tools"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_supplies; ?></div>
                    <div class="stat-label">Total Supplies</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-settings"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_services; ?></div>
                    <div class="stat-label">Total Services</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fi fi-rs-chat"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_enquiries + $total_service_requests + $total_supply_requests; ?></div>
                    <div class="stat-label">Total Requests</div>
                </div>
            </div>
            
            <div class="reports-section">
                <h2><i class="fi fi-rs-clock"></i> Recent Activities</h2>
                
                <?php if (empty($recent_activities)): ?>
                    <div class="no-activities">
                        <i class="fi fi-rs-clock"></i>
                        <h3>No Recent Activities</h3>
                        <p>No activities have been recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="activities-list">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php if ($activity['type'] == 'product'): ?>
                                        <i class="fi fi-rs-shopping-cart"></i>
                                    <?php else: ?>
                                        <i class="fi fi-rs-user"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-info">
                                    <h4><?php echo htmlspecialchars($activity['name']); ?></h4>
                                    <p><?php echo $activity['description']; ?></p>
                                </div>
                                <div class="activity-time">
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="reports-section">
                <h2><i class="fi fi-rs-chart"></i> Request Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fi fi-rs-user-graduate"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_enquiries; ?></div>
                        <div class="stat-label">Expert Enquiries</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fi fi-rs-settings"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_service_requests; ?></div>
                        <div class="stat-label">Service Requests</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fi fi-rs-tools"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_supply_requests; ?></div>
                        <div class="stat-label">Supply Requests</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
