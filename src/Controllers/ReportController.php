<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class ReportController {
    /**
     * Konstruktor: Memastikan hanya user yang login yang bisa mengakses
     */
    public function __construct() {
        AuthMiddleware::requireLogin();
    }

    /**
     * Menampilkan halaman laporan inventaris (beserta filter dan export CSV)
     */
    public function index() {
        $user = Auth::user();
        if ($user['role'] === 'super_admin') {
            header("Location: " . BASE_URL . "/super-admin");
            exit;
        }

        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        // Filter dari query string
        $kategoriFilter = $_GET['kategori'] ?? '';
        $statusFilter   = $_GET['status'] ?? '';
        $dariTanggal    = $_GET['dari'] ?? '';
        $sampaiTanggal  = $_GET['sampai'] ?? '';

        $kategoriList = ['Obat', 'Alkes', 'Habis Pakai'];

        // Data laporan
        $totalItem       = 0;
        $totalStok       = 0;
        $stokRendah      = 0;
        $kategoriSummary = [];
        $items           = [];
        $lowStockItems   = [];

        try {
            // Ringkasan umum
            $stmt = $pdo->prepare('SELECT COUNT(*) AS jumlah, COALESCE(SUM(stok), 0) AS total FROM items WHERE poli_id = :poli_id');
            $stmt->execute([':poli_id' => $poli_id]);
            $row = $stmt->fetch();
            if ($row) {
                $totalItem = (int) $row['jumlah'];
                $totalStok = (int) $row['total'];
            }

            $stmt = $pdo->prepare('SELECT COUNT(*) AS jumlah FROM items WHERE poli_id = :poli_id AND status = "low"');
            $stmt->execute([':poli_id' => $poli_id]);
            $row = $stmt->fetch();
            if ($row) $stokRendah = (int) $row['jumlah'];

            // Breakdown per kategori
            $stmt = $pdo->prepare('SELECT kategori, COUNT(*) AS jumlah_item, COALESCE(SUM(stok), 0) AS total_stok
                                  FROM items WHERE poli_id = :poli_id GROUP BY kategori ORDER BY total_stok DESC');
            $stmt->execute([':poli_id' => $poli_id]);
            $kategoriSummary = $stmt->fetchAll();

            // Item stok rendah
            $stmt = $pdo->prepare('SELECT nama, sku, kategori, stok, satuan, ambang_minimum
                                  FROM items WHERE poli_id = :poli_id AND status = "low" ORDER BY stok ASC LIMIT 6');
            $stmt->execute([':poli_id' => $poli_id]);
            $lowStockItems = $stmt->fetchAll();

            // Query utama dengan filter
            $where  = ['poli_id = :poli_id'];
            $params = [':poli_id' => $poli_id];

            if ($kategoriFilter !== '' && in_array($kategoriFilter, $kategoriList, true)) {
                $where[] = 'kategori = :kategori';
                $params[':kategori'] = $kategoriFilter;
            }
            if ($statusFilter === 'low' || $statusFilter === 'available') {
                $where[] = 'status = :status';
                $params[':status'] = $statusFilter;
            }
            if ($dariTanggal !== '') {
                $where[] = 'DATE(created_at) >= :dari';
                $params[':dari'] = $dariTanggal;
            }
            if ($sampaiTanggal !== '') {
                $where[] = 'DATE(created_at) <= :sampai';
                $params[':sampai'] = $sampaiTanggal;
            }

            $sql = 'SELECT nama, sku, kategori, stok, satuan, status, created_at FROM items WHERE '
                 . implode(' AND ', $where) . ' ORDER BY nama ASC';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $items = $stmt->fetchAll();

        } catch (\Throwable $e) {
            // Error handling
        }

        // Export CSV
        if (($_GET['export'] ?? '') === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="laporan-inventaris-poli-' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Nama Barang', 'SKU', 'Kategori', 'Stok', 'Satuan', 'Status', 'Tanggal Dicatat']);
            foreach ($items as $item) {
                fputcsv($out, [
                    $item['nama'],
                    $item['sku'],
                    $item['kategori'],
                    $item['stok'],
                    $item['satuan'],
                    $item['status'] === 'low' ? 'Stok Rendah' : 'Tersedia',
                    !empty($item['created_at']) ? date('Y-m-d', strtotime($item['created_at'])) : '',
                ]);
            }
            fclose($out);
            exit;
        }

        // Render tampilan
        View::render('poli/laporan', [
            'title' => 'Laporan Inventaris',
            'user' => $user,
            'kategoriFilter' => $kategoriFilter,
            'statusFilter' => $statusFilter,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
            'kategoriList' => $kategoriList,
            'totalItem' => $totalItem,
            'totalStok' => $totalStok,
            'stokRendah' => $stokRendah,
            'kategoriSummary' => $kategoriSummary,
            'lowStockItems' => $lowStockItems,
            'items' => $items
        ]);
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf() {
        $user = Auth::user();
        if ($user['role'] === 'super_admin') {
            header("Location: " . BASE_URL . "/super-admin");
            exit;
        }

        $pdo = getDbConnection();
        $poli_id = $user['poli_id'];

        $kategoriFilter = $_GET['kategori'] ?? '';
        $statusFilter   = $_GET['status'] ?? '';
        $kategoriList   = ['Obat', 'Alkes', 'Habis Pakai'];

        if ($kategoriFilter !== '' && !in_array($kategoriFilter, $kategoriList, true)) {
            $kategoriFilter = '';
        }
        if (!in_array($statusFilter, ['available', 'low'], true)) {
            $statusFilter = '';
        }

        $where  = ['poli_id = :poli_id'];
        $params = [':poli_id' => $poli_id];

        if ($kategoriFilter !== '') {
            $where[] = 'kategori = :kategori';
            $params[':kategori'] = $kategoriFilter;
        }
        if ($statusFilter !== '') {
            $where[] = 'status = :status';
            $params[':status'] = $statusFilter;
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $pdo->prepare("
            SELECT nama, sku, kategori, stok, ambang_minimum, satuan, status, catatan
            FROM items
            WHERE $whereSql
            ORDER BY kategori ASC, nama ASC
        ");
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        // Query nama poli
        $stmtPoli = $pdo->prepare("SELECT nama FROM polis WHERE id = :id");
        $stmtPoli->execute([':id' => $poli_id]);
        $namaPoli = $stmtPoli->fetchColumn();

        $totalStok = 0;
        $totalTersedia = 0;
        $totalRendah = 0;
        $grouped = [];

        foreach ($items as $it) {
            $totalStok += (int) $it['stok'];
            if ($it['status'] === 'low') {
                $totalRendah++;
            } else {
                $totalTersedia++;
            }
            $grouped[$it['kategori']][] = $it;
        }

        $generatedAt = date('d F Y, H:i') . ' WIB';
        $filterLabelParts = [];
        if ($kategoriFilter !== '') $filterLabelParts[] = $kategoriFilter;
        if ($statusFilter !== '')   $filterLabelParts[] = $statusFilter === 'low' ? 'Stok Rendah' : 'Tersedia';
        $filterLabel = $filterLabelParts ? implode(' · ', $filterLabelParts) : 'Semua Kategori & Status';

        $logoPath = dirname(__DIR__, 2) . '/public/assets/logo-uin.png';
        if (!file_exists($logoPath)) {
            $logoPath = dirname(__DIR__, 2) . '/assets/logo-uin.png';
        }
        $logoTag  = file_exists($logoPath) ? '<img src="' . $logoPath . '" class="logo">' : '';

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8">
        <style>
          @page { margin: 100px 40px 70px 40px; }
          * { box-sizing: border-box; }
          body { font-family: 'Helvetica', Arial, sans-serif; color: #191c1c; font-size: 10.5px; }
          .header-bar { position: fixed; top: -85px; left: 0; right: 0; height: 70px; border-bottom: 2px solid #0f5238; padding-bottom: 8px; }
          .header-bar table { width: 100%; border-collapse: collapse; }
          .header-bar .logo { width: 36px; height: 36px; }
          .header-title { font-size: 16px; font-weight: bold; color: #0f5238; margin: 0 0 2px 0; }
          .header-sub { font-size: 10px; color: #707973; margin: 0; }
          .header-meta { text-align: right; font-size: 9.5px; color: #707973; }
          .footer-bar { position: fixed; bottom: -55px; left: 0; right: 0; height: 40px; border-top: 1px solid #bfc9c1; padding-top: 6px; font-size: 9px; color: #707973; }
          .footer-bar table { width: 100%; }
          .report-title { font-size: 14px; font-weight: bold; color: #191c1c; margin: 0 0 2px 0; }
          .report-sub { font-size: 10px; color: #707973; margin: 0 0 16px 0; }
          .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
          .summary-table td { width: 25%; background: #f2f4f3; border: 1px solid #e1e3e2; padding: 8px 10px; text-align: left; }
          .summary-label { font-size: 8.5px; color: #707973; text-transform: uppercase; display: block; margin-bottom: 2px; }
          .summary-value { font-size: 15px; font-weight: bold; color: #0f5238; }
          .summary-value.error { color: #ba1a1a; }
          .kategori-heading { font-size: 11.5px; font-weight: bold; color: #ffffff; background: #0f5238; padding: 5px 10px; margin: 14px 0 0 0; }
          table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
          table.items th { background: #e6e9e8; color: #404943; font-size: 8.5px; text-transform: uppercase; text-align: left; padding: 6px 8px; border-bottom: 1px solid #bfc9c1; }
          table.items td { padding: 6px 8px; border-bottom: 1px solid #e1e3e2; vertical-align: top; }
          table.items tr:nth-child(even) td { background: #f8faf9; }
          .col-center { text-align: center; }
          .nama-cell { font-weight: bold; }
          .sku-cell { color: #707973; font-size: 9px; }
          .status-pill { font-weight: bold; font-size: 9px; }
          .catatan-cell { color: #707973; font-size: 9px; font-style: italic; }
          .empty-state { text-align: center; color: #707973; padding: 40px 0; font-size: 11px; }
        </style>
        </head>
        <body>
          <div class="header-bar">
            <table>
              <tr>
                <td style="width: 40px;"><?= $logoTag ?></td>
                <td>
                  <p class="header-title">Vitalis Admin — <?= htmlspecialchars($namaPoli) ?></p>
                  <p class="header-sub">Klinik Pratama UIN Sunan Gunung Djati Bandung</p>
                </td>
                <td class="header-meta">Dicetak oleh: <?= htmlspecialchars($user['full_name']) ?><br><?= htmlspecialchars($generatedAt) ?></td>
              </tr>
            </table>
          </div>
          <div class="footer-bar">
            <table>
              <tr>
                <td>Laporan Inventaris — Vitalis Admin v2.6.0</td>
                <td style="text-align: right;">
                  <script type="text/php">if (isset($pdf)) { $pdf->page_text(520, 800, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $fontMetrics->getFont("Helvetica"), 9, array(0.44,0.47,0.45)); }</script>
                </td>
              </tr>
            </table>
          </div>
          <p class="report-title">Laporan Inventaris <?= htmlspecialchars($namaPoli) ?></p>
          <p class="report-sub">Filter: <?= htmlspecialchars($filterLabel) ?></p>
          <table class="summary-table">
            <tr>
              <td><span class="summary-label">Total Item</span><span class="summary-value"><?= number_format(count($items), 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Total Stok</span><span class="summary-value"><?= number_format($totalStok, 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Tersedia</span><span class="summary-value"><?= number_format($totalTersedia, 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Stok Rendah</span><span class="summary-value error"><?= number_format($totalRendah, 0, ',', '.') ?></span></td>
            </tr>
          </table>
          <?php if (empty($items)): ?>
            <p class="empty-state">Tidak ada barang yang cocok dengan filter ini.</p>
          <?php else: ?>
            <?php foreach ($grouped as $kategori => $groupItems): ?>
              <div class="kategori-heading"><?= htmlspecialchars($kategori) ?> — <?= count($groupItems) ?> item</div>
              <table class="items">
                <thead>
                  <tr>
                    <th style="width: 28%;">Nama Barang</th>
                    <th style="width: 14%;">SKU</th>
                    <th style="width: 10%;" class="col-center">Stok</th>
                    <th style="width: 10%;" class="col-center">Ambang Min.</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 24%;">Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($groupItems as $item): ?>
                  <tr>
                    <td class="nama-cell"><?= htmlspecialchars($item['nama']) ?></td>
                    <td class="sku-cell"><?= htmlspecialchars($item['sku']) ?></td>
                    <td class="col-center"><?= (int) $item['stok'] ?> <?= htmlspecialchars($item['satuan']) ?></td>
                    <td class="col-center"><?= (int) $item['ambang_minimum'] ?></td>
                    <td class="status-pill" style="color: <?= $item['status'] === 'low' ? '#ba1a1a' : '#0f5238' ?>;">
                      <?= $item['status'] === 'low' ? 'Stok Rendah' : 'Tersedia' ?>
                    </td>
                    <td class="catatan-cell"><?= htmlspecialchars($item['catatan'] ?? '—') ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endforeach; ?>
          <?php endif; ?>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filenameSuffix = $filterLabel !== 'Semua Kategori & Status' ? '-Filtered' : '';
        $filename = 'Laporan-Inventaris-' . preg_replace('/[^a-zA-Z0-9]+/', '', $namaPoli) . $filenameSuffix . '-' . date('Ymd-His') . '.pdf';

        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
