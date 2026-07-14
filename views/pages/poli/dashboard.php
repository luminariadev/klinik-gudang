<?php
// Tampilan ini disertakan dari PoliController
// $totalStok, $stokHampirHabis, $items, $kategoriSummary, $daftarKategori sudah tersedia.
$totalRendahCount = 0;
foreach ($items as $it) { if ($it['status'] === 'low') $totalRendahCount++; }
$totalTersediaCount = count($items) - $totalRendahCount;
$totalItemCount = count($items);

$chartLabels = $daftarKategori;
$chartStokTersedia = array_map(
    fn($kat) => (int) ($kategoriSummary[$kat]['stok_tersedia'] ?? 0),
    $daftarKategori
);
$totalStokTersedia = array_sum($chartStokTersedia);

function statusBadge(string $status, string $context = 'table'): string {
    $isLow = $status === 'low';
    $color = $isLow ? 'error' : 'primary';
    $label = $isLow ? 'Stok Rendah' : 'Tersedia';
    $size  = $context === 'card' ? 'text-[11px] px-2.5 py-1' : 'text-[13px]';
    return '<div class="inline-flex items-center gap-1.5 ' . $size . ' font-bold rounded-full ' . ($context === 'card' ? "bg-{$color}/10 text-{$color}" : "text-{$color}") . '">'
         . '<span class="w-1.5 h-1.5 rounded-full bg-' . $color . ($isLow ? ' animate-pulse' : '') . '"></span>' . $label . '</div>';
}

function kategoriBadge(string $kategori): string {
    $map = [
        'Obat' => 'bg-secondary-container text-on-secondary-container',
        'Alkes' => 'bg-tertiary-container/20 text-tertiary',
        'Habis Pakai' => 'bg-primary-fixed text-on-primary-fixed-variant',
    ];
    $cls = $map[$kategori] ?? 'bg-surface-variant text-on-surface-variant';
    return '<span class="px-2.5 py-1 ' . $cls . ' rounded-full text-[11px] font-bold uppercase tracking-wide whitespace-nowrap">' . htmlspecialchars($kategori) . '</span>';
}

function kategoriIcon(string $kategori): string {
    $map = [
        'Obat' => 'medication',
        'Alkes' => 'medical_services',
        'Habis Pakai' => 'inventory_2',
    ];
    return $map[$kategori] ?? 'category';
}
?>

<div class="mb-lg flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md">
  <div>
    <h2 class="section-title-bar font-display text-[22px] sm:text-[26px] font-bold text-primary mb-2">Ringkasan Inventaris</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px] mt-2">Pantau ketersediaan logistik medis secara real-time.</p>
  </div>
  <?php if ($user['role'] !== 'perawat'): ?>
  <a href="<?= BASE_URL ?>/items/create" class="flex items-center justify-center gap-sm bg-primary text-white px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all shadow-card w-full sm:w-auto">
    <span class="material-symbols-outlined text-[20px]">add</span>
    <span>Tambah Barang Baru</span>
  </a>
  <?php endif; ?>
</div>

<!-- Grafik Stok Tersedia -->
<div class="chart-card animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card transition-shadow relative overflow-hidden mb-8" style="animation-delay:.02s">
  <div class="absolute top-0 left-0 right-0 h-1" style="background:linear-gradient(90deg,#0f5238,#95d4b3,#0f5238); background-size:200% 100%; animation: shimmer 4s linear infinite;"></div>
  <div class="flex justify-between items-start mb-md relative">
    <div>
      <span class="text-[12px] font-bold uppercase tracking-widest text-outline">Jumlah Stok Tersedia <span id="stat-total-context" class="text-primary normal-case tracking-normal transition-opacity"></span></span>
      <div class="flex items-baseline gap-xs mt-1">
        <span id="stat-total-stok" class="text-[28px] font-display font-bold text-on-surface count-up" data-target="<?= (int) $totalStokTersedia ?>">0</span>
        <span class="text-[12px] text-outline">unit tersedia di semua kategori</span>
      </div>
    </div>
    <span class="icon-badge material-symbols-outlined text-primary bg-primary/10 p-2 rounded-xl text-[20px]">bar_chart</span>
  </div>
  <div class="relative h-56 mt-md">
    <canvas id="chartStokTersedia"
      data-labels='<?= json_encode($chartLabels) ?>'
      data-values='<?= json_encode($chartStokTersedia) ?>'></canvas>
  </div>
</div>

<!-- Stok per Kategori (tabel + grafik distribusi) -->
<div class="mb-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="p-lg border-b border-outline-variant/60 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
    <h3 class="font-display text-[15px] font-bold text-on-surface">Stok per Kategori</h3>
    <span class="text-[12px] text-outline">Klik baris untuk memfilter daftar di bawah</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-surface-container text-outline text-[11px] border-b border-outline-variant/60">
          <th class="py-md px-lg font-bold uppercase tracking-wider">Kategori</th>
          <th class="py-md px-md font-bold uppercase tracking-wider text-center">Item</th>
          <th class="py-md px-md font-bold uppercase tracking-wider text-center">Total Stok</th>
          <th class="py-md px-md font-bold uppercase tracking-wider">Distribusi</th>
          <th class="py-md px-md font-bold uppercase tracking-wider text-center">Tersedia</th>
          <th class="py-md px-lg font-bold uppercase tracking-wider text-center">Rendah</th>
        </tr>
      </thead>
      <tbody id="kategori-filter">
        <?php
          $pctTersediaAll = $totalItemCount > 0 ? round($totalTersediaCount / $totalItemCount * 100) : 0;
          $pctRendahAll   = $totalItemCount > 0 ? 100 - $pctTersediaAll : 0;
        ?>
        <tr data-kategori="semua" onclick="filterKategori('semua', this)"
            class="kategori-row active cursor-pointer transition-all duration-200 border-l-4 border-l-primary bg-primary/5 border-b border-outline-variant/30 animate-row" style="animation-delay:.03s">
          <td class="py-md px-lg">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">apps</span>
              <span class="font-display font-bold text-[14px] text-on-surface">Semua Kategori</span>
            </div>
          </td>
          <td class="py-md px-md text-center font-mono text-[13px]"><?= $totalItemCount ?></td>
          <td class="py-md px-md text-center font-mono text-[13px]"><?= number_format($totalStok, 0, ',', '.') ?></td>
          <td class="py-md px-md">
            <div class="w-full max-w-[140px] h-2.5 rounded-full overflow-hidden bg-surface-variant flex">
              <div class="h-full bg-primary transition-all duration-700" style="width:<?= $pctTersediaAll ?>%"></div>
              <div class="h-full bg-error transition-all duration-700" style="width:<?= $pctRendahAll ?>%"></div>
            </div>
          </td>
          <td class="py-md px-md text-center text-[13px] font-bold text-primary"><?= $totalTersediaCount ?></td>
          <td class="py-md px-lg text-center text-[13px] font-bold text-error"><?= $totalRendahCount ?></td>
        </tr>
        <?php foreach ($daftarKategori as $i => $kat):
            $s = $kategoriSummary[$kat] ?? ['jumlah_item' => 0, 'total_stok' => 0, 'jumlah_rendah' => 0, 'jumlah_tersedia' => 0];
            $jml = (int) $s['jumlah_item'];
            $pctTersedia = $jml > 0 ? round(((int) $s['jumlah_tersedia']) / $jml * 100) : 0;
            $pctRendah   = $jml > 0 ? 100 - $pctTersedia : 0;
        ?>
        <tr data-kategori="<?= htmlspecialchars($kat) ?>" onclick="filterKategori('<?= htmlspecialchars($kat) ?>', this)"
            class="kategori-row cursor-pointer transition-all duration-200 border-l-4 border-l-transparent hover:bg-surface-container-low border-b border-outline-variant/30 animate-row" style="animation-delay:<?= .08 + $i * .05 ?>s">
          <td class="py-md px-lg">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]"><?= kategoriIcon($kat) ?></span>
              <span class="font-display font-bold text-[14px] text-on-surface"><?= htmlspecialchars($kat) ?></span>
            </div>
          </td>
          <td class="py-md px-md text-center font-mono text-[13px]"><?= $jml ?></td>
          <td class="py-md px-md text-center font-mono text-[13px]"><?= number_format((int) $s['total_stok'], 0, ',', '.') ?></td>
          <td class="py-md px-md">
            <div class="w-full max-w-[140px] h-2.5 rounded-full overflow-hidden bg-surface-variant flex">
              <div class="h-full bg-primary transition-all duration-700" style="width:<?= $pctTersedia ?>%"></div>
              <div class="h-full bg-error transition-all duration-700" style="width:<?= $pctRendah ?>%"></div>
            </div>
          </td>
          <td class="py-md px-md text-center text-[13px] font-bold text-primary"><?= (int) $s['jumlah_tersedia'] ?></td>
          <td class="py-md px-lg text-center text-[13px] font-bold text-error"><?= (int) $s['jumlah_rendah'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="flex gap-2 p-md border-t border-outline-variant/60 bg-surface-container-low" id="status-filter">
    <button type="button" data-status="semua" onclick="filterStatus('semua', this)" class="status-chip active px-3 py-1.5 rounded-full text-[12px] font-bold border-2 border-primary text-primary bg-primary/5 shadow-[0_2px_8px_rgba(15,82,56,.15)]">Semua Status</button>
    <button type="button" data-status="available" onclick="filterStatus('available', this)" class="status-chip px-3 py-1.5 rounded-full text-[12px] font-bold border-2 border-outline-variant/60 text-on-surface-variant hover:border-primary/40 transition-all">Tersedia</button>
    <button type="button" data-status="low" onclick="filterStatus('low', this)" class="status-chip px-3 py-1.5 rounded-full text-[12px] font-bold border-2 border-outline-variant/60 text-on-surface-variant hover:border-primary/40 transition-all">Stok Rendah</button>
  </div>
</div>

<!-- Inventaris -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="p-lg border-b border-outline-variant/60 flex flex-col md:flex-row md:items-center justify-between gap-md">
    <h3 class="font-display text-[17px] font-bold text-on-surface">Daftar Inventaris</h3>
    <div class="flex flex-wrap items-center gap-sm">
      <button type="button" onclick="downloadLaporan()" class="flex items-center gap-xs px-md py-2 border border-outline-variant rounded-xl text-[13px] text-primary hover:bg-primary/5 transition-colors font-bold">
        <span class="material-symbols-outlined text-[18px]">download</span><span class="hidden sm:inline">Download List</span>
      </button>
      <span id="filter-summary" class="text-[12px] text-outline font-medium"></span>
    </div>
  </div>

  <?php if (empty($items)): ?>
    <div class="py-16 px-lg text-center">
      <span class="material-symbols-outlined text-[40px] text-outline-variant mb-2">inventory_2</span>
      <p class="text-on-surface-variant font-medium">Belum ada barang tercatat.</p>
    </div>
  <?php else: ?>
    <!-- Tampilan tabel: layar md ke atas -->
    <div class="desktop-table overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container text-outline text-[11px] border-b border-outline-variant/60">
            <th class="py-md px-lg font-bold uppercase tracking-wider">Nama Barang</th>
            <th class="py-md px-md font-bold uppercase tracking-wider">Kategori</th>
            <th class="py-md px-md font-bold uppercase tracking-wider text-center">Stok</th>
            <th class="py-md px-md font-bold uppercase tracking-wider">Status</th>
            <th class="py-md px-lg font-bold uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="table-body" class="divide-y divide-outline-variant/30">
          <?php foreach ($items as $i => $item): ?>
          <tr class="hover:bg-primary/[.03] hover:shadow-[inset_3px_0_0_#0f5238] transition-all group animate-row"
              style="animation-delay:<?= min($i * 0.03, 0.4) ?>s"
              data-kategori="<?= htmlspecialchars($item['kategori']) ?>"
              data-status="<?= htmlspecialchars($item['status']) ?>"
              data-nama="<?= htmlspecialchars(mb_strtolower($item['nama'])) ?>"
              data-stok="<?= (int) $item['stok'] ?>">
            <td class="py-md px-lg">
              <div class="font-bold text-on-surface text-[14px]"><?= htmlspecialchars($item['nama']) ?></div>
              <div class="text-[12px] text-outline">SKU: <?= htmlspecialchars($item['sku']) ?></div>
            </td>
            <td class="py-md px-md"><?= kategoriBadge($item['kategori']) ?></td>
            <td class="py-md px-md text-center font-mono text-[14px]"><?= htmlspecialchars((string) $item['stok']) ?> <span class="text-outline text-[12px]"><?= htmlspecialchars($item['satuan']) ?></span></td>
            <td class="py-md px-md"><?= statusBadge($item['status']) ?></td>
            <td class="py-md px-lg text-right">
              <div class="flex justify-end gap-xs opacity-0 group-hover:opacity-100 transition-opacity">
                <?php if ($user['role'] !== 'perawat'): ?>
                <button type="button" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-transform hover:scale-110" title="Update Stok"
                  onclick='openEditModal(<?= (int) $item["id"] ?>, <?= json_encode($item["nama"]) ?>, <?= (int) $item["stok"] ?>, <?= (int) $item["ambang_minimum"] ?>, <?= json_encode($item["satuan"]) ?>, <?= json_encode($item["catatan"] ?? "") ?>)'>
                  <span class="material-symbols-outlined text-[20px]">edit_note</span>
                </button>
                <?php endif; ?>
                <button type="button" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg transition-transform hover:scale-110" title="Catat Penggunaan"
                  onclick='openUseModal(<?= (int) $item["id"] ?>, <?= json_encode($item["nama"]) ?>, <?= (int) $item["stok"] ?>, <?= json_encode($item["satuan"]) ?>)'>
                  <span class="material-symbols-outlined text-[20px]">output</span>
                </button>
                <?php if ($user['role'] !== 'perawat'): ?>
                <button type="button" class="p-2 text-error hover:bg-error/10 rounded-lg transition-transform hover:scale-110" title="Hapus"
                  onclick='openDeleteModal(<?= (int) $item["id"] ?>, <?= json_encode($item["nama"]) ?>)'>
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <!-- Tampilan kartu mobile dihapus sementara untuk ringkasnya, atau biarkan jika penting -->
  <?php endif; ?>
</div>

<!-- ============================================================= -->
<!-- Modal: Update Stok -->
<!-- ============================================================= -->
<div id="modal-edit" class="modal-hidden fixed inset-0 z-[65] flex items-center justify-center p-4">
  <div class="modal-overlay absolute inset-0 bg-black/40" onclick="closeModal('modal-edit')"></div>
  <form method="POST" action="<?= BASE_URL ?>/action" class="modal-panel relative bg-surface-container-lowest rounded-2xl shadow-card-hover w-full max-w-sm p-lg">
    <input type="hidden" name="action" value="update_item">
    <input type="hidden" name="id" id="edit-id">
    <div class="flex items-center justify-between mb-md">
      <h3 class="font-display font-bold text-[17px] text-on-surface">Update Stok Barang</h3>
      <button type="button" onclick="closeModal('modal-edit')" class="p-1 text-outline hover:text-error rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <p id="edit-nama" class="text-[14px] font-bold text-primary mb-md"></p>
    <div class="space-y-md">
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Jumlah Stok (<span id="edit-satuan"></span>)</label>
        <input type="number" name="stok" id="edit-stok" min="0" required class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
      </div>
    </div>
    <div class="flex gap-sm mt-lg">
      <button type="button" onclick="closeModal('modal-edit')" class="flex-1 py-2.5 rounded-xl border border-outline-variant font-bold text-[14px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</button>
      <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary text-white font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all">Simpan</button>
    </div>
  </form>
</div>

<!-- ============================================================= -->
<!-- Modal: Catat Penggunaan -->
<!-- ============================================================= -->
<div id="modal-use" class="modal-hidden fixed inset-0 z-[65] flex items-center justify-center p-4">
  <div class="modal-overlay absolute inset-0 bg-black/40" onclick="closeModal('modal-use')"></div>
  <form method="POST" action="<?= BASE_URL ?>/action" class="modal-panel relative bg-surface-container-lowest rounded-2xl shadow-card-hover w-full max-w-sm p-lg">
    <input type="hidden" name="action" value="use_item">
    <input type="hidden" name="id" id="use-id">
    <div class="flex items-center justify-between mb-md">
      <h3 class="font-display font-bold text-[17px] text-on-surface">Catat Penggunaan</h3>
      <button type="button" onclick="closeModal('modal-use')" class="p-1 text-outline hover:text-error rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <p id="use-nama" class="text-[14px] font-bold text-secondary mb-1"></p>
    <p class="text-[12px] text-outline mb-md">Stok saat ini: <span id="use-stok" class="font-bold"></span> <span id="use-satuan"></span></p>
    <div class="space-y-md">
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Jumlah Pemakaian</label>
        <input type="number" name="jumlah_pakai" id="use-jumlah" min="1" required class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-secondary/25 focus:border-secondary outline-none transition-all">
      </div>
    </div>
    <div class="flex gap-sm mt-lg">
      <button type="button" onclick="closeModal('modal-use')" class="flex-1 py-2.5 rounded-xl border border-outline-variant font-bold text-[14px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</button>
      <button type="submit" class="flex-1 py-2.5 rounded-xl bg-secondary text-white font-bold text-[14px] hover:bg-tertiary-container active:scale-[0.98] transition-all">Catat</button>
    </div>
  </form>
</div>

<script>
  function openEditModal(id, nama, stok, ambang, satuan, catatan) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').textContent = nama;
    document.getElementById('edit-stok').value = stok;
    document.getElementById('edit-satuan').textContent = satuan;
    const modal = document.getElementById('modal-edit');
    modal.classList.remove('modal-hidden');
  }
  function openUseModal(id, nama, stok, satuan) {
    document.getElementById('use-id').value = id;
    document.getElementById('use-nama').textContent = nama;
    document.getElementById('use-stok').textContent = stok;
    document.getElementById('use-satuan').textContent = satuan;
    document.getElementById('use-jumlah').max = stok;
    const modal = document.getElementById('modal-use');
    modal.classList.remove('modal-hidden');
  }
  function closeModal(id) {
    document.getElementById(id).classList.add('modal-hidden');
  }
  
  // Render Chart
  document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('chartStokTersedia');
    if(canvas) {
      const labels = JSON.parse(canvas.getAttribute('data-labels'));
      const values = JSON.parse(canvas.getAttribute('data-values'));
      
      new Chart(canvas, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Stok Tersedia',
            data: values,
            backgroundColor: 'rgba(15, 82, 56, 0.8)',
            borderRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    }
  });
</script>
