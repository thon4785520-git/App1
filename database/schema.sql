CREATE DATABASE IF NOT EXISTS religious_equipment CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE religious_equipment;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL
);

CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    quantity_total INT NOT NULL DEFAULT 0,
    quantity_available INT NOT NULL DEFAULT 0,
    location VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipment_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE borrow_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    borrower_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    organization VARCHAR(150) NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_borrow_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE borrow_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity INT NOT NULL,
    CONSTRAINT fk_item_transaction FOREIGN KEY (transaction_id) REFERENCES borrow_transactions(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);

INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin', '$2y$12$aALjiIZHpCVpwKyfVHvPZO.lDN2WCOibQIAXAkOmASRTnuKoSF7XO', 'แอดมินระบบ', 'admin'),
('user1', '$2y$12$aALjiIZHpCVpwKyfVHvPZO.lDN2WCOibQIAXAkOmASRTnuKoSF7XO', 'ผู้ใช้งานทั่วไป', 'user');

INSERT INTO categories (name, description) VALUES
('ชุดโต๊ะหมู่บูชา', 'ชุดอุปกรณ์พิธีสงฆ์และงานบุญ'),
('เครื่องเสียงงานพิธี', 'ไมโครโฟน ลำโพง และอุปกรณ์เสียง'),
('อุปกรณ์ตกแต่ง', 'พรม ผ้าม่าน และของตกแต่งงานพิธี');

INSERT INTO equipment (category_id, code, name, quantity_total, quantity_available, location) VALUES
(1, 'EQ-001', 'ชุดโต๊ะหมู่บูชา 9', 12, 12, 'คลัง A1'),
(2, 'EQ-002', 'ชุดเครื่องเสียงเคลื่อนที่', 8, 8, 'คลัง B2'),
(3, 'EQ-003', 'พรมแดง 50 เมตร', 15, 15, 'คลัง C1');
