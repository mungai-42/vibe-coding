<?php
// Database configuration
$host = 'localhost';
$dbname = 'digifarm_db';
$username = 'root';
$password = 'Root@123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}0

// Create tables if they don't exist
function createTables($pdo) {
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        user_type ENUM('farmer', 'admin') DEFAULT 'farmer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Experts table
    $sql = "CREATE TABLE IF NOT EXISTS experts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        contact VARCHAR(20) NOT NULL,
        specialization VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        years_experience INT,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        farmer_id INT NOT NULL,
        farmer_contact VARCHAR(20) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        category VARCHAR(50),
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Input supplies table
    $sql = "CREATE TABLE IF NOT EXISTS input_supplies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category ENUM('tools', 'fertilizers', 'seeds') NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        supplier VARCHAR(100),
        contact VARCHAR(20),
        stock_quantity INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Extensive services table
    $sql = "CREATE TABLE IF NOT EXISTS extensive_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        contact VARCHAR(20) NOT NULL,
        service_type ENUM('veterinary', 'agronomy', 'machinery', 'other') NOT NULL,
        description TEXT,
        location VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Expert enquiries table
    $sql = "CREATE TABLE IF NOT EXISTS expert_enquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        expert_id INT NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('pending', 'responded', 'closed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Service requests table
    $sql = "CREATE TABLE IF NOT EXISTS service_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        service_id INT NOT NULL,
        request_details TEXT NOT NULL,
        preferred_date DATE,
        status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (service_id) REFERENCES extensive_services(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Supply requests table
    $sql = "CREATE TABLE IF NOT EXISTS supply_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        supply_id INT NOT NULL,
        quantity INT NOT NULL,
        request_details TEXT,
        status ENUM('pending', 'approved', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (supply_id) REFERENCES input_supplies(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
}

// Initialize tables
createTables($pdo);
?>
