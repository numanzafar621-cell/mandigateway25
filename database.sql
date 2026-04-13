-- =============================================
-- Database: mandigateway_db
-- Complete schema for MandiGateway Multi-Store Platform
-- Last updated: Fully working, all tables included
-- =============================================

CREATE DATABASE IF NOT EXISTS mandigateway_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mandigateway_db;

-- =============================================
-- Table: users (store owners + admin)
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    cnic_front VARCHAR(500),
    cnic_back VARCHAR(500),
    address TEXT,
    google_map TEXT,
    status ENUM('pending','active','suspended') DEFAULT 'pending',
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Table: stores (each user's store settings)
-- =============================================
CREATE TABLE stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    subdomain VARCHAR(100) UNIQUE NOT NULL,
    logo VARCHAR(500) DEFAULT 'logo.png',
    header_color VARCHAR(7) DEFAULT '#0d6efd',
    banner_text VARCHAR(255) DEFAULT 'Welcome to my store!',
    whatsapp_number VARCHAR(20),
    whatsapp_position ENUM('floating','below_product','above_product') DEFAULT 'floating',
    theme VARCHAR(20) DEFAULT 'modern',
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    footer_text VARCHAR(500) DEFAULT 'Powered by MandiGateway',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Table: categories (per user)
-- =============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Table: products (with admin approval)
-- =============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    status ENUM('pending','active','inactive','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- =============================================
-- Table: pages (custom pages)
-- =============================================
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Table: posts (blog posts)
-- =============================================
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Table: sliders (homepage banners)
-- =============================================
CREATE TABLE sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    image VARCHAR(500),
    text VARCHAR(255),
    position INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Table: orders
-- =============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_user_id INT,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(20),
    customer_address TEXT,
    total DECIMAL(10,2),
    payment_method ENUM('cod','jazzcash','easypaisa') DEFAULT 'cod',
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_user_id) REFERENCES users(id)
);

-- =============================================
-- Table: order_items
-- =============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =============================================
-- Table: reviews (product ratings)
-- =============================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    store_user_id INT,
    customer_name VARCHAR(255),
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =============================================
-- Table: password_resets (for forgot password)
-- =============================================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL
);

-- =============================================
-- Table: chat_messages (live chat)
-- =============================================
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_user_id INT NOT NULL,
    sender ENUM('customer','owner') NOT NULL,
    customer_session VARCHAR(255),
    customer_name VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- Insert default admin user
-- email: admin@mandigateway.com
-- password: admin123 (hashed)
-- =============================================
INSERT INTO users (business_name, full_name, phone, email, password, status, role)
VALUES ('MandiGateway', 'Super Admin', '03001234567', 'admin@mandigateway.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', 'admin');

-- =============================================
-- Insert default store for admin
-- =============================================
INSERT INTO stores (user_id, subdomain, header_color, banner_text, whatsapp_number, footer_text)
SELECT id, 'mandigateway', '#0d6efd', 'Welcome to MandiGateway', '03001234567', 'Official MandiGateway Platform'
FROM users WHERE email = 'admin@mandigateway.com';

-- =============================================
-- Indexes for performance
-- =============================================
CREATE INDEX idx_products_user_status ON products(user_id, status);
CREATE INDEX idx_orders_store_user ON orders(store_user_id);
CREATE INDEX idx_chat_store ON chat_messages(store_user_id, created_at);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_password_resets_token ON password_resets(token);