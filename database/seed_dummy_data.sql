-- Hapus data lama agar tidak duplikat (opsional, hati-hati jika ada data asli)
-- Gunakan ini HANYA untuk testing / pengecekan awal!
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE usage_logs;
TRUNCATE TABLE items;
TRUNCATE TABLE users;
TRUNCATE TABLE polis;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insert Kategori
INSERT INTO categories (nama) VALUES 
('Obat'),
('Alkes'),
('Habis Pakai'),
('Peralatan Gigi');

-- 2. Insert Poli
INSERT INTO polis (id, nama) VALUES 
(1, 'Poli Gigi'),
(2, 'Poli Umum');

-- 3. Insert Users (Password default: '123' dihash dengan password_hash('123', PASSWORD_DEFAULT))
-- Hash dari '123' adalah $2y$10$wN.d5L8Qj... (kita gunakan hash standar untuk '123')
INSERT INTO users (id, username, password, full_name, role, poli_id) VALUES 
(1, 'super', '$2y$10$eE08pMInJ83451K6R3969O0Bv607F106H4QG3GZ.u3Z1215A4T7Ie', 'Super Admin Utama', 'super_admin', NULL),

(2, 'admingigi', '$2y$10$eE08pMInJ83451K6R3969O0Bv607F106H4QG3GZ.u3Z1215A4T7Ie', 'Admin Poli Gigi', 'admin_poli', 1),
(3, 'perawatgigi', '$2y$10$eE08pMInJ83451K6R3969O0Bv607F106H4QG3GZ.u3Z1215A4T7Ie', 'Suster Poli Gigi', 'perawat', 1),

(4, 'adminumum', '$2y$10$eE08pMInJ83451K6R3969O0Bv607F106H4QG3GZ.u3Z1215A4T7Ie', 'Admin Poli Umum', 'admin_poli', 2),
(5, 'perawatumum', '$2y$10$eE08pMInJ83451K6R3969O0Bv607F106H4QG3GZ.u3Z1215A4T7Ie', 'Mantri Poli Umum', 'perawat', 2);

-- 4. Insert Items
INSERT INTO items (id, nama, sku, kategori, stok, ambang_minimum, satuan, status, catatan, poli_id) VALUES 
-- Barang Poli Gigi
(1, 'Kaca Mulut (Mouth Mirror)', 'GG-001', 'Peralatan Gigi', 50, 10, 'pcs', 'available', 'Untuk pemeriksaan gigi', 1),
(2, 'Jarum Suntik Gigi 3cc', 'GG-002', 'Alkes', 100, 20, 'pcs', 'available', 'Sekali pakai', 1),
(3, 'Kapas Silinder (Cotton Roll)', 'GG-003', 'Habis Pakai', 5, 10, 'box', 'low', 'Stok sudah mau habis', 1),

-- Barang Poli Umum
(4, 'Paracetamol 500mg', 'UM-001', 'Obat', 200, 50, 'strip', 'available', 'Obat pereda nyeri', 2),
(5, 'Amoxicillin 500mg', 'UM-002', 'Obat', 150, 30, 'strip', 'available', 'Antibiotik', 2),
(6, 'Plester Luka', 'UM-003', 'Habis Pakai', 10, 20, 'kotak', 'low', 'Segera restock', 2),
(7, 'Stetoskop', 'UM-004', 'Alkes', 3, 2, 'pcs', 'available', 'Alat periksa', 2);

-- 5. Insert Usage Logs (Contoh data pengambilan barang)
INSERT INTO usage_logs (item_id, user_id, jumlah, keterangan, created_at) VALUES 
(1, 3, 2, 'Dipakai untuk pasien A', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 5, 5, 'Diberikan ke pasien B', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(3, 3, 1, 'Dipakai untuk cabut gigi pasien C', NOW());

-- Selesai! Anda bisa menggunakan akun-akun di atas untuk testing.
-- Password semua akun: 123
