<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class GlobalReportController {
    public function __construct() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $user = Auth::user();
        $pdo = getDbConnection();

        $poliFilter = $_GET['poli'] ?? '';
        $perawatFilter = $_GET['perawat'] ?? '';
        $kategoriFilter = $_GET['kategori'] ?? '';

        $params = [];
        $where = ["1=1"];

        if ($poliFilter !== '') {
            $where[] = "i.poli_id = :poli";
            $params[':poli'] = $poliFilter;
        }
        if ($perawatFilter !== '') {
            $where[] = "ul.user_id = :perawat";
            $params[':perawat'] = $perawatFilter;
        }
        if ($kategoriFilter !== '') {
            $where[] = "i.kategori = :kategori";
            $params[':kategori'] = $kategoriFilter;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT 
                ul.id, 
                ul.jumlah, 
                ul.created_at, 
                ul.catatan,
                i.nama AS item_nama,
                i.sku,
                i.kategori,
                i.satuan,
                u.full_name AS user_nama,
                u.role,
                p.nama AS poli_nama
            FROM usage_logs ul
            JOIN items i ON ul.item_id = i.id
            JOIN users u ON ul.user_id = u.id
            LEFT JOIN polis p ON i.poli_id = p.id
            WHERE $whereClause
            ORDER BY ul.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        // Data for dropdowns
        $stmtPoli = $pdo->query("SELECT id, nama FROM polis ORDER BY nama");
        $polis = $stmtPoli->fetchAll();

        $stmtUser = $pdo->query("SELECT id, full_name, role FROM users WHERE role IN ('perawat', 'admin_poli') ORDER BY full_name");
        $users = $stmtUser->fetchAll();

        $stmtKat = $pdo->query("SELECT nama FROM categories ORDER BY nama");
        $kategoris = $stmtKat->fetchAll(PDO::FETCH_COLUMN);

        View::render('super_admin/laporan_penggunaan', [
            'title' => 'Laporan Penggunaan (Global)',
            'user' => $user,
            'logs' => $logs,
            'polis' => $polis,
            'users' => $users,
            'kategoris' => $kategoris,
            'poliFilter' => $poliFilter,
            'perawatFilter' => $perawatFilter,
            'kategoriFilter' => $kategoriFilter
        ]);
    }
}
