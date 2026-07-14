-- =================================================================
-- FILE UPDATE DATABASE BACKEND (STRUKTUR SAJA)
-- Gunakan file ini di phpMyAdmin untuk mengupdate struktur tabel
-- =================================================================

USE stock_gudang;

-- 1. Tambahkan tabel activity_logs (jika belum ada)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tambahkan kolom keterangan pada tabel usage_logs (jika belum ada)
-- (Catatan: Jalankan query ini. Jika kolom sudah ada, phpMyAdmin akan mengabaikan atau memberi peringatan ringan)
ALTER TABLE usage_logs ADD COLUMN IF NOT EXISTS keterangan VARCHAR(255) NULL AFTER jumlah;

-- Selesai! Struktur database Anda sudah yang paling baru.
