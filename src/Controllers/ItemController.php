<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class ItemController {
    /**
     * Konstruktor: Memastikan hanya user yang login yang bisa mengakses
     */
    public function __construct() {
        AuthMiddleware::requireLogin();
    }

    /**
     * Menampilkan halaman Input Barang (Form Tambah Barang)
     */
    public function create() {
        $user = Auth::user();
        
        // Perawat tidak diizinkan menambah barang baru (Master Data)
        if ($user['role'] === 'perawat' || $user['role'] === 'super_admin') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $pdo = getDbConnection();
        $stmtKat = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC");
        $daftarKategori = $stmtKat->fetchAll(PDO::FETCH_COLUMN);

        $daftarSatuan   = ['pcs', 'box', 'strip', 'botol', 'ampul', 'tablet', 'kapsul', 'ml', 'kotak', 'pack'];
        
        $errors = [];
        $old = [
            'nama'           => '',
            'sku'            => '',
            'kategori'       => '',
            'stok'           => '',
            'ambang_minimum' => '15',
            'satuan'         => '',
            'catatan'        => '',
        ];

        // Ambil pesan flash (error/success) jika ada
        $flashStatus = $_GET['status'] ?? null;
        $flashMsg    = $_GET['msg'] ?? null;
        if (isset($_SESSION['form_errors'])) {
            $errors = $_SESSION['form_errors'];
            $old = $_SESSION['form_old'];
            unset($_SESSION['form_errors'], $_SESSION['form_old']);
        }

        // $pdo is already defined at the top of the method for categories
        $poli_id = $user['poli_id'];

        // Ambil barang yang baru ditambahkan (5 terakhir)
        $stmt = $pdo->prepare('SELECT nama, sku, kategori, stok, satuan, status FROM items WHERE poli_id = :poli_id ORDER BY id DESC LIMIT 5');
        $stmt->execute([':poli_id' => $poli_id]);
        $recentItems = $stmt->fetchAll();

        View::render('poli/input_barang', [
            'title' => 'Input Barang Baru',
            'user' => $user,
            'daftarKategori' => $daftarKategori,
            'daftarSatuan' => $daftarSatuan,
            'errors' => $errors,
            'old' => $old,
            'recentItems' => $recentItems,
            'flashStatus' => $flashStatus,
            'flashMsg' => $flashMsg
        ]);
    }

    /**
     * Menyimpan barang baru ke database (POST)
     */
    public function store() {
        $user = Auth::user();
        if ($user['role'] === 'perawat' || $user['role'] === 'super_admin') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/items/create");
            exit;
        }

        $pdo = getDbConnection();
        $stmtKat = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC");
        $daftarKategori = $stmtKat->fetchAll(PDO::FETCH_COLUMN);
        $old = [
            'nama'           => trim((string) ($_POST['nama'] ?? '')),
            'sku'            => trim((string) ($_POST['sku'] ?? '')),
            'kategori'       => trim((string) ($_POST['kategori'] ?? '')),
            'stok'           => (string) ($_POST['stok'] ?? ''),
            'ambang_minimum' => (string) ($_POST['ambang_minimum'] ?? '15'),
            'satuan'         => trim((string) ($_POST['satuan'] ?? '')),
            'catatan'        => trim((string) ($_POST['catatan'] ?? '')),
        ];

        $errors = [];

        // Validasi
        if ($old['nama'] === '') $errors['nama'] = 'Nama barang wajib diisi.';
        if ($old['sku'] === '') $errors['sku'] = 'SKU wajib diisi.';
        if (!in_array($old['kategori'], $daftarKategori, true)) $errors['kategori'] = 'Pilih kategori yang valid.';
        if ($old['satuan'] === '') $errors['satuan'] = 'Satuan wajib diisi.';
        if ($old['stok'] === '' || !is_numeric($old['stok']) || (int) $old['stok'] < 0) {
            $errors['stok'] = 'Stok awal harus berupa angka 0 atau lebih.';
        }
        if ($old['ambang_minimum'] === '' || !is_numeric($old['ambang_minimum']) || (int) $old['ambang_minimum'] < 0) {
            $errors['ambang_minimum'] = 'Ambang minimum harus berupa angka 0 atau lebih.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $old;
            header("Location: " . BASE_URL . "/items/create");
            exit;
        }

        $stok    = max(0, (int) $old['stok']);
        $ambang  = max(0, (int) $old['ambang_minimum']);
        $status  = $stok <= $ambang ? 'low' : 'available';

        $pdo = getDbConnection();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO items (nama, sku, kategori, stok, ambang_minimum, satuan, status, catatan, poli_id)
                VALUES (:nama, :sku, :kategori, :stok, :ambang, :satuan, :status, :catatan, :poli_id)
            ');
            $stmt->execute([
                ':nama'    => $old['nama'],
                ':sku'     => $old['sku'],
                ':kategori'=> $old['kategori'],
                ':stok'    => $stok,
                ':ambang'  => $ambang,
                ':satuan'  => $old['satuan'],
                ':status'  => $status,
                ':catatan' => $old['catatan'] !== '' ? $old['catatan'] : null,
                ':poli_id' => $user['poli_id'],
            ]);

            header('Location: ' . BASE_URL . '/items/create?' . http_build_query([
                'status' => 'success',
                'msg'    => "Barang \"{$old['nama']}\" berhasil ditambahkan.",
            ]));
            exit;

        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['form_errors'] = ['sku' => 'SKU ini sudah digunakan oleh barang lain.'];
            } else {
                $_SESSION['form_errors'] = ['general' => 'Gagal menyimpan barang. Silakan coba lagi.'];
            }
            $_SESSION['form_old'] = $old;
            header("Location: " . BASE_URL . "/items/create");
            exit;
        }
    }

    /**
     * Memproses aksi dari Dashboard (Update Stok, Catat Penggunaan, Hapus)
     */
    public function action() {
        $user = Auth::user();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $action = $_POST['action'] ?? '';
        $flashStatus = 'success';
        $flashMsg = '';
        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        try {
            if ($action === 'update_item' && $user['role'] !== 'perawat') {
                $id      = (int) ($_POST['id'] ?? 0);
                $stok    = max(0, (int) ($_POST['stok'] ?? 0));
                $ambang  = max(0, (int) ($_POST['ambang_minimum'] ?? 15));
                $catatan = trim((string) ($_POST['catatan'] ?? ''));
                $status  = $stok <= $ambang ? 'low' : 'available';

                $stmt = $pdo->prepare('UPDATE items SET stok = :stok, ambang_minimum = :ambang, status = :status, catatan = :catatan WHERE id = :id AND poli_id = :poli_id');
                $stmt->execute([
                    ':stok'   => $stok,
                    ':ambang' => $ambang,
                    ':status' => $status,
                    ':catatan'=> $catatan !== '' ? $catatan : null,
                    ':id'     => $id,
                    ':poli_id'=> $poli_id
                ]);
                $flashMsg = 'Stok barang berhasil diperbarui.';

            } elseif ($action === 'use_item') {
                $id     = (int) ($_POST['id'] ?? 0);
                $jumlah = max(0, (int) ($_POST['jumlah_pakai'] ?? 0));

                $stmt = $pdo->prepare('SELECT stok, ambang_minimum FROM items WHERE id = :id AND poli_id = :poli_id');
                $stmt->execute([':id' => $id, ':poli_id' => $poli_id]);
                $row = $stmt->fetch();

                if ($row) {
                    if ($jumlah > (int) $row['stok']) {
                        $flashStatus = 'error';
                        $flashMsg = 'Jumlah pemakaian melebihi stok yang tersedia.';
                    } else {
                        $newStok   = max(0, (int) $row['stok'] - $jumlah);
                        $newStatus = $newStok <= (int) $row['ambang_minimum'] ? 'low' : 'available';
                        
                        $pdo->beginTransaction();
                        // 1. Update stok items
                        $stmt2 = $pdo->prepare('UPDATE items SET stok = :stok, status = :status WHERE id = :id');
                        $stmt2->execute([':stok' => $newStok, ':status' => $newStatus, ':id' => $id]);
                        
                        // 2. Catat ke tabel usage_logs (Fitur Perawat / Log aktivitas)
                        $stmtLog = $pdo->prepare('INSERT INTO usage_logs (item_id, user_id, jumlah) VALUES (:item_id, :user_id, :jumlah)');
                        $stmtLog->execute([
                            ':item_id' => $id,
                            ':user_id' => $user['id'],
                            ':jumlah' => $jumlah
                        ]);
                        
                        $pdo->commit();
                        $flashMsg = 'Pemakaian barang berhasil dicatat.';
                    }
                } else {
                    $flashStatus = 'error';
                    $flashMsg = 'Barang tidak ditemukan.';
                }

            } elseif ($action === 'delete_item' && $user['role'] !== 'perawat') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare('DELETE FROM items WHERE id = :id AND poli_id = :poli_id');
                $stmt->execute([':id' => $id, ':poli_id' => $poli_id]);
                $flashMsg = 'Barang berhasil dihapus.';
            }
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $flashStatus = 'error';
            $flashMsg = 'Gagal memproses permintaan. Silakan coba lagi.';
        }

        header('Location: ' . BASE_URL . '/dashboard?' . http_build_query(['status' => $flashStatus, 'msg' => $flashMsg]));
        exit;
    }
}
