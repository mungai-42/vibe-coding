# DigiFarm - Digital Farming Solutions

A comprehensive digital platform connecting farmers with agricultural experts, services, and marketplace opportunities.

## 🌱 Overview

DigiFarm is a full-stack web application built with HTML, CSS, JavaScript, PHP, and MySQL. It provides a complete ecosystem for farmers to access expert advice, request services, order supplies, and sell their products.

## 🚀 Features

### For Farmers
- **User Registration & Authentication**: Secure login/registration system
- **Expert Advisory**: Request consultations from agricultural experts
- **Service Requests**: Request veterinary, agronomy, and machinery services
- **Input Supply Orders**: Order tools, fertilizers, and quality seeds
- **Marketplace**: Post and sell farm products
- **Dashboard**: Overview of activities and requests

### For Administrators
- **Expert Management**: Add and manage agricultural experts
- **Supply Management**: Add and manage input supplies
- **Service Management**: Add and manage extensive services
- **System Overview**: View statistics and manage the platform

## 📁 Project Structure

```
DIGI_FARM/
├── index.html              # Landing page
├── style.css               # Main stylesheet
├── script.js               # Frontend JavaScript
├── login.php               # User authentication
├── register.php            # User registration
├── logout.php              # Logout functionality
├── config/
│   └── database.php        # Database connection and setup
├── farmer/
│   ├── dashboard.php       # Farmer dashboard
│   ├── expert-enquiry.php  # Expert consultation form
│   ├── service-request.php # Service request form
│   ├── supply-request.php  # Supply order form
│   └── add-product.php     # Product listing form
└── admin/
    ├── dashboard.php       # Admin dashboard
    ├── add-expert.php      # Add expert form
    ├── add-supply.php      # Add supply form
    └── add-service.php     # Add service form
```

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Security**: PDO prepared statements, password hashing
- **Styling**: Modern CSS with gradients and responsive design

## 📋 Database Schema

### Tables
1. **users** - Farmer and admin accounts
2. **experts** - Agricultural experts information
3. **products** - Marketplace products
4. **input_supplies** - Tools, fertilizers, seeds
5. **extensive_services** - Professional services
6. **expert_enquiries** - Expert consultation requests
7. **service_requests** - Service booking requests
8. **supply_requests** - Supply order requests

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   git clone <repository-url>
   cd DIGI_FARM
   ```

2. **Database Setup**
   - Create a MySQL database named `digifarm_db`
   - Import the database schema (tables will be created automatically)

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update database credentials:
     ```php
     $host = 'localhost';
     $dbname = 'digifarm_db';
     $username = 'your_username';
     $password = 'your_password';
     ```

4. **Web Server Configuration**
   - Place files in your web server directory
   - Ensure PHP has write permissions
   - Configure URL rewriting if needed

5. **Access the Application**
   - Navigate to `http://localhost/DIGI_FARM/`
   - Register as a farmer or use admin credentials

## 👥 User Roles

### Farmer
- Register and login
- Request expert consultations
- Order input supplies
- Request professional services
- Post products to marketplace
- View dashboard with activities

### Administrator
- Manage all system data
- Add/edit experts, supplies, services
- View system statistics
- Monitor farmer activities

## 🔐 Security Features

- **Password Hashing**: Secure password storage using `password_hash()`
- **SQL Injection Prevention**: PDO prepared statements
- **Session Management**: Secure session handling
- **Input Validation**: Server-side validation for all forms
- **XSS Prevention**: HTML escaping for user inputs

## 🎨 UI/UX Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern Interface**: Clean, professional design with gradients
- **Smooth Animations**: CSS transitions and hover effects
- **User-Friendly Forms**: Intuitive form layouts with validation
- **Consistent Styling**: Unified color scheme and typography

## 📱 Responsive Design

The application is fully responsive with:
- Mobile-first approach
- Flexible grid layouts
- Adaptive typography
- Touch-friendly interface elements

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` to set your database credentials.

### Email Configuration (Future Enhancement)
Add SMTP settings for email notifications.

## 🚀 Deployment

### Local Development
1. Use XAMPP, WAMP, or similar local server
2. Place files in `htdocs` or `www` directory
3. Start Apache and MySQL services

### Production Deployment
1. Upload files to web server
2. Configure database connection
3. Set proper file permissions
4. Enable HTTPS for security

## 📊 System Requirements

### Server Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- 50MB disk space
- 128MB RAM minimum

### Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## 🔄 Future Enhancements

- **Email Notifications**: Automated email alerts
- **Payment Integration**: Online payment processing
- **Mobile App**: Native mobile application
- **Analytics Dashboard**: Advanced reporting
- **Multi-language Support**: Internationalization
- **API Development**: RESTful API for third-party integration

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials
   - Ensure MySQL service is running
   - Check database exists

2. **Permission Errors**
   - Set proper file permissions (755 for directories, 644 for files)
   - Ensure web server has write access

3. **Session Issues**
   - Check PHP session configuration
   - Verify session storage permissions

## 📞 Support

For technical support or questions:
- Check the troubleshooting section
- Review error logs
- Ensure all requirements are met

## 📄 License

This project is developed for educational and commercial use.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

**DigiFarm** - Empowering farmers with digital solutions for a sustainable future.
