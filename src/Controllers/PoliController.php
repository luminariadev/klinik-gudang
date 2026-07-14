<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class PoliController {
    /**
     * Konstruktor: Memastikan hanya user yang login yang bisa mengakses
     */
    public function __construct() {
        AuthMiddleware::requireLogin();
    }

    /**
     * Menampilkan Dashboard Poli (Umum, Gigi, dll) berdasarkan poli_id user
     */
    public function dashboard() {
        $user = Auth::user();
        
        // Super admin tidak menggunakan dashboard ini
        if ($user['role'] === 'super_admin') {
            header("Location: " . BASE_URL . "/super-admin");
            exit;
        }

        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        // Mengambil data ringkasan stok
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(stok), 0) AS total FROM items WHERE poli_id = :poli_id');
        $stmt->execute([':poli_id' => $poli_id]);
        $totalStok = (int) $stmt->fetchColumn();

        // Mengambil jumlah barang yang stoknya menipis
        $stmt = $pdo->prepare('SELECT COUNT(*) AS jumlah FROM items WHERE poli_id = :poli_id AND stok <= ambang_minimum');
        $stmt->execute([':poli_id' => $poli_id]);
        $stokHampirHabis = (int) $stmt->fetchColumn();

        // Mengambil semua item untuk poli ini
        $stmt = $pdo->prepare('SELECT id, nama, sku, kategori, stok, ambang_minimum, satuan, status, catatan FROM items WHERE poli_id = :poli_id ORDER BY nama ASC');
        $stmt->execute([':poli_id' => $poli_id]);
        $items = $stmt->fetchAll();

        // Mengambil ringkasan per kategori
        $kategoriSummary = [];
        $stmt = $pdo->prepare('
            SELECT
                kategori,
                COUNT(*) AS jumlah_item,
                COALESCE(SUM(stok), 0) AS total_stok,
                SUM(CASE WHEN stok <= ambang_minimum THEN 1 ELSE 0 END) AS jumlah_rendah,
                SUM(CASE WHEN stok > ambang_minimum THEN 1 ELSE 0 END) AS jumlah_tersedia,
                COALESCE(SUM(CASE WHEN stok > ambang_minimum THEN stok ELSE 0 END), 0) AS stok_tersedia
            FROM items
            WHERE poli_id = :poli_id
            GROUP BY kategori
        ');
        $stmt->execute([':poli_id' => $poli_id]);
        foreach ($stmt->fetchAll() as $r) {
            $kategoriSummary[$r['kategori']] = $r;
        }
        
        $daftarKategori = array_keys($kategoriSummary);
        if (empty($daftarKategori)) {
            $daftarKategori = ['Obat', 'Alkes', 'Habis Pakai']; // Default jika kosong
        }

        // Tampilkan halaman dengan data yang diambil
        View::render('poli/dashboard', [
            'totalStok' => $totalStok,
            'stokHampirHabis' => $stokHampirHabis,
            'items' => $items,
            'kategoriSummary' => $kategoriSummary,
            'daftarKategori' => $daftarKategori,
            'user' => $user
        ]);
    }

    /**
     * Menangani form aksi (Update Stok, Catat Pemakaian, Hapus)
     */
    public function action() {
        // Logika akan dipindahkan ke ItemController nantinya, 
        // namun untuk saat ini kita bisa menanganinya di sini
    }
}
