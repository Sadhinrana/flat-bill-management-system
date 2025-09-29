-- Multi-Tenant Flat & Bill Management System
-- Database Structure and Sample Data
-- Generated for Laravel 12.x

-- Create database
CREATE DATABASE IF NOT EXISTS flat_bill_management;
USE flat_bill_management;

-- Users table (modified from Laravel default)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'house_owner') NOT NULL DEFAULT 'house_owner',
    building_id BIGINT UNSIGNED NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_role_building (role, building_id),
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Buildings table
CREATE TABLE buildings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_owner_id (owner_id),
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Flats table
CREATE TABLE flats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flat_number VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    owner_contact VARCHAR(255) NOT NULL,
    owner_email VARCHAR(255) NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_building_flat (building_id, flat_number),
    INDEX idx_building_id (building_id),
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Tenants table
CREATE TABLE tenants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    flat_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_building_id (building_id),
    INDEX idx_flat_id (flat_id),
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    FOREIGN KEY (flat_id) REFERENCES flats(id) ON DELETE SET NULL
);

-- Bill Categories table
CREATE TABLE bill_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_building_category (building_id, name),
    INDEX idx_building_id (building_id),
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Bills table
CREATE TABLE bills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month VARCHAR(7) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('paid', 'unpaid') NOT NULL DEFAULT 'unpaid',
    notes TEXT NULL,
    flat_id BIGINT UNSIGNED NOT NULL,
    bill_category_id BIGINT UNSIGNED NOT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_flat_month (flat_id, month),
    INDEX idx_building_month (building_id, month),
    INDEX idx_status (status),
    FOREIGN KEY (flat_id) REFERENCES flats(id) ON DELETE CASCADE,
    FOREIGN KEY (bill_category_id) REFERENCES bill_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Cache table (Laravel default)
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- Jobs table (Laravel default)
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INTEGER UNSIGNED NULL,
    available_at INTEGER UNSIGNED NOT NULL,
    created_at INTEGER UNSIGNED NOT NULL,
    INDEX idx_queue (queue)
);

-- Sample Data Insertion

-- Insert Users
INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
('Admin User', 'admin@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8Kz8Kz8K', 'admin', NOW(), NOW()),
('John Smith', 'john@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8Kz8Kz8K', 'house_owner', NOW(), NOW()),
('Sarah Johnson', 'sarah@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8Kz8Kz8K', 'house_owner', NOW(), NOW()),
('Mike Wilson', 'mike@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8Kz8Kz8K', 'house_owner', NOW(), NOW());

-- Insert Buildings
INSERT INTO buildings (name, address, city, state, postal_code, owner_id, created_at, updated_at) VALUES
('Building 1', '123 Main Street, Apt 1', 'New York', 'NY', '10001', 2, NOW(), NOW()),
('Building 2', '123 Main Street, Apt 2', 'New York', 'NY', '10002', 3, NOW(), NOW()),
('Building 3', '123 Main Street, Apt 3', 'New York', 'NY', '10003', 4, NOW(), NOW());

-- Update users with building_id
UPDATE users SET building_id = 1 WHERE id = 2;
UPDATE users SET building_id = 2 WHERE id = 3;
UPDATE users SET building_id = 3 WHERE id = 4;

-- Insert Flats
INSERT INTO flats (flat_number, owner_name, owner_contact, owner_email, building_id, created_at, updated_at) VALUES
-- Building 1 Flats
('A1', 'Flat Owner 1', '+1-555-0001', 'flat1@example.com', 1, NOW(), NOW()),
('A2', 'Flat Owner 2', '+1-555-0002', 'flat2@example.com', 1, NOW(), NOW()),
('A3', 'Flat Owner 3', '+1-555-0003', 'flat3@example.com', 1, NOW(), NOW()),
('A4', 'Flat Owner 4', '+1-555-0004', 'flat4@example.com', 1, NOW(), NOW()),
('A5', 'Flat Owner 5', '+1-555-0005', 'flat5@example.com', 1, NOW(), NOW()),

-- Building 2 Flats
('A1', 'Flat Owner 6', '+1-555-0006', 'flat6@example.com', 2, NOW(), NOW()),
('A2', 'Flat Owner 7', '+1-555-0007', 'flat7@example.com', 2, NOW(), NOW()),
('A3', 'Flat Owner 8', '+1-555-0008', 'flat8@example.com', 2, NOW(), NOW()),
('A4', 'Flat Owner 9', '+1-555-0009', 'flat9@example.com', 2, NOW(), NOW()),
('A5', 'Flat Owner 10', '+1-555-0010', 'flat10@example.com', 2, NOW(), NOW()),

-- Building 3 Flats
('A1', 'Flat Owner 11', '+1-555-0011', 'flat11@example.com', 3, NOW(), NOW()),
('A2', 'Flat Owner 12', '+1-555-0012', 'flat12@example.com', 3, NOW(), NOW()),
('A3', 'Flat Owner 13', '+1-555-0013', 'flat13@example.com', 3, NOW(), NOW()),
('A4', 'Flat Owner 14', '+1-555-0014', 'flat14@example.com', 3, NOW(), NOW()),
('A5', 'Flat Owner 15', '+1-555-0015', 'flat15@example.com', 3, NOW(), NOW());

-- Insert Tenants
INSERT INTO tenants (name, contact, email, building_id, flat_id, created_at, updated_at) VALUES
-- Building 1 Tenants
('Tenant 1', '+1-555-1001', 'tenant1@example.com', 1, 1, NOW(), NOW()),
('Tenant 2', '+1-555-1002', 'tenant2@example.com', 1, 2, NOW(), NOW()),
('Tenant 3', '+1-555-1003', 'tenant3@example.com', 1, 3, NOW(), NOW()),

-- Building 2 Tenants
('Tenant 4', '+1-555-1004', 'tenant4@example.com', 2, 6, NOW(), NOW()),
('Tenant 5', '+1-555-1005', 'tenant5@example.com', 2, 7, NOW(), NOW()),
('Tenant 6', '+1-555-1006', 'tenant6@example.com', 2, 8, NOW(), NOW()),

-- Building 3 Tenants
('Tenant 7', '+1-555-1007', 'tenant7@example.com', 3, 11, NOW(), NOW()),
('Tenant 8', '+1-555-1008', 'tenant8@example.com', 3, 12, NOW(), NOW()),
('Tenant 9', '+1-555-1009', 'tenant9@example.com', 3, 13, NOW(), NOW());

-- Insert Bill Categories
INSERT INTO bill_categories (name, description, building_id, created_at, updated_at) VALUES
-- Building 1 Categories
('Electricity', 'Monthly electricity charges', 1, NOW(), NOW()),
('Gas Bill', 'Monthly gas bill charges', 1, NOW(), NOW()),
('Water Bill', 'Monthly water bill charges', 1, NOW(), NOW()),
('Utility Charges', 'Monthly utility charges', 1, NOW(), NOW()),

-- Building 2 Categories
('Electricity', 'Monthly electricity charges', 2, NOW(), NOW()),
('Gas Bill', 'Monthly gas bill charges', 2, NOW(), NOW()),
('Water Bill', 'Monthly water bill charges', 2, NOW(), NOW()),
('Utility Charges', 'Monthly utility charges', 2, NOW(), NOW()),

-- Building 3 Categories
('Electricity', 'Monthly electricity charges', 3, NOW(), NOW()),
('Gas Bill', 'Monthly gas bill charges', 3, NOW(), NOW()),
('Water Bill', 'Monthly water bill charges', 3, NOW(), NOW()),
('Utility Charges', 'Monthly utility charges', 3, NOW(), NOW());

-- Insert Bills (Sample data for 3 months)
INSERT INTO bills (month, amount, due_amount, status, notes, flat_id, bill_category_id, building_id, created_at, updated_at) VALUES
-- Building 1 Bills - January 2024
('2024-01', 120.00, 0.00, 'paid', 'Monthly electricity bill', 1, 1, 1, NOW(), NOW()),
('2024-01', 80.00, 0.00, 'paid', 'Monthly gas bill', 1, 2, 1, NOW(), NOW()),
('2024-01', 60.00, 0.00, 'paid', 'Monthly water bill', 1, 3, 1, NOW(), NOW()),
('2024-01', 40.00, 0.00, 'paid', 'Monthly utility charges', 1, 4, 1, NOW(), NOW()),

('2024-01', 130.00, 0.00, 'paid', 'Monthly electricity bill', 2, 1, 1, NOW(), NOW()),
('2024-01', 85.00, 0.00, 'paid', 'Monthly gas bill', 2, 2, 1, NOW(), NOW()),
('2024-01', 65.00, 0.00, 'paid', 'Monthly water bill', 2, 3, 1, NOW(), NOW()),
('2024-01', 45.00, 0.00, 'paid', 'Monthly utility charges', 2, 4, 1, NOW(), NOW()),

-- Building 1 Bills - February 2024
('2024-02', 125.00, 0.00, 'paid', 'Monthly electricity bill', 1, 1, 1, NOW(), NOW()),
('2024-02', 82.00, 0.00, 'paid', 'Monthly gas bill', 1, 2, 1, NOW(), NOW()),
('2024-02', 62.00, 0.00, 'paid', 'Monthly water bill', 1, 3, 1, NOW(), NOW()),
('2024-02', 42.00, 0.00, 'paid', 'Monthly utility charges', 1, 4, 1, NOW(), NOW()),

('2024-02', 135.00, 15.00, 'unpaid', 'Monthly electricity bill', 2, 1, 1, NOW(), NOW()),
('2024-02', 87.00, 0.00, 'paid', 'Monthly gas bill', 2, 2, 1, NOW(), NOW()),
('2024-02', 67.00, 0.00, 'paid', 'Monthly water bill', 2, 3, 1, NOW(), NOW()),
('2024-02', 47.00, 0.00, 'paid', 'Monthly utility charges', 2, 4, 1, NOW(), NOW()),

-- Building 2 Bills - January 2024
('2024-01', 140.00, 0.00, 'paid', 'Monthly electricity bill', 6, 5, 2, NOW(), NOW()),
('2024-01', 90.00, 0.00, 'paid', 'Monthly gas bill', 6, 6, 2, NOW(), NOW()),
('2024-01', 70.00, 0.00, 'paid', 'Monthly water bill', 6, 7, 2, NOW(), NOW()),
('2024-01', 50.00, 0.00, 'paid', 'Monthly utility charges', 6, 8, 2, NOW(), NOW()),

-- Building 2 Bills - February 2024
('2024-02', 145.00, 20.00, 'unpaid', 'Monthly electricity bill', 6, 5, 2, NOW(), NOW()),
('2024-02', 92.00, 0.00, 'paid', 'Monthly gas bill', 6, 6, 2, NOW(), NOW()),
('2024-02', 72.00, 0.00, 'paid', 'Monthly water bill', 6, 7, 2, NOW(), NOW()),
('2024-02', 52.00, 0.00, 'paid', 'Monthly utility charges', 6, 8, 2, NOW(), NOW()),

-- Building 3 Bills - January 2024
('2024-01', 150.00, 0.00, 'paid', 'Monthly electricity bill', 11, 9, 3, NOW(), NOW()),
('2024-01', 95.00, 0.00, 'paid', 'Monthly gas bill', 11, 10, 3, NOW(), NOW()),
('2024-01', 75.00, 0.00, 'paid', 'Monthly water bill', 11, 11, 3, NOW(), NOW()),
('2024-01', 55.00, 0.00, 'paid', 'Monthly utility charges', 11, 12, 3, NOW(), NOW()),

-- Building 3 Bills - February 2024
('2024-02', 155.00, 25.00, 'unpaid', 'Monthly electricity bill', 11, 9, 3, NOW(), NOW()),
('2024-02', 97.00, 0.00, 'paid', 'Monthly gas bill', 11, 10, 3, NOW(), NOW()),
('2024-02', 77.00, 0.00, 'paid', 'Monthly water bill', 11, 11, 3, NOW(), NOW()),
('2024-02', 57.00, 0.00, 'paid', 'Monthly utility charges', 11, 12, 3, NOW(), NOW());

-- Create indexes for better performance
CREATE INDEX idx_bills_flat_status ON bills(flat_id, status);
CREATE INDEX idx_bills_building_status ON bills(building_id, status);
CREATE INDEX idx_bills_month_status ON bills(month, status);
CREATE INDEX idx_tenants_building_flat ON tenants(building_id, flat_id);

-- Show table information
SHOW TABLES;

-- Show sample data counts
SELECT 'Users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'Buildings', COUNT(*) FROM buildings
UNION ALL
SELECT 'Flats', COUNT(*) FROM flats
UNION ALL
SELECT 'Tenants', COUNT(*) FROM tenants
UNION ALL
SELECT 'Bill Categories', COUNT(*) FROM bill_categories
UNION ALL
SELECT 'Bills', COUNT(*) FROM bills;

-- Show bills summary by status
SELECT 
    b.name as building_name,
    COUNT(*) as total_bills,
    SUM(CASE WHEN bills.status = 'paid' THEN 1 ELSE 0 END) as paid_bills,
    SUM(CASE WHEN bills.status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_bills,
    SUM(CASE WHEN bills.status = 'paid' THEN bills.amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN bills.status = 'unpaid' THEN bills.amount ELSE 0 END) as unpaid_amount
FROM bills
JOIN buildings b ON bills.building_id = b.id
GROUP BY b.id, b.name
ORDER BY b.name;




