<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use PDO;

class SuperAdminController {
    public function __construct() {
        AuthMiddleware::requireRole('super_admin');
    }

    public function dashboard() {
        $pdo = getDbConnection();
        
        // Statistik Global
        $stats = [
            'total_polis' => $pdo->query("SELECT COUNT(*) FROM polis")->fetchColumn(),
            'total_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'super_admin'")->fetchColumn(),
            'total_items' => $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn(),
            'low_stock'   => $pdo->query("SELECT COUNT(*) FROM items WHERE status = 'low' OR stok <= ambang_minimum")->fetchColumn(),
        ];

        // Daftar Poli beserta jumlah stoknya
        $polis = $pdo->query("
            SELECT p.id, p.nama, 
                   COUNT(i.id) as item_count, 
                   COALESCE(SUM(i.stok), 0) as total_stok
            FROM polis p
            LEFT JOIN items i ON p.id = i.poli_id
            GROUP BY p.id
        ")->fetchAll();

        View::render('super_admin/dashboard', [
            'title' => 'Dashboard Super Admin',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'polis' => $polis
        ]);
    }

    // Nanti akan ada fungsi untuk manage polis dan users
}
