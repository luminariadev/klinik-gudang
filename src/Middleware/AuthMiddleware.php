<?php
// src/Middleware/AuthMiddleware.php
namespace App\Middleware;

use App\Helpers\Auth;

class AuthMiddleware {
    public static function requireLogin() {
        if (!Auth::check()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }

    public static function requireRole($allowedRoles) {
        self::requireLogin();
        $user = Auth::user();
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        if (!in_array($user['role'], $allowedRoles)) {
            header("Location: " . BASE_URL . "/unauthorized");
            exit;
        }
    }

    public static function guestOnly() {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user['role'] === 'super_admin') {
                header("Location: " . BASE_URL . "/super-admin");
            } else {
                header("Location: " . BASE_URL . "/dashboard");
            }
            exit;
        }
    }
}
