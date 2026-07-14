<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class MasterKategoriController {
    public function __construct() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $user = Auth::user();
        $pdo = getDbConnection();

        $stmt = $pdo->query("SELECT * FROM categories ORDER BY nama ASC");
        $categories = $stmt->fetchAll();

        $flashStatus = $_GET['status'] ?? null;
        $flashMsg = $_GET['msg'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        View::render('super_admin/master_kategori', [
            'title' => 'Master Kategori',
            'user' => $user,
            'categories' => $categories,
            'flashStatus' => $flashStatus,
            'flashMsg' => $flashMsg,
            'flashError' => $flashError
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($nama !== '') {
                $pdo = getDbConnection();
                
                // Check duplicate
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE nama = :nama");
                $stmt->execute([':nama' => $nama]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['flash_error'] = "Kategori '$nama' sudah terdaftar.";
                    header('Location: ' . BASE_URL . '/super-admin/kategori');
                    exit;
                }
                
                $stmt = $pdo->prepare("INSERT INTO categories (nama, deskripsi) VALUES (:nama, :deskripsi)");
                $stmt->execute([':nama' => $nama, ':deskripsi' => $deskripsi]);
                header('Location: ' . BASE_URL . '/super-admin/kategori?status=success&msg=' . urlencode('Kategori berhasil ditambahkan'));
                exit;
            } else {
                $_SESSION['flash_error'] = "Nama kategori wajib diisi.";
            }
        }
        header('Location: ' . BASE_URL . '/super-admin/kategori');
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo = getDbConnection();
            
            // Note: because items.kategori is currently a VARCHAR and not a foreign key,
            // deleting a category will NOT cascade delete items, it will just remove it from the dropdown.
            // If they want to edit items with deleted categories, they will need to select a new valid category.
            
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: ' . BASE_URL . '/super-admin/kategori?status=success&msg=' . urlencode('Kategori berhasil dihapus'));
            exit;
        }
        header('Location: ' . BASE_URL . '/super-admin/kategori');
        exit;
    }
}
