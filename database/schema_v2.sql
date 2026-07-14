-- database/schema_v2.sql
-- Skema baru untuk aplikasi stock gudang dengan arsitektur MVC

CREATE DATABASE IF NOT EXISTS klinik_gudang_ CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE klinik_gudang_;

-- Tabel Polis (Dinamis)
CREATE TABLE IF NOT EXISTS polis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Poli
INSERT INTO polis (id, nama, deskripsi) VALUES 
(1, 'Poli Umum', 'Layanan kesehatan umum'),
(2, 'Poli Gigi', 'Layanan kesehatan gigi dan mulut')
ON DUPLICATE KEY UPDATE nama=VALUES(nama);

-- Tabel Users (Sudah mendukung poli_id dan remember_token)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin_poli', 'perawat') NOT NULL,
    poli_id INT NULL,
    full_name VARCHAR(100) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (poli_id) REFERENCES polis(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Super Admin
-- Password default: admin123
INSERT INTO users (username, password_hash, role, poli_id, full_name) VALUES
('super.admin', '$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG', 'super_admin', NULL, 'Super Admin')
ON DUPLICATE KEY UPDATE role=VALUES(role);

-- Data Awal Admin & Perawat Poli Umum
INSERT INTO users (username, password_hash, role, poli_id, full_name) VALUES
('admin.umum', '$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG', 'admin_poli', 1, 'Admin Poli Umum'),
('perawat.umum', '$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG', 'perawat', 1, 'Perawat Poli Umum')
ON DUPLICATE KEY UPDATE role=VALUES(role);

-- Tabel Items (Menghubungkan item ke poli tertentu)
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poli_id INT NOT NULL,
    nama VARCHAR(150) NOT NULL,
    sku VARCHAR(30) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    ambang_minimum INT NOT NULL DEFAULT 15,
    satuan VARCHAR(20) NOT NULL,
    status ENUM('available', 'low') NOT NULL DEFAULT 'available',
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (poli_id) REFERENCES polis(id) ON DELETE CASCADE,
    UNIQUE KEY sku_poli (sku, poli_id), -- SKU unik per poli
    INDEX idx_poli_status (poli_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Log Penggunaan (Fitur Perawat)
CREATE TABLE IF NOT EXISTS usage_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    jumlah INT NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
