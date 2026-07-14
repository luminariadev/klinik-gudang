-- Menambahkan kolom keterangan pada usage_logs jika belum ada

-- Gunakan database stock_gudang
USE stock_gudang;

-- Alter tabel usage_logs untuk menambahkan keterangan
ALTER TABLE usage_logs ADD COLUMN keterangan VARCHAR(255) NULL AFTER jumlah;
