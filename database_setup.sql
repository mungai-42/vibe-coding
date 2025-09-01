-- Step 1: Create the database
CREATE DATABASE IF NOT EXISTS digifarm_db;
USE digifarm_db;

-- Step 2: Create all tables

-- Users table (for farmers and admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    user_type ENUM('farmer', 'admin') DEFAULT 'farmer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Experts table (for expert advisory)
CREATE TABLE IF NOT EXISTS experts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    experience_years INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table (for marketplace)
CREATE TABLE IF NOT EXISTS products (
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
);

-- Input supplies table (for tools, fertilizers, seeds)
CREATE TABLE IF NOT EXISTS input_supplies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('tools', 'fertilizers', 'seeds') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    supplier VARCHAR(100),
    contact VARCHAR(20),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Extensive services table (for veterinary, agronomy, machinery services)
CREATE TABLE IF NOT EXISTS extensive_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    service_type ENUM('veterinary', 'agronomy', 'machinery', 'other') NOT NULL,
    description TEXT,
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Expert enquiries table (for farmer requests to experts)
CREATE TABLE IF NOT EXISTS expert_enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    expert_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'responded', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

-- Service requests table (for farmer requests for extensive services)
CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    request_details TEXT NOT NULL,
    preferred_date DATE,
    status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES extensive_services(id) ON DELETE CASCADE
);

-- Supply requests table (for farmer requests for input supplies)
CREATE TABLE IF NOT EXISTS supply_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    supply_id INT NOT NULL,
    quantity INT NOT NULL,
    request_details TEXT,
    status ENUM('pending', 'approved', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (supply_id) REFERENCES input_supplies(id) ON DELETE CASCADE
);

-- Step 3: Insert sample data for testing

-- Insert a default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, phone, user_type) VALUES 
('admin', 'admin@digifarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '+1234567890', 'admin');

-- Insert sample experts
INSERT INTO experts (name, contact, specialization, email, experience_years) VALUES 
('Dr. John Smith', '+1234567891', 'Crop Management', 'john.smith@agri.com', 15),
('Dr. Sarah Johnson', '+1234567892', 'Livestock Health', 'sarah.johnson@vet.com', 12),
('Prof. Michael Brown', '+1234567893', 'Soil Science', 'michael.brown@soil.com', 20),
('Dr. Lisa Davis', '+1234567894', 'Pest Control', 'lisa.davis@pest.com', 8);

-- Insert sample input supplies
INSERT INTO input_supplies (name, category, price, description, supplier, contact, stock_quantity) VALUES 
('Organic Fertilizer 50kg', 'fertilizers', 45.00, 'High-quality organic fertilizer for all crops', 'AgriSupply Co.', '+1234567895', 100),
('Tractor Plow', 'tools', 250.00, 'Heavy-duty tractor plow for large farms', 'FarmTools Inc.', '+1234567896', 5),
('Hybrid Corn Seeds', 'seeds', 15.00, 'High-yield hybrid corn seeds', 'SeedMaster Ltd.', '+1234567897', 200),
('Pesticide Sprayer', 'tools', 75.00, 'Manual pesticide sprayer', 'AgriTools Co.', '+1234567898', 25),
('NPK Fertilizer 25kg', 'fertilizers', 35.00, 'Balanced NPK fertilizer', 'NutriGrow Ltd.', '+1234567899', 150),
('Wheat Seeds Premium', 'seeds', 12.00, 'Premium quality wheat seeds', 'SeedMaster Ltd.', '+1234567897', 300);

-- Insert sample extensive services
INSERT INTO extensive_services (name, contact, service_type, description, location) VALUES 
('VetCare Services', '+1234567900', 'veterinary', 'Complete veterinary services for livestock', 'Central District'),
('AgroTech Consulting', '+1234567901', 'agronomy', 'Professional agronomy consulting services', 'North Region'),
('Farm Machinery Rentals', '+1234567902', 'machinery', 'Tractor and machinery rental services', 'South Region'),
('Soil Testing Lab', '+1234567903', 'agronomy', 'Professional soil testing and analysis', 'East District'),
('Livestock Vaccination', '+1234567904', 'veterinary', 'Mobile livestock vaccination services', 'West Region');

-- Step 4: Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_products_farmer ON products(farmer_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_input_supplies_category ON input_supplies(category);
CREATE INDEX idx_extensive_services_type ON extensive_services(service_type);
CREATE INDEX idx_expert_enquiries_user ON expert_enquiries(user_id);
CREATE INDEX idx_expert_enquiries_expert ON expert_enquiries(expert_id);
CREATE INDEX idx_service_requests_user ON service_requests(user_id);
CREATE INDEX idx_service_requests_service ON service_requests(service_id);
CREATE INDEX idx_supply_requests_user ON supply_requests(user_id);
CREATE INDEX idx_supply_requests_supply ON supply_requests(supply_id);

-- Step 5: Show table structure verification
SHOW TABLES;

-- Step 6: Display table information
DESCRIBE users;
DESCRIBE experts;
DESCRIBE products;
DESCRIBE input_supplies;
DESCRIBE extensive_services;
DESCRIBE expert_enquiries;
DESCRIBE service_requests;
DESCRIBE supply_requests;
