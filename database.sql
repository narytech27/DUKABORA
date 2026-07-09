-- ============================================================
-- Duka Bora — Online Market Inventory System
-- IS 181 Group Assignment 2 | Module 1: Database Design
-- ============================================================

DROP DATABASE IF EXISTS duka_bora;
CREATE DATABASE duka_bora;
USE duka_bora;

-- ------------------------------------------------------------
-- Table: categories
-- ------------------------------------------------------------
CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE
);

-- ------------------------------------------------------------
-- Table: suppliers
-- ------------------------------------------------------------
CREATE TABLE suppliers (
    supplier_id   INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(100) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    location      VARCHAR(100) NOT NULL
);

-- ------------------------------------------------------------
-- Table: products
-- ------------------------------------------------------------
CREATE TABLE products (
    product_id  INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)   NOT NULL,
    category_id INT            NOT NULL,
    supplier_id INT            NOT NULL,
    price       DECIMAL(10,2)  NOT NULL CHECK (price > 0),
    stock_qty   INT            NOT NULL DEFAULT 0 CHECK (stock_qty >= 0),
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_products_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ------------------------------------------------------------
-- Table: sales
-- ------------------------------------------------------------
CREATE TABLE sales (
    sale_id     INT AUTO_INCREMENT PRIMARY KEY,
    product_id  INT           NOT NULL,
    qty_sold    INT           NOT NULL CHECK (qty_sold > 0),
    sale_date   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_sales_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO categories (category_name) VALUES
    ('Electronics'),
    ('Clothing'),
    ('Food');

INSERT INTO suppliers (supplier_name, phone, location) VALUES
    ('Mwanza Tech Distributors', '0765-112233', 'Mwanza'),
    ('Kariakoo Textiles Ltd',    '0713-445566', 'Dar es Salaam'),
    ('Nyanza Fresh Foods',       '0687-778899', 'Mwanza');

INSERT INTO products (name, category_id, supplier_id, price, stock_qty) VALUES
    ('LED Torch 5W',              1, 1, 8500.00,  25),
    ('USB Phone Charger',         1, 1, 6000.00,   3),
    ('Bluetooth Earphones',       1, 1, 25000.00,  0),
    ('Extension Cable 4-way',     1, 1, 12000.00, 14),
    ('Men Cotton T-Shirt',        2, 2, 9000.00,  18),
    ('Women Kitenge Dress',       2, 2, 22000.00,  6),
    ('Children School Uniform',   2, 2, 15000.00,  4),
    ('Rice 5kg Bag',               3, 3, 14000.00, 30),
    ('Cooking Oil 2L',             3, 3, 9500.00,   8),
    ('Sugar 2kg Pack',              3, 3, 5500.00,   2),
    ('Maize Flour 5kg',            3, 3, 8000.00,  20);
