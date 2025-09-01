# DigiFarm Database Setup Guide

## Overview
This guide provides step-by-step instructions for setting up the MySQL database for the DigiFarm MVP system.

## Prerequisites
- MySQL Server (version 5.7 or higher)
- MySQL Workbench, phpMyAdmin, or command line access
- PHP with PDO extension enabled

## Method 1: Using MySQL Command Line

### Step 1: Access MySQL Command Line
```bash
mysql -u root -p
```
Enter your MySQL root password when prompted.

### Step 2: Run the Complete Setup Script
```sql
source /path/to/your/database_setup.sql;
```

## Method 2: Using phpMyAdmin

### Step 1: Access phpMyAdmin
1. Open your web browser
2. Navigate to `http://localhost/phpmyadmin` (or your server's phpMyAdmin URL)
3. Login with your MySQL credentials

### Step 2: Create Database
1. Click "New" in the left sidebar
2. Enter "digifarm_db" as the database name
3. Click "Create"

### Step 3: Import SQL File
1. Select the "digifarm_db" database
2. Click the "Import" tab
3. Click "Choose File" and select `database_setup.sql`
4. Click "Go" to execute the script

## Method 3: Using MySQL Workbench

### Step 1: Connect to MySQL Server
1. Open MySQL Workbench
2. Create a new connection or use existing one
3. Connect to your MySQL server

### Step 2: Execute SQL Script
1. Go to File → Open SQL Script
2. Select `database_setup.sql`
3. Click the lightning bolt icon to execute the script

## Database Structure

### Tables Overview

| Table Name | Purpose | Key Fields |
|------------|---------|------------|
| `users` | Store farmer and admin accounts | id, username, email, user_type |
| `experts` | Store expert advisory information | id, name, specialization |
| `products` | Store marketplace products | id, name, price, farmer_id |
| `input_supplies` | Store tools, fertilizers, seeds | id, name, category, price |
| `extensive_services` | Store service providers | id, name, service_type |
| `expert_enquiries` | Store farmer requests to experts | id, user_id, expert_id |
| `service_requests` | Store service requests | id, user_id, service_id |
| `supply_requests` | Store supply requests | id, user_id, supply_id |

### Table Details

#### 1. Users Table
```sql
CREATE TABLE users (
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
```

#### 2. Experts Table
```sql
CREATE TABLE experts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    experience_years INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. Products Table
```sql
CREATE TABLE products (
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
```

#### 4. Input Supplies Table
```sql
CREATE TABLE input_supplies (
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
```

#### 5. Extensive Services Table
```sql
CREATE TABLE extensive_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    service_type ENUM('veterinary', 'agronomy', 'machinery', 'other') NOT NULL,
    description TEXT,
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 6. Expert Enquiries Table
```sql
CREATE TABLE expert_enquiries (
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
```

#### 7. Service Requests Table
```sql
CREATE TABLE service_requests (
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
```

#### 8. Supply Requests Table
```sql
CREATE TABLE supply_requests (
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
```

## Sample Data Included

The setup script includes sample data for testing:

### Default Admin Account
- **Username:** admin
- **Email:** admin@digifarm.com
- **Password:** admin123
- **Type:** admin

### Sample Experts
- Dr. John Smith (Crop Management)
- Dr. Sarah Johnson (Livestock Health)
- Prof. Michael Brown (Soil Science)
- Dr. Lisa Davis (Pest Control)

### Sample Input Supplies
- Organic Fertilizer 50kg ($45.00)
- Tractor Plow ($250.00)
- Hybrid Corn Seeds ($15.00)
- Pesticide Sprayer ($75.00)
- NPK Fertilizer 25kg ($35.00)
- Wheat Seeds Premium ($12.00)

### Sample Services
- VetCare Services (Veterinary)
- AgroTech Consulting (Agronomy)
- Farm Machinery Rentals (Machinery)
- Soil Testing Lab (Agronomy)
- Livestock Vaccination (Veterinary)

## Verification Steps

After running the setup script, verify the installation:

### 1. Check Database Creation
```sql
SHOW DATABASES;
```

### 2. Check Tables Creation
```sql
USE digifarm_db;
SHOW TABLES;
```

### 3. Check Sample Data
```sql
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM experts;
SELECT COUNT(*) FROM input_supplies;
SELECT COUNT(*) FROM extensive_services;
```

### 4. Test Admin Login
```sql
SELECT username, email, user_type FROM users WHERE username = 'admin';
```

## Configuration

### Update Database Connection
Edit `config/database.php` with your MySQL credentials:

```php
$host = 'localhost';        // Your MySQL host
$dbname = 'digifarm_db';    // Database name
$username = 'your_username'; // Your MySQL username
$password = 'your_password'; // Your MySQL password
```

## Troubleshooting

### Common Issues

1. **Access Denied Error**
   - Check MySQL user permissions
   - Verify username and password

2. **Connection Failed**
   - Ensure MySQL service is running
   - Check host and port settings

3. **Table Creation Failed**
   - Check MySQL version compatibility
   - Verify database exists

4. **Foreign Key Errors**
   - Ensure tables are created in correct order
   - Check data types match between tables

### Useful Commands

```sql
-- Check MySQL version
SELECT VERSION();

-- Check user privileges
SHOW GRANTS FOR 'your_username'@'localhost';

-- Check table structure
DESCRIBE table_name;

-- Check foreign key constraints
SHOW CREATE TABLE table_name;
```

## Security Considerations

1. **Change Default Passwords**
   - Update the default admin password
   - Use strong passwords for all accounts

2. **Database Permissions**
   - Create a dedicated database user
   - Grant only necessary permissions

3. **Backup Strategy**
   - Regular database backups
   - Test backup restoration

## Next Steps

After database setup:
1. Configure your web server (Apache/Nginx)
2. Set up PHP environment
3. Test the application
4. Add more sample data as needed

## Support

If you encounter issues:
1. Check the error logs
2. Verify all prerequisites are met
3. Test database connectivity
4. Review the troubleshooting section above
