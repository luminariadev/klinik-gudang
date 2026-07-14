<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use PDO;

class MasterPoliController {
    public function __construct() {
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $pdo = getDbConnection();
        $polis = $pdo->query("SELECT * FROM polis ORDER BY nama ASC")->fetchAll();

        View::render('super_admin/master_poli', [
            'title' => 'Master Poli',
            'active_menu' => 'master_poli',
            'polis' => $polis
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($nama !== '') {
                $pdo = getDbConnection();
                $stmt = $pdo->prepare("INSERT INTO polis (nama, deskripsi) VALUES (:nama, :deskripsi)");
                $stmt->execute([':nama' => $nama, ':deskripsi' => $deskripsi]);
                header('Location: ' . BASE_URL . '/super-admin/polis?status=success&msg=Poli+berhasil+ditambahkan');
                exit;
            }
        }
        header('Location: ' . BASE_URL . '/super-admin/polis');
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM polis WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: ' . BASE_URL . '/super-admin/polis?status=success&msg=Poli+berhasil+dihapus');
            exit;
        }
        header('Location: ' . BASE_URL . '/super-admin/polis');
        exit;
    }
}
