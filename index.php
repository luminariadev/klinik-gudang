<?php
// index.php
require_once __DIR__ . '/config/database.php';

// Mulai session di satu tempat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sederhana Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Simple Router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Hapus prefix jika di-deploy di subfolder, misal '/stock_gudang'
$base_path = '/stock_gudang'; // Sesuaikan jika perlu
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}
if ($uri == '') $uri = '/';

switch ($uri) {
    case '/':
    case '/login':
        require __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processLogin();
        } else {
            $controller->login();
        }
        break;
    case '/logout':
        require __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new \App\Controllers\AuthController();
        $controller->logout();
        break;
        
    // Super Admin Routes
    case '/super-admin':
        require __DIR__ . '/src/Controllers/SuperAdminController.php';
        $controller = new \App\Controllers\SuperAdminController();
        $controller->dashboard();
        break;

    // Fitur Super Admin: Master Poli
    case '/super-admin/polis':
        require __DIR__ . '/src/Controllers/MasterPoliController.php';
        $controller = new \App\Controllers\MasterPoliController();
        $controller->index();
        break;
    case '/super-admin/polis/store':
        require __DIR__ . '/src/Controllers/MasterPoliController.php';
        $controller = new \App\Controllers\MasterPoliController();
        $controller->store();
        break;
    case '/super-admin/polis/delete':
        require __DIR__ . '/src/Controllers/MasterPoliController.php';
        $controller = new \App\Controllers\MasterPoliController();
        $controller->delete();
        break;

    // Fitur Super Admin: Master Kategori
    case '/super-admin/kategori':
        require __DIR__ . '/src/Controllers/MasterKategoriController.php';
        $controller = new \App\Controllers\MasterKategoriController();
        $controller->index();
        break;
    case '/super-admin/kategori/store':
        require __DIR__ . '/src/Controllers/MasterKategoriController.php';
        $controller = new \App\Controllers\MasterKategoriController();
        $controller->store();
        break;
    case '/super-admin/kategori/delete':
        require __DIR__ . '/src/Controllers/MasterKategoriController.php';
        $controller = new \App\Controllers\MasterKategoriController();
        $controller->delete();
        break;

    // Fitur Super Admin: Master Barang Global
    case '/super-admin/barang':
        require __DIR__ . '/src/Controllers/GlobalItemController.php';
        $controller = new \App\Controllers\GlobalItemController();
        $controller->index();
        break;
    case '/super-admin/barang/create':
        require __DIR__ . '/src/Controllers/GlobalItemController.php';
        $controller = new \App\Controllers\GlobalItemController();
        $controller->create();
        break;
    case '/super-admin/barang/store':
        require __DIR__ . '/src/Controllers/GlobalItemController.php';
        $controller = new \App\Controllers\GlobalItemController();
        $controller->store();
        break;
    case '/super-admin/barang/action':
        require __DIR__ . '/src/Controllers/GlobalItemController.php';
        $controller = new \App\Controllers\GlobalItemController();
        $controller->action();
        break;

    // Fitur Super Admin: Laporan Penggunaan Global
    case '/super-admin/laporan-penggunaan':
        require __DIR__ . '/src/Controllers/GlobalReportController.php';
        $controller = new \App\Controllers\GlobalReportController();
        $controller->index();
        break;

    // Fitur Super Admin: Laporan Inventaris Global (Eksport)
    case '/super-admin/laporan-inventaris':
        require __DIR__ . '/src/Controllers/GlobalInventoryReportController.php';
        $controller = new \App\Controllers\GlobalInventoryReportController();
        $controller->index();
        break;

    case '/super-admin/users':
        require __DIR__ . '/src/Controllers/MasterUserController.php';
        $controller = new \App\Controllers\MasterUserController();
        $controller->index();
        break;

    case '/super-admin/users/store':
        require __DIR__ . '/src/Controllers/MasterUserController.php';
        $controller = new \App\Controllers\MasterUserController();
        $controller->store();
        break;

    case '/super-admin/users/reset-password':
        require __DIR__ . '/src/Controllers/MasterUserController.php';
        $controller = new \App\Controllers\MasterUserController();
        $controller->resetPassword();
        break;

    case '/super-admin/users/delete':
        require __DIR__ . '/src/Controllers/MasterUserController.php';
        $controller = new \App\Controllers\MasterUserController();
        $controller->delete();
        break;
        
    // Poli Routes
    case '/dashboard':
        require __DIR__ . '/src/Controllers/PoliController.php';
        $controller = new \App\Controllers\PoliController();
        $controller->dashboard();
        break;

    // Fitur Admin Poli: Master Barang & Input Barang
    case '/items':
        require __DIR__ . '/src/Controllers/ItemController.php';
        $controller = new \App\Controllers\ItemController();
        $controller->index();
        break;
    case '/items/create':
        require __DIR__ . '/src/Controllers/ItemController.php';
        $controller = new \App\Controllers\ItemController();
        $controller->create();
        break;
    
    // Fitur Perawat
    case '/perawat/barang':
        require __DIR__ . '/src/Controllers/PerawatController.php';
        $controller = new \App\Controllers\PerawatController();
        $controller->dataBarang();
        break;
    case '/perawat/ambil':
        require __DIR__ . '/src/Controllers/PerawatController.php';
        $controller = new \App\Controllers\PerawatController();
        $controller->ambilBarang();
        break;

    case '/items/store':
        require __DIR__ . '/src/Controllers/ItemController.php';
        $controller = new \App\Controllers\ItemController();
        $controller->store();
        break;

    case '/action':
        require __DIR__ . '/src/Controllers/ItemController.php';
        $controller = new \App\Controllers\ItemController();
        $controller->action();
        break;
        
    case '/laporan':
        require __DIR__ . '/src/Controllers/ReportController.php';
        $controller = new \App\Controllers\ReportController();
        $controller->index();
        break;

    case '/laporan/pdf':
        require __DIR__ . '/src/Controllers/ReportController.php';
        $controller = new \App\Controllers\ReportController();
        $controller->exportPdf();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
