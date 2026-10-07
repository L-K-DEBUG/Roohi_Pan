-- =========================================================
-- ROOHI PAN — Full Schema (v2)
-- XAMPP / MariaDB compatible
-- WARNING: This DROPS the existing Roohi_pan database.
-- =========================================================

DROP DATABASE IF EXISTS Roohi_pan;

CREATE DATABASE Roohi_pan
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE Roohi_pan;


-- =========================================
-- 1. EMPLOYEES
-- =========================================

CREATE TABLE employees (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    role        ENUM('cashier','manager','owner') NOT NULL DEFAULT 'cashier',
    pin_hash    VARCHAR(255) NOT NULL,
    active      BOOLEAN NOT NULL DEFAULT TRUE,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- =========================================
-- 2. ITEMS (master menu list)
-- =========================================

CREATE TABLE items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    category    VARCHAR(50) NOT NULL,
    unit        VARCHAR(30) NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    active      BOOLEAN NOT NULL DEFAULT TRUE,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_item_price CHECK (price >= 0),
    CONSTRAINT unique_item_name UNIQUE (name)
);


-- =========================================
-- 3. DAILY STOCK (what was prepared today)
-- =========================================

CREATE TABLE daily_stock (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    item_id             INT NOT NULL,
    quantity_prepared   INT NOT NULL,
    quantity_remaining  INT NOT NULL,
    employee_id         INT NOT NULL,
    stock_date          DATE NOT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_daily_stock_item
        FOREIGN KEY (item_id) REFERENCES items(id),

    CONSTRAINT fk_daily_stock_employee
        FOREIGN KEY (employee_id) REFERENCES employees(id),

    CONSTRAINT unique_item_per_day
        UNIQUE (item_id, stock_date),

    CONSTRAINT chk_prepared_nonneg
        CHECK (quantity_prepared >= 0),

    CONSTRAINT chk_remaining_nonneg
        CHECK (quantity_remaining >= 0),

    CONSTRAINT chk_remaining_lte_prepared
        CHECK (quantity_remaining <= quantity_prepared)
);


-- =========================================
-- 4. RECEIPTS (one per customer order)
-- =========================================

CREATE TABLE receipts (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no      VARCHAR(30) NOT NULL,
    employee_id     INT NOT NULL,
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_method  VARCHAR(20) NOT NULL DEFAULT 'cash',
    voided          BOOLEAN NOT NULL DEFAULT FALSE,
    voided_by       INT NULL,
    voided_at       DATETIME NULL,
    void_reason     VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT unique_receipt_no UNIQUE (receipt_no),

    CONSTRAINT fk_receipts_employee
        FOREIGN KEY (employee_id) REFERENCES employees(id),

    CONSTRAINT fk_receipts_voided_by
        FOREIGN KEY (voided_by) REFERENCES employees(id),

    CONSTRAINT chk_receipt_total
        CHECK (total_amount >= 0)
);


-- =========================================
-- 5. SALES (line items on a receipt)
-- =========================================

CREATE TABLE sales (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id      INT NOT NULL,
    daily_stock_id  INT NOT NULL,
    item_id         INT NOT NULL,
    employee_id     INT NOT NULL,
    quantity_sold   INT NOT NULL,
    unit_price      DECIMAL(10,2) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_sales_receipt
        FOREIGN KEY (receipt_id) REFERENCES receipts(id),

    CONSTRAINT fk_sales_daily_stock
        FOREIGN KEY (daily_stock_id) REFERENCES daily_stock(id),

    CONSTRAINT fk_sales_item
        FOREIGN KEY (item_id) REFERENCES items(id),

    CONSTRAINT fk_sales_employee
        FOREIGN KEY (employee_id) REFERENCES employees(id),

    CONSTRAINT chk_qty_sold_positive
        CHECK (quantity_sold > 0),

    CONSTRAINT chk_sale_unit_price
        CHECK (unit_price >= 0)
);


-- =========================================
-- 6. STOCK ADJUSTMENTS (audit trail for stock changes)
-- =========================================

CREATE TABLE stock_adjustments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    daily_stock_id  INT NOT NULL,
    employee_id     INT NOT NULL,
    quantity        INT NOT NULL,
    adjustment_type ENUM('add','remove') NOT NULL,
    reason          VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_adjustment_daily_stock
        FOREIGN KEY (daily_stock_id) REFERENCES daily_stock(id),

    CONSTRAINT fk_adjustment_employee
        FOREIGN KEY (employee_id) REFERENCES employees(id),

    CONSTRAINT chk_adjustment_quantity
        CHECK (quantity > 0)
);


-- =========================================
-- 7. AUDIT LOG (who did what, when)
-- =========================================

CREATE TABLE audit_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT NULL,
    action          VARCHAR(50) NOT NULL,
    target_table    VARCHAR(50) NULL,
    target_id       INT NULL,
    details         TEXT NULL,
    ip_address      VARCHAR(45) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_employee
        FOREIGN KEY (employee_id) REFERENCES employees(id)
);


-- =========================================
-- INDEXES (for fast lookups)
-- =========================================

CREATE INDEX idx_daily_stock_date     ON daily_stock (stock_date);
CREATE INDEX idx_daily_stock_item     ON daily_stock (item_id);
CREATE INDEX idx_sales_receipt        ON sales (receipt_id);
CREATE INDEX idx_sales_item           ON sales (item_id);
CREATE INDEX idx_sales_created        ON sales (created_at);
CREATE INDEX idx_receipts_created     ON receipts (created_at);
CREATE INDEX idx_audit_employee       ON audit_log (employee_id);
CREATE INDEX idx_audit_action         ON audit_log (action);
CREATE INDEX idx_audit_created        ON audit_log (created_at);