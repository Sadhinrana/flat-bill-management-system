-- Multi-Tenant Flat & Bill Management System
-- Database Structure and Sample Data
-- Generated for Laravel 12.x

-- Create database
CREATE DATABASE IF NOT EXISTS flat_bill_management;
USE flat_bill_management;

-- Users table
CREATE TABLE users (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(255) NOT NULL UNIQUE,
                       email_verified_at TIMESTAMP NULL,
                       password VARCHAR(255) NOT NULL,
                       role ENUM('admin', 'house_owner') NOT NULL DEFAULT 'house_owner',
                       remember_token VARCHAR(100) NULL,
                       created_at TIMESTAMP NULL,
                       updated_at TIMESTAMP NULL,
                       INDEX idx_role (role)
);

-- Password Reset Tokens table
CREATE TABLE password_reset_tokens (
                                       email VARCHAR(255) PRIMARY KEY,
                                       token VARCHAR(255) NOT NULL,
                                       created_at TIMESTAMP NULL
);

-- Sessions table
CREATE TABLE sessions (
                          id VARCHAR(255) PRIMARY KEY,
                          user_id BIGINT UNSIGNED NULL,
                          ip_address VARCHAR(45) NULL,
                          user_agent TEXT NULL,
                          payload LONGTEXT NOT NULL,
                          last_activity INTEGER NOT NULL,
                          INDEX idx_user_id (user_id),
                          INDEX idx_last_activity (last_activity)
);

-- Cache table
CREATE TABLE cache (
                       key VARCHAR(255) PRIMARY KEY,
                       value MEDIUMTEXT NOT NULL,
                       expiration INTEGER NOT NULL
);

-- Cache Locks table
CREATE TABLE cache_locks (
                             key VARCHAR(255) PRIMARY KEY,
                             owner VARCHAR(255) NOT NULL,
                             expiration INTEGER NOT NULL
);

-- Jobs table
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

-- Job Batches table
CREATE TABLE job_batches (
                             id VARCHAR(255) PRIMARY KEY,
                             name VARCHAR(255) NOT NULL,
                             total_jobs INTEGER NOT NULL,
                             pending_jobs INTEGER NOT NULL,
                             failed_jobs INTEGER NOT NULL,
                             failed_job_ids LONGTEXT NOT NULL,
                             options MEDIUMTEXT NULL,
                             cancelled_at INTEGER NULL,
                             created_at INTEGER NOT NULL,
                             finished_at INTEGER NULL
);

-- Failed Jobs table
CREATE TABLE failed_jobs (
                             id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                             uuid VARCHAR(255) UNIQUE NOT NULL,
                             connection TEXT NOT NULL,
                             queue TEXT NOT NULL,
                             payload LONGTEXT NOT NULL,
                             exception LONGTEXT NOT NULL,
                             failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
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
                         UNIQUE KEY unique_email_building (email, building_id),
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

-- Insert Flats
INSERT INTO flats (flat_number, owner_name, owner_contact, owner_email, building_id, created_at, updated_at) VALUES
-- Building 1 Flats
('A1', 'Flat Owner 1', '+1-555-0001', 'flat1@example.com', 1, NOW(), NOW()),
('A2', 'Flat Owner 2', '+1-555-0002', 'flat2@example.com', 1, NOW(), NOW()),
('A3', 'Flat Owner 3', '+1-555-0003', 'flat3@example.com', 1, NOW(), NOW()),
('A4', 'Flat Owner 4', '+1-555-0004', 'flat4@example.com', 1, NOW(), NOW()),
('A5', 'Flat Owner 5', '+1-555-0005', 'flat5@example.com', 1, NOW(), NOW()),
-- Building 2 Flats
('A1', 'Flat Owner 1', '+1-555-0001', 'flat6@example.com', 2, NOW(), NOW()),
('A2', 'Flat Owner 2', '+1-555-0002', 'flat7@example.com', 2, NOW(), NOW()),
('A3', 'Flat Owner 3', '+1-555-0003', 'flat8@example.com', 2, NOW(), NOW()),
('A4', 'Flat Owner 4', '+1-555-0004', 'flat9@example.com', 2, NOW(), NOW()),
('A5', 'Flat Owner 5', '+1-555-0005', 'flat10@example.com', 2, NOW(), NOW()),
-- Building 3 Flats
('A1', 'Flat Owner 1', '+1-555-0001', 'flat11@example.com', 3, NOW(), NOW()),
('A2', 'Flat Owner 2', '+1-555-0002', 'flat12@example.com', 3, NOW(), NOW()),
('A3', 'Flat Owner 3', '+1-555-0003', 'flat13@example.com', 3, NOW(), NOW()),
('A4', 'Flat Owner 4', '+1-555-0004', 'flat14@example.com', 3, NOW(), NOW()),
('A5', 'Flat Owner 5', '+1-555-0005', 'flat15@example.com', 3, NOW(), NOW());

-- Insert Bill Categories
INSERT INTO bill_categories (name, description, building_id, created_at, updated_at) VALUES
                                                                                         ('Electricity', 'Monthly electricity charges', 1, NOW(), NOW()),
                                                                                         ('Gas Bill', 'Monthly gas bill charges', 1, NOW(), NOW()),
                                                                                         ('Water Bill', 'Monthly water bill charges', 1, NOW(), NOW()),
                                                                                         ('Utility Charges', 'Monthly utility charges', 1, NOW(), NOW()),
                                                                                         ('Electricity', 'Monthly electricity charges', 2, NOW(), NOW()),
                                                                                         ('Gas Bill', 'Monthly gas bill charges', 2, NOW(), NOW()),
                                                                                         ('Water Bill', 'Monthly water bill charges', 2, NOW(), NOW()),
                                                                                         ('Utility Charges', 'Monthly utility charges', 2, NOW(), NOW()),
                                                                                         ('Electricity', 'Monthly electricity charges', 3, NOW(), NOW()),
                                                                                         ('Gas Bill', 'Monthly gas bill charges', 3, NOW(), NOW()),
                                                                                         ('Water Bill', 'Monthly water bill charges', 3, NOW(), NOW()),
                                                                                         ('Utility Charges', 'Monthly utility charges', 3, NOW(), NOW());

-- Insert Tenants (3 per building as per seeder)
INSERT INTO tenants (name, contact, email, building_id, flat_id, created_at, updated_at) VALUES
-- Building 1 Tenants
('Tenant 1', '+1-555-1001', 'tenant1@example.com', 1, 1, NOW(), NOW()),
('Tenant 2', '+1-555-1002', 'tenant2@example.com', 1, 2, NOW(), NOW()),
('Tenant 3', '+1-555-1003', 'tenant3@example.com', 1, 3, NOW(), NOW()),
-- Building 2 Tenants
('Tenant 1', '+1-555-1004', 'tenant4@example.com', 2, 6, NOW(), NOW()),
('Tenant 2', '+1-555-1005', 'tenant5@example.com', 2, 7, NOW(), NOW()),
('Tenant 3', '+1-555-1006', 'tenant6@example.com', 2, 8, NOW(), NOW()),
-- Building 3 Tenants
('Tenant 1', '+1-555-1007', 'tenant7@example.com', 3, 11, NOW(), NOW()),
('Tenant 2', '+1-555-1008', 'tenant8@example.com', 3, 12, NOW(), NOW()),
('Tenant 3', '+1-555-1009', 'tenant9@example.com', 3, 13, NOW(), NOW());

-- Insert Bills for each flat and category (3 months as per seeder)
INSERT INTO bills (month, amount, due_amount, status, notes, flat_id, bill_category_id, building_id, created_at, updated_at)
SELECT
    month.value as month,
    ROUND(50 + RAND() * 150, 2) as amount,
    CASE WHEN RAND() > 0.7 THEN ROUND(RAND() * 50, 2) ELSE 0 END as due_amount,
    CASE WHEN RAND() > 0.7 THEN 'unpaid' ELSE 'paid' END as status,
    CONCAT('Monthly ', bc.name, ' charges') as notes,
    f.id as flat_id,
    bc.id as bill_category_id,
    f.building_id,
    NOW() as created_at,
    NOW() as updated_at
FROM flats f
CROSS JOIN bill_categories bc
CROSS JOIN (
    SELECT '2024-01' as value UNION SELECT '2024-02' UNION SELECT '2024-03'
) as month
WHERE f.building_id = bc.building_id;

-- Create performance indexes
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
