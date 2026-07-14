<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\View;
use App\Helpers\ActivityLogger;
use App\Middleware\AuthMiddleware;

class AuthController {
    /**
     * Menampilkan halaman login
     */
    public function login() {
        // Mencegah user yang sudah login mengakses halaman ini
        AuthMiddleware::guestOnly();
        
        $error = '';
        if (isset($_SESSION['login_error'])) {
            $error = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        
        // Render tampilan form login dari folder views
        View::render('auth/login', ['error' => $error]);
    }

    /**
     * Memproses data form login (POST)
     */
    public function processLogin() {
        // Pastikan hanya tamu yang bisa login
        AuthMiddleware::guestOnly();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validasi input kosong
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Username dan Password wajib diisi.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $pdo = getDbConnection();
        // Mencari data user di tabel users berdasarkan username
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role, poli_id, full_name FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Jika user ditemukan dan password cocok
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session login dan token remember me jika dicentang
            Auth::login($user, $remember);
            
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_poli_id'] = $user['poli_id'];
            
            ActivityLogger::log($user['id'], 'login', 'User logged in');

            // Redirect sesuai dengan role pengguna
            if ($user['role'] === 'super_admin') {
                header("Location: " . BASE_URL . "/super-admin");
            } else {
                header("Location: " . BASE_URL . "/dashboard");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Username atau password salah.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }

    /**
     * Memproses logout user
     */
    public function logout() {
        // Hapus semua session dan token di database
        Auth::logout();
        header("Location: " . BASE_URL . "/login");
        exit;
    }
}
