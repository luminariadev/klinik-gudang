<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class GlobalItemController {
    public function __construct() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $user = Auth::user();
        $pdo = getDbConnection();

        // Get all items with their poli name
        $stmt = $pdo->query("
            SELECT i.*, p.nama AS poli_nama 
            FROM items i
            LEFT JOIN polis p ON i.poli_id = p.id
            ORDER BY p.nama ASC, i.nama ASC
        ");
        $items = $stmt->fetchAll();

        $flashStatus = $_GET['status'] ?? null;
        $flashMsg = $_GET['msg'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        View::render('super_admin/master_barang', [
            'title' => 'Master Barang',
            'user' => $user,
            'items' => $items,
            'flashStatus' => $flashStatus,
            'flashMsg' => $flashMsg,
            'flashError' => $flashError
        ]);
    }

    public function create() {
        $user = Auth::user();
        $pdo = getDbConnection();
        
        $stmtKat = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC");
        $daftarKategori = $stmtKat->fetchAll(PDO::FETCH_COLUMN);

        $daftarSatuan = ['pcs', 'box', 'strip', 'botol', 'ampul', 'tablet', 'kapsul', 'ml', 'kotak', 'pack'];
        
        $stmtPoli = $pdo->query("SELECT id, nama FROM polis ORDER BY nama ASC");
        $daftarPoli = $stmtPoli->fetchAll();

        $errors = [];
        $old = [
            'nama'           => '',
            'sku'            => '',
            'kategori'       => '',
            'stok'           => '',
            'ambang_minimum' => '15',
            'satuan'         => '',
            'catatan'        => '',
            'poli_id'        => ''
        ];

        $flashStatus = $_GET['status'] ?? null;
        $flashMsg    = $_GET['msg'] ?? null;
        if (isset($_SESSION['form_errors'])) {
            $errors = $_SESSION['form_errors'];
            $old = $_SESSION['form_old'];
            unset($_SESSION['form_errors'], $_SESSION['form_old']);
        }

        View::render('super_admin/input_barang_global', [
            'title' => 'Input Barang Baru (Global)',
            'user' => $user,
            'daftarKategori' => $daftarKategori,
            'daftarSatuan' => $daftarSatuan,
            'daftarPoli' => $daftarPoli,
            'errors' => $errors,
            'old' => $old,
            'flashStatus' => $flashStatus,
            'flashMsg' => $flashMsg
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/super-admin/barang/create");
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
            'poli_id'        => trim((string) ($_POST['poli_id'] ?? ''))
        ];

        $errors = [];

        if ($old['nama'] === '') $errors['nama'] = 'Nama barang wajib diisi.';
        if ($old['sku'] === '') $errors['sku'] = 'SKU wajib diisi.';
        if (!in_array($old['kategori'], $daftarKategori, true)) $errors['kategori'] = 'Pilih kategori yang valid.';
        if ($old['satuan'] === '') $errors['satuan'] = 'Satuan wajib diisi.';
        if ($old['poli_id'] === '') $errors['poli_id'] = 'Poli wajib dipilih.';
        if ($old['stok'] === '' || !is_numeric($old['stok']) || (int) $old['stok'] < 0) {
            $errors['stok'] = 'Stok awal harus berupa angka 0 atau lebih.';
        }
        if ($old['ambang_minimum'] === '' || !is_numeric($old['ambang_minimum']) || (int) $old['ambang_minimum'] < 0) {
            $errors['ambang_minimum'] = 'Ambang minimum harus berupa angka 0 atau lebih.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $old;
            header("Location: " . BASE_URL . "/super-admin/barang/create");
            exit;
        }

        $stok    = max(0, (int) $old['stok']);
        $ambang  = max(0, (int) $old['ambang_minimum']);
        $status  = $stok <= $ambang ? 'low' : 'available';

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
                ':poli_id' => $old['poli_id'],
            ]);

            header('Location: ' . BASE_URL . '/super-admin/barang?status=success&msg=' . urlencode("Barang \"{$old['nama']}\" berhasil ditambahkan."));
            exit;

        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['form_errors'] = ['sku' => 'SKU ini sudah digunakan oleh barang lain di poli tersebut.'];
            } else {
                $_SESSION['form_errors'] = ['general' => 'Gagal menyimpan barang. Silakan coba lagi.'];
            }
            $_SESSION['form_old'] = $old;
            header("Location: " . BASE_URL . "/super-admin/barang/create");
            exit;
        }
    }

    public function action() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/super-admin/barang");
            exit;
        }

        $action = $_POST['action'] ?? '';
        $flashStatus = 'success';
        $flashMsg = '';
        $pdo = getDbConnection();

        try {
            if ($action === 'update_item') {
                $id      = (int) ($_POST['id'] ?? 0);
                $stok    = max(0, (int) ($_POST['stok'] ?? 0));
                $ambang  = max(0, (int) ($_POST['ambang_minimum'] ?? 15));
                $catatan = trim((string) ($_POST['catatan'] ?? ''));
                $status  = $stok <= $ambang ? 'low' : 'available';

                $stmt = $pdo->prepare('UPDATE items SET stok = :stok, ambang_minimum = :ambang, status = :status, catatan = :catatan WHERE id = :id');
                $stmt->execute([
                    ':stok'   => $stok,
                    ':ambang' => $ambang,
                    ':status' => $status,
                    ':catatan'=> $catatan !== '' ? $catatan : null,
                    ':id'     => $id
                ]);
                $flashMsg = 'Stok barang berhasil diperbarui.';

            } elseif ($action === 'delete_item') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare('DELETE FROM items WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $flashMsg = 'Barang berhasil dihapus.';
            }
        } catch (\Throwable $e) {
            $flashStatus = 'error';
            $flashMsg = 'Gagal memproses permintaan.';
        }

        header('Location: ' . BASE_URL . '/super-admin/barang?' . http_build_query(['status' => $flashStatus, 'msg' => $flashMsg]));
        exit;
    }
}
