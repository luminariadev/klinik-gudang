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
            'total_kategori' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
            'usage_this_month' => $pdo->query("SELECT COALESCE(SUM(jumlah), 0) FROM usage_logs WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn(),
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

        // Aktivitas Terbaru
        $recentActivities = $pdo->query("
            SELECT a.action, a.description, a.created_at, u.full_name, u.role
            FROM activity_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 5
        ")->fetchAll();

        View::render('super_admin/dashboard', [
            'title' => 'Dashboard Super Admin',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'polis' => $polis,
            'recentActivities' => $recentActivities
        ]);
    }

    // Nanti akan ada fungsi untuk manage polis dan users
}
