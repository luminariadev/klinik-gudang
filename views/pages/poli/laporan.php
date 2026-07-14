<?php
function statusBadgeLaporan(string $status): string {
    $isLow = $status === 'low';
    $color = $isLow ? 'error' : 'primary';
    $label = $isLow ? 'Stok Rendah' : 'Tersedia';
    return '<div class="inline-flex items-center gap-1.5 text-[13px] font-bold text-' . $color . '">'
         . '<span class="w-1.5 h-1.5 rounded-full bg-' . $color . ($isLow ? ' animate-pulse' : '') . '"></span>' . $label . '</div>';
}

function kategoriBadgeLaporan(string $kategori): string {
    $map = [
        'Obat' => 'bg-secondary-container text-on-secondary-container',
        'Alkes' => 'bg-tertiary-container/20 text-tertiary',
        'Habis Pakai' => 'bg-primary-fixed text-on-primary-fixed-variant',
    ];
    $cls = $map[$kategori] ?? 'bg-surface-variant text-on-surface-variant';
    return '<span class="px-2.5 py-1 ' . $cls . ' rounded-full text-[11px] font-bold uppercase tracking-wide whitespace-nowrap">' . htmlspecialchars($kategori) . '</span>';
}

function kategoriDot(string $kategori): string {
    $map = ['Obat' => 'bg-secondary', 'Alkes' => 'bg-tertiary', 'Habis Pakai' => 'bg-primary'];
    return $map[$kategori] ?? 'bg-outline';
}
?>

<div class="mb-lg flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Laporan Inventaris</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Rekap stok, kategori, dan status barang.</p>
  </div>
  <div class="flex gap-sm no-print">
    <button onclick="window.print()" class="flex items-center justify-center gap-sm border border-outline-variant px-lg py-3 rounded-xl font-bold text-[14px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">
      <span class="material-symbols-outlined text-[20px]">print</span>
      <span class="hidden sm:inline">Cetak Laporan</span>
    </button>
    <a href="<?= BASE_URL ?>/laporan/pdf<?= $kategoriFilter ? '?kategori=' . urlencode($kategoriFilter) . ($statusFilter ? '&status=' . urlencode($statusFilter) : '') : ($statusFilter ? '?status=' . urlencode($statusFilter) : '') ?>" class="flex items-center justify-center gap-sm bg-primary text-white px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all shadow-card">
      <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
      <span class="hidden sm:inline">Export PDF</span>
    </a>
    <a href="<?= BASE_URL ?>/laporan?export=csv<?= $kategoriFilter ? '&kategori=' . urlencode($kategoriFilter) : '' ?><?= $statusFilter ? '&status=' . urlencode($statusFilter) : '' ?>" class="flex items-center justify-center gap-sm bg-surface-variant text-on-surface-variant px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-outline-variant active:scale-[0.98] transition-all shadow-card">
      <span class="material-symbols-outlined text-[20px]">download</span>
      <span class="hidden sm:inline">Export CSV</span>
    </a>
  </div>
</div>

<!-- Ringkasan -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-gutter mb-lg">
  <div class="bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <div class="flex items-center justify-between mb-md">
      <span class="text-[11px] font-bold uppercase tracking-widest text-outline">Total Item</span>
      <span class="material-symbols-outlined text-primary bg-primary/10 p-1.5 rounded-lg text-[18px]">category</span>
    </div>
    <span class="text-[28px] font-display font-bold text-on-surface"><?= number_format($totalItem, 0, ',', '.') ?></span>
  </div>
  <div class="bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <div class="flex items-center justify-between mb-md">
      <span class="text-[11px] font-bold uppercase tracking-widest text-outline">Total Stok</span>
      <span class="material-symbols-outlined text-primary bg-primary/10 p-1.5 rounded-lg text-[18px]">inventory</span>
    </div>
    <span class="text-[28px] font-display font-bold text-on-surface"><?= number_format($totalStok, 0, ',', '.') ?></span>
  </div>
  <div class="bg-surface-container-lowest p-lg rounded-2xl border border-error/20 shadow-card">
    <div class="flex items-center justify-between mb-md">
      <span class="text-[11px] font-bold uppercase tracking-widest text-error">Stok Rendah</span>
      <span class="material-symbols-outlined text-error bg-error-container p-1.5 rounded-lg text-[18px]">warning</span>
    </div>
    <span class="text-[28px] font-display font-bold text-error"><?= number_format($stokRendah, 0, ',', '.') ?></span>
  </div>
  <div class="bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <div class="flex items-center justify-between mb-md">
      <span class="text-[11px] font-bold uppercase tracking-widest text-outline">Kategori</span>
      <span class="material-symbols-outlined text-primary bg-primary/10 p-1.5 rounded-lg text-[18px]">label</span>
    </div>
    <span class="text-[28px] font-display font-bold text-on-surface"><?= count($kategoriSummary) ?></span>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-lg">

  <!-- Breakdown per kategori -->
  <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg">
    <h3 class="font-display text-[16px] font-bold text-on-surface mb-md">Distribusi Stok per Kategori</h3>
    <?php if (empty($kategoriSummary)): ?>
      <p class="text-[13px] text-outline py-md">Belum ada data kategori.</p>
    <?php else: ?>
      <div class="space-y-md">
        <?php
          $maxStok = max(array_column($kategoriSummary, 'total_stok')) ?: 1;
          foreach ($kategoriSummary as $k):
            $persen = round(((int)$k['total_stok'] / $maxStok) * 100);
        ?>
        <div>
          <div class="flex justify-between items-center mb-1.5">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full <?= kategoriDot($k['kategori']) ?>"></span>
              <span class="text-[13px] font-bold text-on-surface"><?= htmlspecialchars($k['kategori']) ?></span>
              <span class="text-[12px] text-outline">(<?= (int)$k['jumlah_item'] ?> item)</span>
            </div>
            <span class="text-[13px] font-mono text-on-surface-variant"><?= number_format((int)$k['total_stok'], 0, ',', '.') ?></span>
          </div>
          <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden">
            <div class="h-full <?= kategoriDot($k['kategori']) ?> rounded-full" style="width: <?= $persen ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Panel stok rendah -->
  <div class="bg-surface-container-lowest rounded-2xl border border-error/20 shadow-card p-lg">
    <h3 class="font-display text-[16px] font-bold text-error mb-md flex items-center gap-2">
      <span class="material-symbols-outlined text-[20px]">warning</span>
      Perlu Perhatian
    </h3>
    <?php if (empty($lowStockItems)): ?>
      <p class="text-[13px] text-outline py-md">Tidak ada item dengan stok rendah saat ini.</p>
    <?php else: ?>
      <div class="space-y-sm">
        <?php foreach ($lowStockItems as $li): ?>
          <div class="flex justify-between items-center py-2 border-b border-outline-variant/30 last:border-0">
            <div class="min-w-0">
              <p class="text-[13px] font-bold text-on-surface truncate"><?= htmlspecialchars($li['nama']) ?></p>
              <p class="text-[11px] text-outline">SKU: <?= htmlspecialchars($li['sku']) ?></p>
            </div>
            <span class="text-[13px] font-mono font-bold text-error shrink-0 ml-2"><?= htmlspecialchars((string)$li['stok']) ?> <?= htmlspecialchars($li['satuan']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Filter -->
<form method="GET" action="<?= BASE_URL ?>/laporan" class="no-print bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg mb-lg">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-md items-end">
    <div>
      <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5" for="kategori">Kategori</label>
      <select id="kategori" name="kategori" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoriList as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>" <?= $kategoriFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5" for="status">Status</label>
      <select id="status" name="status" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none">
        <option value="">Semua Status</option>
        <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Tersedia</option>
        <option value="low" <?= $statusFilter === 'low' ? 'selected' : '' ?>>Stok Rendah</option>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5" for="dari">Dari Tanggal</label>
      <input type="date" id="dari" name="dari" value="<?= htmlspecialchars($dariTanggal) ?>" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none">
    </div>
    <div>
      <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5" for="sampai">Sampai Tanggal</label>
      <input type="date" id="sampai" name="sampai" value="<?= htmlspecialchars($sampaiTanggal) ?>" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none">
    </div>
    <div class="flex gap-sm">
      <button type="submit" class="flex-1 flex items-center justify-center gap-xs bg-primary text-white px-md py-2.5 rounded-xl font-bold text-[13px] hover:bg-primary-container transition-all">
        <span class="material-symbols-outlined text-[18px]">filter_alt</span>Terapkan
      </button>
      <a href="<?= BASE_URL ?>/laporan" class="flex items-center justify-center px-md py-2.5 border border-outline-variant rounded-xl font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">Reset</a>
    </div>
  </div>
</form>

<!-- Tabel laporan -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="p-lg border-b border-outline-variant/60 flex flex-col sm:flex-row sm:items-center justify-between gap-sm">
    <h3 class="font-display text-[17px] font-bold text-on-surface">Detail Inventaris</h3>
    <p class="text-[12px] text-outline"><?= count($items) ?> item ditemukan</p>
  </div>

  <?php if (empty($items)): ?>
    <div class="py-16 px-lg text-center">
      <span class="material-symbols-outlined text-[40px] text-outline-variant mb-2">assessment</span>
      <p class="text-on-surface-variant font-medium">Tidak ada data untuk filter yang dipilih.</p>
      <p class="text-[13px] text-outline mt-1">Coba ubah kategori, status, atau rentang tanggal.</p>
    </div>
  <?php else: ?>

    <div class="desktop-table overflow-x-auto hidden md:block">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container text-outline text-[11px] border-b border-outline-variant/60">
            <th class="py-md px-lg font-bold uppercase tracking-wider">Nama Barang</th>
            <th class="py-md px-md font-bold uppercase tracking-wider">Kategori</th>
            <th class="py-md px-md font-bold uppercase tracking-wider text-center">Stok</th>
            <th class="py-md px-md font-bold uppercase tracking-wider">Status</th>
            <th class="py-md px-lg font-bold uppercase tracking-wider text-right">Tanggal Dicatat</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/30">
          <?php foreach ($items as $item): ?>
          <tr class="hover:bg-surface-container-low transition-colors">
            <td class="py-md px-lg">
              <div class="font-bold text-on-surface text-[14px]"><?= htmlspecialchars($item['nama']) ?></div>
              <div class="text-[12px] text-outline">SKU: <?= htmlspecialchars($item['sku']) ?></div>
            </td>
            <td class="py-md px-md"><?= kategoriBadgeLaporan($item['kategori']) ?></td>
            <td class="py-md px-md text-center font-mono text-[14px]"><?= htmlspecialchars((string)$item['stok']) ?> <span class="text-outline text-[12px]"><?= htmlspecialchars($item['satuan']) ?></span></td>
            <td class="py-md px-md"><?= statusBadgeLaporan($item['status']) ?></td>
            <td class="py-md px-lg text-right text-[13px] text-outline">
              <?= !empty($item['created_at']) ? htmlspecialchars(date('d M Y', strtotime($item['created_at']))) : '—' ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mobile-cards block md:hidden divide-y divide-outline-variant/40">
      <?php foreach ($items as $item): ?>
      <div class="p-lg flex flex-col gap-sm">
        <div class="flex justify-between items-start gap-md">
          <div class="min-w-0">
            <div class="font-bold text-on-surface text-[15px] truncate"><?= htmlspecialchars($item['nama']) ?></div>
            <div class="text-[12px] text-outline">SKU: <?= htmlspecialchars($item['sku']) ?></div>
          </div>
          <?= kategoriBadgeLaporan($item['kategori']) ?>
        </div>
        <div class="flex justify-between items-center mt-1">
          <div class="font-mono text-[14px] text-on-surface">
            <?= htmlspecialchars((string)$item['stok']) ?> <span class="text-outline text-[12px]"><?= htmlspecialchars($item['satuan']) ?></span>
          </div>
          <?= statusBadgeLaporan($item['status']) ?>
        </div>
        <div class="text-[12px] text-outline mt-1">
          Dicatat: <?= !empty($item['created_at']) ? htmlspecialchars(date('d M Y', strtotime($item['created_at']))) : '—' ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
