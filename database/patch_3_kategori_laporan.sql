-- database/patch_3_kategori_laporan.sql
-- Patch untuk menambahkan fitur Master Kategori

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Kategori
INSERT INTO categories (nama, deskripsi) VALUES 
('Obat', 'Kategori untuk jenis-jenis obatobatan'),
('Alkes', 'Kategori untuk alat kesehatan'),
('Habis Pakai', 'Bahan habis pakai medis')
ON DUPLICATE KEY UPDATE nama=VALUES(nama);
