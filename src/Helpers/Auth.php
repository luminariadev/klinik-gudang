<?php
// src/Helpers/Auth.php
namespace App\Helpers;

use PDO;

class Auth {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            return true;
        }

        // Cek remember token
        if (isset($_COOKIE['remember_token'])) {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT id, username, role, poli_id, full_name FROM users WHERE remember_token = :token LIMIT 1");
            $stmt->execute([':token' => $_COOKIE['remember_token']]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['poli_id']   = $user['poli_id'];
                $_SESSION['full_name'] = $user['full_name'];
                return true;
            } else {
                // Token tidak valid, hapus cookie
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
        return false;
    }

    public static function user() {
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'poli_id' => $_SESSION['poli_id'],
                'full_name' => $_SESSION['full_name'],
            ];
        }
        return null;
    }
    
    public static function login($user, $remember = false) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['poli_id']   = $user['poli_id'];
        $_SESSION['full_name'] = $user['full_name'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
            $stmt->execute([':token' => $token, ':id' => $user['id']]);
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        }
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!empty($_SESSION['user_id'])) {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);
        }

        $_SESSION = [];
        session_destroy();

        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
}
