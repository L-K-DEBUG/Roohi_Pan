CREATE DATABASE IF NOT EXISTS Roohi_pan;

USE Roohi_pan;


-- =========================================
-- 1. EMPLOYEES
-- =========================================

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
);


-- =========================================
-- 2. ITEMS
-- =========================================

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    unit VARCHAR(30) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
);


-- =========================================
-- 3. DAILY STOCK
-- =========================================

CREATE TABLE daily_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    quantity_available INT NOT NULL,
    quantity_remaining INT NOT NULL,
    employee_id INT NOT NULL,
    stock_date DATE NOT NULL DEFAULT (CURRENT_DATE),

    CONSTRAINT fk_daily_stock_item
        FOREIGN KEY (item_id)
        REFERENCES items(id),

    CONSTRAINT fk_daily_stock_employee
        FOREIGN KEY (employee_id)
        REFERENCES employees(id),

    CONSTRAINT unique_item_per_day
        UNIQUE (item_id, stock_date)
);


-- =========================================
-- 4. SALES
-- =========================================

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    daily_stock_id INT NOT NULL,
    employee_id INT NOT NULL,
    quantity_sold INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    receipt_no VARCHAR(30) NOT NULL,
    sale_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_sales_daily_stock
        FOREIGN KEY (daily_stock_id)
        REFERENCES daily_stock(id),

    CONSTRAINT fk_sales_employee
        FOREIGN KEY (employee_id)
        REFERENCES employees(id)
);


-- =========================================
-- 5. STOCK ADJUSTMENTS
-- =========================================

CREATE TABLE stock_adjustments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    daily_stock_id INT NOT NULL,
    employee_id INT NOT NULL,
    quantity INT NOT NULL,
    adjustment_type ENUM('add', 'remove') NOT NULL,
    reason VARCHAR(255),
    adjustment_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_adjustment_daily_stock
        FOREIGN KEY (daily_stock_id)
        REFERENCES daily_stock(id),

    CONSTRAINT fk_adjustment_employee
        FOREIGN KEY (employee_id)
        REFERENCES employees(id)
);