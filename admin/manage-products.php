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

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$product_id]);
        if ($result) {
            $message = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch all products with farmer information
try {
    $stmt = $pdo->query("
        SELECT p.*, u.full_name as farmer_name, u.phone as farmer_phone 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error fetching products: ' . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - DigiFarm Admin</title>
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
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
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
        
        .product-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .product-icon {
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
        
        .product-info h3 {
            color: var(--green);
            margin: 0;
            font-size: 1.2rem;
        }
        
        .product-info p {
            color: #666;
            margin: 0.2rem 0 0 0;
            font-size: 0.9rem;
        }
        
        .product-details {
            margin-bottom: 1rem;
        }
        
        .product-details p {
            margin: 0.3rem 0;
            color: #555;
        }
        
        .product-details strong {
            color: var(--green);
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--green);
            margin-bottom: 0.5rem;
        }
        
        .product-quantity {
            background: #e9ecef;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .farmer-info {
            background: #d1ecf1;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .farmer-info h4 {
            color: var(--green);
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
        }
        
        .product-actions {
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
    <div class="manage-container">
        <a href="dashboard.php" class="back-link">
            <i class="fi fi-rs-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="manage-content">
            <div class="manage-header">
                <h1><i class="fi fi-rs-shopping-cart"></i> Manage Products</h1>
                <p>View, edit, and manage marketplace products</p>
                <a href="../farmer/add-product.php" class="btn-add">
                    <i class="fi fi-rs-plus"></i> Add New Product
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fi fi-rs-shopping-cart"></i>
                    <h3>No Products Found</h3>
                    <p>No products have been added to the marketplace yet.</p>
                    <a href="../farmer/add-product.php" class="btn-add">Add Product</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-header">
                                <div class="product-icon">
                                    <i class="fi fi-rs-plant-care"></i>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($product['category'] ?? 'General'); ?></p>
                                </div>
                            </div>
                            
                            <div class="product-price">
                                $<?php echo number_format($product['price'], 2); ?>
                            </div>
                            
                            <div class="product-quantity">
                                <strong>Quantity:</strong> <?php echo htmlspecialchars($product['quantity']); ?> available
                            </div>
                            
                            <div class="farmer-info">
                                <h4><i class="fi fi-rs-user"></i> Seller Information</h4>
                                <p><strong>Farmer:</strong> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($product['farmer_contact']); ?></p>
                            </div>
                            
                            <div class="product-details">
                                <?php if ($product['description']): ?>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                                <?php endif; ?>
                                <p><strong>Added:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?></p>
                            </div>
                            
                            <div class="product-actions">
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                    <i class="fi fi-rs-edit"></i> Edit
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn-delete">
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
