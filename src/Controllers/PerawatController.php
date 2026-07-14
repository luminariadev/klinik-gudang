<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class PerawatController {
    public function __construct() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireRole('perawat');
    }

    public function dataBarang() {
        $user = Auth::user();
        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        $kategoriFilter = $_GET['kategori'] ?? '';
        $search         = trim($_GET['search'] ?? '');

        $kategoris = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        
        $where = ["poli_id = :poli_id"];
        $params = [':poli_id' => $poli_id];

        if ($kategoriFilter !== '') {
            $where[] = "kategori = :kategori";
            $params[':kategori'] = $kategoriFilter;
        }
        if ($search !== '') {
            $where[] = "(nama LIKE :search OR sku LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $whereClause = implode(' AND ', $where);
        
        $stmt = $pdo->prepare("SELECT * FROM items WHERE $whereClause ORDER BY nama ASC");
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        View::render('perawat/data_barang', [
            'title' => 'Data Barang',
            'user' => $user,
            'items' => $items,
            'kategoris' => $kategoris,
            'kategoriFilter' => $kategoriFilter,
            'search' => $search
        ]);
    }

    public function ambilBarang() {
        $user = Auth::user();
        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        $kategoriFilter = $_GET['kategori'] ?? '';
        $search         = trim($_GET['search'] ?? '');

        $kategoris = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        
        $where = ["poli_id = :poli_id"];
        $params = [':poli_id' => $poli_id];

        if ($kategoriFilter !== '') {
            $where[] = "kategori = :kategori";
            $params[':kategori'] = $kategoriFilter;
        }
        if ($search !== '') {
            $where[] = "(nama LIKE :search OR sku LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $whereClause = implode(' AND ', $where);
        
        $stmt = $pdo->prepare("SELECT * FROM items WHERE $whereClause ORDER BY nama ASC");
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        View::render('perawat/ambil_barang', [
            'title' => 'Pengambilan Barang',
            'user' => $user,
            'items' => $items,
            'kategoris' => $kategoris,
            'kategoriFilter' => $kategoriFilter,
            'search' => $search
        ]);
    }
}
