# 🛍️ Krist - E-Commerce Web Application

A full-featured e-commerce web application developed as part of the **Web Technology** university course. This project demonstrates the implementation of a complete online shopping platform with user authentication, product management, shopping cart functionality, order processing, and an admin dashboard.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---

## 📋 Table of Contents

- [About The Project](#about-the-project)
- [Features](#features)
- [Project Structure](#project-structure)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Development Tasks](#development-tasks)
- [Screenshots](#screenshots)
- [Author](#author)

---

## 📖 About The Project

**Krist** is a modern e-commerce webshop application that provides a seamless online shopping experience. The project was developed incrementally through **5 tasks/milestones**, with each task building upon the previous one to create a comprehensive e-commerce solution.

This project was completed as part of the **Web Technology** course curriculum, with presentations delivered after the completion of each task to demonstrate progress and functionality.

---

## ✨ Features

### 🛒 Customer Features
- **User Authentication**
  - User registration with form validation
  - Secure login/logout functionality
  - Password recovery (Forgot Password with OTP verification)
  - Session management
  
- **Product Browsing**
  - Homepage with hero section and bestseller products
  - Browse products by categories (Shoes, Electronics, Fashion)
  - Product listing with filtering options
  - Detailed product pages with images, descriptions, sizes, and colors
  - Star rating display
  - Related products suggestions

- **Shopping Cart**
  - Add products to cart with size and color selection
  - Update quantity or remove items
  - Real-time cart updates via AJAX
  - Persistent cart using sessions
  - Order summary with subtotal, tax calculation, and discounts

- **Order Management**
  - Secure checkout process
  - Order history viewing
  - Order details with item breakdown
  - Order cancellation (for pending orders)
  - Order status tracking (Pending, Processing, Shipped, Delivered)

- **Discount System**
  - Automatic discount application (10% every 10th order, 20% every 20th order)
  - Tax calculation (19% VAT)

### 👨‍💼 Admin Features
- **Admin Dashboard**
  - Statistics overview (Total Orders, Pending Orders, Customers, Revenue)
  - Recent orders display
  
- **Order Management**
  - View all orders with filtering by status
  - Update order status
  - View detailed order information
  
- **Customer Management**
  - View all registered customers
  - Block/Unblock customers with reason
  - View customer order history

---

## 📁 Project Structure

```
krist-webshop/
├── index.php                 # Homepage
├── test_admin.php           # Admin testing
├── README.md                # Project documentation
│
├── administrator/           # Admin panel
│   ├── index.php           # Admin dashboard
│   ├── login.php           # Admin login
│   ├── logout.php          # Admin logout
│   ├── orders.php          # Order management
│   ├── order_detail.php    # Order details
│   └── customers.php       # Customer management
│
├── api/                     # API endpoints
│   ├── auth.php            # Authentication API
│   ├── cart.php            # Cart operations API
│   ├── admin_customers.php # Admin customer API
│   ├── admin_orders.php    # Admin orders API
│   └── admin_discount_settings.php
│
├── assets/                  # Static assets (images)
│
├── auth/                    # Authentication logic
│   ├── auth.php            # User authentication
│   ├── admin_auth.php      # Admin authentication
│   └── auth_api.php        # Auth API handlers
│
├── CSS/                     # Stylesheets
│   ├── mystyle.css         # Main styles
│   ├── admin.css           # Admin panel styles
│   ├── shoppingCart.css    # Cart page styles
│   ├── product_dtls.css    # Product details styles
│   └── ...                 # Other page-specific styles
│
├── data/                    # JSON data files
│   ├── products.json       # Product catalog
│   └── categories.json     # Product categories
│
├── database/               # Database configuration
│   ├── db.php             # Database connection
│   └── setup.php          # Database initialization
│
├── includes/               # PHP includes
│   ├── functions.php      # General functions
│   ├── cart_functions.php # Cart operations
│   ├── admin_functions.php # Admin functions
│   ├── navbar.php         # Navigation component
│   └── footer.php         # Footer component
│
├── JS/                     # JavaScript files
│   ├── script.js          # Main scripts
│   ├── cart.js            # Cart functionality
│   ├── validation.js      # Form validation
│   ├── admin.js           # Admin panel scripts
│   ├── priceTax.js        # Price calculations
│   └── profile.js         # User profile scripts
│
└── pages/                  # Customer-facing pages
    ├── login.php          # User login
    ├── registration.php   # User registration
    ├── logout.php         # User logout
    ├── list.php           # Product listing
    ├── product_details.php # Single product view
    ├── shoppingCart.php   # Shopping cart
    ├── myorders.php       # Order history
    ├── order_details.php  # Order details
    ├── personal_information.php # User profile
    ├── fogot-password.php # Password recovery
    ├── otpVerification.php # OTP verification
    ├── about.php          # About page
    ├── contact.php        # Contact page
    └── ...                # Other pages
```

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL** - Database management
- **PDO** - Database connectivity with prepared statements

### Frontend
- **HTML5** - Page structure
- **CSS3** - Styling and responsive design
- **JavaScript (ES6)** - Client-side interactivity
- **AJAX/Fetch API** - Asynchronous requests

### Libraries & Tools
- **Font Awesome 6.5** - Icons
- **JSON** - Data storage for products

---

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (or XAMPP/WAMP/MAMP)
- Web browser

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/krist-webshop.git
   ```

2. **Move to web server directory**
   ```bash
   # For XAMPP
   mv krist-webshop /xampp/htdocs/
   
   # For WAMP
   mv krist-webshop /wamp/www/
   ```

3. **Start Apache and MySQL services**
   - Using XAMPP Control Panel or
   - Command line: `sudo service apache2 start && sudo service mysql start`

4. **Configure database connection**
   - Open `database/db.php`
   - Update credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'webShop');
   ```

5. **Run database setup**
   - Navigate to: `http://localhost/krist-webshop/database/setup.php`
   - This will create the database and required tables

6. **Access the application**
   - Homepage: `http://localhost/krist-webshop/`
   - Admin Panel: `http://localhost/krist-webshop/administrator/`

---

## 🗄️ Database Setup

The `database/setup.php` script automatically creates:

### Tables
- **users** - Customer accounts (id, name, email, password, phone, address, is_blocked, blocked_reason)
- **admins** - Admin accounts (id, username, password, name)
- **orders** - Order records (order details, shipping info, amounts, status)
- **order_items** - Individual items in orders

### Default Accounts

| Type | Email/Username | Password |
|------|---------------|----------|
| Customer | test@example.com | test123 |
| Admin | admin | admin123 |

---

## 📱 Usage

### For Customers

1. **Register/Login** - Create an account or login
2. **Browse Products** - Explore categories or search products
3. **Add to Cart** - Select size, color, and quantity
4. **Checkout** - Provide shipping details and place order
5. **Track Orders** - View order status in "My Orders"

### For Administrators

1. **Login** - Access admin panel at `/administrator/login.php`
2. **Dashboard** - View sales statistics and recent orders
3. **Manage Orders** - Update order statuses
4. **Manage Customers** - Block/unblock users as needed

---

## 📝 Development Tasks

This project was developed through **5 progressive tasks**, with a presentation after each milestone:

### Task 1: Project Foundation
- Initial project setup and file structure
- Basic HTML pages with CSS styling
- Static homepage design
- Navigation structure

### Task 2: Product Catalog
- Product listing page
- Product details page
- Category-based navigation
- JSON-based product data
- Image galleries and thumbnails

### Task 3: User Authentication
- User registration with validation
- Login/Logout functionality
- Session management
- Password recovery flow
- Form validation (client-side & server-side)

### Task 4: Shopping Cart & Checkout
- Add to cart functionality
- Cart management (update quantity, remove items)
- AJAX-based cart operations
- Tax and discount calculations
- Order placement and confirmation

### Task 5: Admin Panel & Order Management
- Admin authentication
- Dashboard with statistics
- Order management system
- Customer management (block/unblock)
- Order status updates
- Complete integration and testing

---

## 📸 Screenshots

### Homepage
- Hero section with featured collection
- Shop by categories
- Bestseller products grid

### Product Details
- Product images with thumbnails
- Size and color selection
- Add to cart functionality
- Related products

### Shopping Cart
- Cart items with quantity controls
- Order summary with tax calculation
- Checkout button

### Admin Dashboard
- Statistics cards
- Recent orders table
- Navigation menu

---

## 👨‍🎓 Author

**[Your Name]**

- University Course: **Web Technology**
- Project Type: Course Project with 5 Task Milestones
- Year: 2024-2025

---

## 📄 License

This project is developed for educational purposes as part of a university course.

---

## 🙏 Acknowledgments

- University instructors for guidance
- Font Awesome for icons
- Online resources and documentation

---

<p align="center">
  Made with ❤️ for Web Technology Course
</p>
