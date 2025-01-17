-- Create database
CREATE DATABASE IF NOT EXISTS inventory_manager;
USE inventory_manager;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Transactions table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    type ENUM('inbound', 'outbound') NOT NULL,
    quantity INT NOT NULL,
    user_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default users
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- password: 'password'
('Test123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'); -- password: 'password'

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Electro', 'Electronic devices and gadgets'),
('Motorji', 'Motors and related equipment'),
('Clothes', 'Pants, shirts, and other clothing items');

-- Insert sample products
INSERT INTO products (name, sku, price, stock, category_id) VALUES
('Test1', 'TEST001', 200.00, 83, 1),
('Test2', 'TEST002', 400.00, 5, 2),
('Test3', 'TEST003', 1000.00, 2, 1),
('Test4', 'TEST004', 4.00, 4, 3),
('Test5', 'TEST005', 99.99, 50, 1),
('Test6', 'TEST006', 99.99, 100, 1),
('Test7', 'TEST007', 149.99, 50, 1);

-- Insert sample transactions
INSERT INTO transactions (product_id, type, quantity, user_id, notes) VALUES
(1, 'inbound', 100, 1, 'Initial stock'),
(1, 'outbound', 17, 1, 'Sale'),
(2, 'inbound', 10, 1, 'Restocking'),
(2, 'outbound', 5, 1, 'Sale');