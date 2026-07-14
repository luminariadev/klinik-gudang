<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use PDO;

class MasterUserController {
    public function __construct() {
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $pdo = getDbConnection();
        
        $users = $pdo->query("
            SELECT u.id, u.username, u.full_name, u.role, u.created_at, 
                   p.nama as nama_poli
            FROM users u
            LEFT JOIN polis p ON u.poli_id = p.id
            WHERE u.role != 'super_admin'
            ORDER BY u.created_at DESC
        ")->fetchAll();

        $polis = $pdo->query("SELECT id, nama FROM polis ORDER BY nama ASC")->fetchAll();

        View::render('super_admin/master_user', [
            'title' => 'Master User',
            'active_menu' => 'master_user',
            'users' => $users,
            'polis' => $polis,
            'hasilAdmin' => $_SESSION['admin_baru'] ?? null,
            'flashError' => $_SESSION['flash_error'] ?? null,
            'flashInfo' => $_SESSION['flash_info'] ?? null,
        ]);
        
        unset($_SESSION['admin_baru'], $_SESSION['flash_error'], $_SESSION['flash_info']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $poli_id = (int) ($_POST['poli_id'] ?? 0);

            if ($nama && $username && $password && in_array($role, ['admin_poli', 'perawat']) && $poli_id > 0) {
                $pdo = getDbConnection();
                
                // Cek username
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['flash_error'] = "Username '$username' sudah digunakan.";
                    header('Location: ' . BASE_URL . '/super-admin/users');
                    exit;
                }
                
                // Get Poli Name for display
                $stmt = $pdo->prepare("SELECT nama FROM polis WHERE id = :id");
                $stmt->execute([':id' => $poli_id]);
                $nama_poli = $stmt->fetchColumn();

                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, full_name, poli_id, created_by) VALUES (:username, :password, :role, :nama, :poli_id, :created_by)");
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $hash,
                    ':role' => $role,
                    ':nama' => $nama,
                    ':poli_id' => $poli_id,
                    ':created_by' => $_SESSION['user_id']
                ]);

                $_SESSION['admin_baru'] = [
                    'fullName' => $nama,
                    'role' => $role === 'admin_poli' ? "Admin ($nama_poli)" : "Perawat ($nama_poli)",
                    'username' => $username,
                    'password' => $password,
                    'isReset' => false
                ];
                header('Location: ' . BASE_URL . '/super-admin/users?status=success');
                exit;
            } else {
                $_SESSION['flash_error'] = "Validasi gagal. Pastikan semua kolom diisi dengan benar.";
            }
        }
        header('Location: ' . BASE_URL . '/super-admin/users');
        exit;
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['user_id'] ?? 0);
            $password = $_POST['new_password'] ?? '';

            if ($id > 0 && strlen($password) >= 8) {
                $pdo = getDbConnection();
                
                // Check if user exists and is not super admin
                $stmt = $pdo->prepare("SELECT u.username, u.full_name, u.role, p.nama as nama_poli FROM users u LEFT JOIN polis p ON u.poli_id = p.id WHERE u.id = :id AND u.role != 'super_admin'");
                $stmt->execute([':id' => $id]);
                $user = $stmt->fetch();
                
                if ($user) {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
                    $stmt->execute([':hash' => $hash, ':id' => $id]);

                    $_SESSION['admin_baru'] = [
                        'fullName' => $user['full_name'],
                        'role' => $user['role'] === 'admin_poli' ? "Admin ({$user['nama_poli']})" : "Perawat ({$user['nama_poli']})",
                        'username' => $user['username'],
                        'password' => $password,
                        'isReset' => true
                    ];
                    header('Location: ' . BASE_URL . '/super-admin/users?status=success');
                    exit;
                }
            }
            $_SESSION['flash_error'] = "Gagal mereset password. Pastikan password minimal 8 karakter.";
        }
        header('Location: ' . BASE_URL . '/super-admin/users');
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['user_id'] ?? 0);
            
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'super_admin'");
            $stmt->execute([':id' => $id]);
            
            $_SESSION['flash_info'] = "Pengguna berhasil dihapus.";
        }
        header('Location: ' . BASE_URL . '/super-admin/users');
        exit;
    }
}
