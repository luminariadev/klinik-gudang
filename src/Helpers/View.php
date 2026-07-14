<?php
namespace App\Helpers;

class View {
    public static function render($view, $data = []) {
        extract($data);
        
        // Cek jika view adalah halaman auth, tidak perlu layout utama
        if (strpos($view, 'auth/') === 0) {
            $viewFile = __DIR__ . '/../../views/pages/' . $view . '.php';
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                die("View $view not found.");
            }
            return;
        }

        // Untuk halaman lain, set contentView lalu panggil layout
        $contentView = $view;
        $layoutFile = __DIR__ . '/../../views/layouts/app.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            die("Layout not found.");
        }
    }
}
