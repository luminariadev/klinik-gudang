<?php
namespace App\Controllers;

use App\Helpers\View;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use PDO;

class GlobalInventoryReportController {
    public function __construct() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireRole('super_admin');
    }

    public function index() {
        $user = Auth::user();
        $pdo = getDbConnection();

        // Parameter filter
        $poliFilter     = $_GET['poli'] ?? '';
        $kategoriFilter = $_GET['kategori'] ?? '';
        $statusFilter   = $_GET['status'] ?? '';
        $search         = trim($_GET['search'] ?? '');

        // Query daftar poli & kategori
        $polis = $pdo->query("SELECT id, nama FROM polis ORDER BY nama ASC")->fetchAll();
        $kategoris = $pdo->query("SELECT nama FROM categories ORDER BY nama ASC")->fetchAll(PDO::FETCH_COLUMN);

        // Build Where
        $where = ["1=1"];
        $params = [];

        if ($poliFilter !== '') {
            $where[] = "i.poli_id = :poli";
            $params[':poli'] = $poliFilter;
        }
        if ($kategoriFilter !== '') {
            $where[] = "i.kategori = :kategori";
            $params[':kategori'] = $kategoriFilter;
        }
        if ($statusFilter !== '') {
            $where[] = "i.status = :status";
            $params[':status'] = $statusFilter;
        }
        if ($search !== '') {
            $where[] = "(i.nama LIKE :search1 OR i.sku LIKE :search2)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT i.*, p.nama AS poli_nama 
            FROM items i
            LEFT JOIN polis p ON i.poli_id = p.id
            WHERE $whereClause
            ORDER BY p.nama ASC, i.kategori ASC, i.nama ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        // Export Actions
        $export = $_GET['export'] ?? '';
        if ($export === 'csv') {
            $this->exportCsv($items);
            exit;
        } elseif ($export === 'pdf') {
            $this->exportPdf($items, $poliFilter, $kategoriFilter, $statusFilter);
            exit;
        }

        View::render('super_admin/laporan_inventaris', [
            'title' => 'Laporan Inventaris Global',
            'user' => $user,
            'items' => $items,
            'polis' => $polis,
            'kategoris' => $kategoris,
            'poliFilter' => $poliFilter,
            'kategoriFilter' => $kategoriFilter,
            'statusFilter' => $statusFilter,
            'search' => $search
        ]);
    }

    private function exportCsv($items) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan-Inventaris-Global-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Poli', 'Nama Barang', 'SKU', 'Kategori', 'Stok', 'Satuan', 'Status', 'Catatan'], ',', '"', "\\");
        
        foreach ($items as $item) {
            fputcsv($out, [
                $item['poli_nama'] ?? 'Tanpa Poli',
                $item['nama'],
                $item['sku'],
                $item['kategori'],
                $item['stok'],
                $item['satuan'],
                $item['status'] === 'low' ? 'Stok Rendah' : 'Tersedia',
                $item['catatan'] ?? ''
            ], ',', '"', "\\");
        }
        fclose($out);
    }

    private function exportPdf($items, $poliFilter, $kategoriFilter, $statusFilter) {
        $user = Auth::user();
        
        // Buat string filter untuk header
        $filterLabelParts = [];
        if ($poliFilter !== '') {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT nama FROM polis WHERE id = ?");
            $stmt->execute([$poliFilter]);
            $filterLabelParts[] = "Poli: " . $stmt->fetchColumn();
        }
        if ($kategoriFilter !== '') $filterLabelParts[] = "Kategori: " . $kategoriFilter;
        if ($statusFilter !== '')   $filterLabelParts[] = $statusFilter === 'low' ? 'Stok Rendah' : 'Tersedia';
        $filterLabel = $filterLabelParts ? implode(' · ', $filterLabelParts) : 'Semua Data Global';

        $totalStok = 0;
        $totalTersedia = 0;
        $totalRendah = 0;
        $grouped = [];

        foreach ($items as $it) {
            $totalStok += (int) $it['stok'];
            if ($it['status'] === 'low') $totalRendah++;
            else $totalTersedia++;
            
            $poli = $it['poli_nama'] ?? 'Tanpa Poli';
            $grouped[$poli][] = $it;
        }

        $generatedAt = date('d F Y, H:i') . ' WIB';
        
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
          .poli-heading { font-size: 11.5px; font-weight: bold; color: #ffffff; background: #0f5238; padding: 5px 10px; margin: 14px 0 0 0; }
          table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
          table.items th { background: #e6e9e8; color: #404943; font-size: 8.5px; text-transform: uppercase; text-align: left; padding: 6px 8px; border-bottom: 1px solid #bfc9c1; }
          table.items td { padding: 6px 8px; border-bottom: 1px solid #e1e3e2; vertical-align: top; }
          table.items tr:nth-child(even) td { background: #f8faf9; }
          .col-center { text-align: center; }
          .nama-cell { font-weight: bold; }
          .sku-cell { color: #707973; font-size: 9px; }
          .status-pill { font-weight: bold; font-size: 9px; }
          .catatan-cell { color: #707973; font-size: 9px; font-style: italic; }
        </style>
        </head>
        <body>
          <div class="header-bar">
            <table>
              <tr>
                <td style="width: 40px;"><?= $logoTag ?></td>
                <td>
                  <p class="header-title">Vitalis Admin — Rekap Global</p>
                  <p class="header-sub">Klinik Pratama UIN Sunan Gunung Djati Bandung</p>
                </td>
                <td class="header-meta">Dicetak oleh: <?= htmlspecialchars($user['full_name']) ?><br><?= htmlspecialchars($generatedAt) ?></td>
              </tr>
            </table>
          </div>
          <div class="footer-bar">
            <table>
              <tr>
                <td>Laporan Inventaris Global — Vitalis Admin v2.6.0</td>
                <td style="text-align: right;">
                  <script type="text/php">if (isset($pdf)) { $pdf->page_text(520, 800, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $fontMetrics->getFont("Helvetica"), 9, array(0.44,0.47,0.45)); }</script>
                </td>
              </tr>
            </table>
          </div>
          <p class="report-title">Laporan Inventaris Global</p>
          <p class="report-sub">Filter: <?= htmlspecialchars($filterLabel) ?></p>
          <table class="summary-table">
            <tr>
              <td><span class="summary-label">Total Item</span><span class="summary-value"><?= number_format(count($items), 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Total Stok Fisik</span><span class="summary-value"><?= number_format($totalStok, 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Item Tersedia</span><span class="summary-value"><?= number_format($totalTersedia, 0, ',', '.') ?></span></td>
              <td><span class="summary-label">Item Stok Rendah</span><span class="summary-value error"><?= number_format($totalRendah, 0, ',', '.') ?></span></td>
            </tr>
          </table>
          
          <?php if (empty($items)): ?>
            <p class="empty-state" style="text-align:center;color:#707973;">Tidak ada data barang yang sesuai filter.</p>
          <?php else: ?>
            <?php foreach ($grouped as $poli => $groupItems): ?>
              <div class="poli-heading">Poli: <?= htmlspecialchars($poli) ?> — <?= count($groupItems) ?> item</div>
              <table class="items">
                <thead>
                  <tr>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 15%;">SKU</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;" class="col-center">Stok</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 20%;">Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($groupItems as $item): ?>
                  <tr>
                    <td class="nama-cell"><?= htmlspecialchars($item['nama']) ?></td>
                    <td class="sku-cell"><?= htmlspecialchars($item['sku']) ?></td>
                    <td><?= htmlspecialchars($item['kategori']) ?></td>
                    <td class="col-center"><?= (int) $item['stok'] ?> <?= htmlspecialchars($item['satuan']) ?></td>
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

        $autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            echo "<script>alert('Peringatan: Library DomPDF belum diinstal (vendor/autoload.php tidak ditemukan). Menampilkan laporan dalam format cetak standar. Silakan gunakan fitur Print browser (Ctrl+P) lalu simpan sebagai PDF.'); window.print();</script>";
            echo $html;
            exit;
        }

        require_once $autoloadPath;
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'Laporan-Inventaris-Global-' . date('Ymd-His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
