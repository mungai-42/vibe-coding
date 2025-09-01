<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch all products from database
try {
    $stmt = $pdo->query("
        SELECT p.*, u.full_name as farmer_name, u.phone as farmer_phone 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching products: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - DigiFarm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <style>
        .marketplace-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #65b741 0%, #4a8c2f 100%);
            padding: 20px;
        }
        
        .marketplace-content {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .marketplace-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .marketplace-header h1 {
            color: var(--green);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .marketplace-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .product-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            background: #ddd;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
        
        .product-info h3 {
            color: var(--green);
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .product-details {
            margin-bottom: 1rem;
        }
        
        .product-details p {
            margin: 0.3rem 0;
            color: #666;
        }
        
        .product-description {
            color: #555;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .contact-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .contact-info h4 {
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .btn-contact {
            background: var(--green);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .btn-contact:hover {
            background: var(--dark-green);
        }
        
        .btn-add-product {
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
        
        .btn-add-product:hover {
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
        
        .no-products {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-products i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="marketplace-container">
        <a href="../farmer/dashboard.php" class="back-link">
            <i class="fi fi-rs-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="marketplace-content">
            <div class="marketplace-header">
                <h1><i class="fi fi-rs-shopping-cart"></i> DigiFarm Marketplace</h1>
                <p>Browse and buy agricultural products from fellow farmers</p>
                <a href="add-product.php" class="btn-add-product">
                    <i class="fi fi-rs-plus"></i> Add Your Product
                </a>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fi fi-rs-shopping-cart"></i>
                    <h3>No Products Available</h3>
                    <p>Be the first to add a product to the marketplace!</p>
                    <a href="add-product.php" class="btn-add-product">Add Product</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if ($product['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <i class="fi fi-rs-plant-care" style="font-size: 3rem;"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                
                                <div class="product-details">
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? 'General'); ?></p>
                                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($product['quantity']); ?> available</p>
                                </div>
                                
                                <?php if ($product['description']): ?>
                                    <div class="product-description">
                                        <?php echo htmlspecialchars($product['description']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="contact-info">
                                    <h4><i class="fi fi-rs-user"></i> Seller Information</h4>
                                    <p><strong>Farmer:</strong> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($product['farmer_contact']); ?></p>
                                </div>
                                
                                <a href="tel:<?php echo htmlspecialchars($product['farmer_contact']); ?>" class="btn-contact">
                                    <i class="fi fi-rs-phone"></i> Contact Seller
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
