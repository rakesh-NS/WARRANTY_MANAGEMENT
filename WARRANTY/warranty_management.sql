
-- Create the database
CREATE DATABASE IF NOT EXISTS warranty_management;
USE warranty_management;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    user_type ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    registration_date DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Create product_categories table
CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    warranty_duration INT NOT NULL COMMENT 'Duration in months'
);

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    purchase_date DATE NOT NULL,
    registration_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
);

-- Create warranties table
CREATE TABLE warranties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    start_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create warranty_claims table
CREATE TABLE warranty_claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    issue_description TEXT NOT NULL,
    date_submitted DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    admin_notes TEXT,
    processed_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create repair_records table
CREATE TABLE repair_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    claim_id INT NOT NULL,
    repair_date DATE NOT NULL,
    repair_description TEXT NOT NULL,
    cost DECIMAL(10,2),
    technician_name VARCHAR(100),
    status ENUM('in-progress', 'completed') NOT NULL DEFAULT 'in-progress',
    FOREIGN KEY (claim_id) REFERENCES warranty_claims(id)
);

-- Insert sample product categories
INSERT INTO product_categories (name, description, warranty_duration) VALUES
('Smartphones', 'Mobile phones and smartphones', 12),
('Laptops', 'Laptop computers', 24),
('Televisions', 'TV sets of all types', 36),
('Refrigerators', 'Cooling appliances', 60),
('Washing Machines', 'Laundry appliances', 48),
('Audio Systems', 'Sound systems and speakers', 12),
('Gaming Consoles', 'Video game systems', 24);

-- No user data is being inserted, leaving the users table empty
