<div class="mb-lg flex flex-col sm:flex-row justify-between sm:items-end gap-md no-print">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Laporan & Export Inventaris (Global)</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Pantau dan unduh data seluruh stok barang dari semua poli.</p>
  </div>
  <div class="flex gap-sm">
    <a href="<?= BASE_URL ?>/super-admin/laporan-inventaris?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" class="flex items-center justify-center gap-sm bg-surface-container-lowest border border-outline-variant text-on-surface-variant px-md py-2.5 rounded-xl font-bold text-[13px] hover:bg-surface-variant/40 transition-all shadow-sm">
      <span class="material-symbols-outlined text-[18px]">table_chart</span>CSV
    </a>
    <a href="<?= BASE_URL ?>/super-admin/laporan-inventaris?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>" target="_blank" class="flex items-center justify-center gap-sm bg-error text-white px-md py-2.5 rounded-xl font-bold text-[13px] hover:bg-error/90 transition-all shadow-sm">
      <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>PDF
    </a>
  </div>
</div>

<form method="GET" action="<?= BASE_URL ?>/super-admin/laporan-inventaris" class="no-print bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg mb-lg">
  <div class="grid grid-cols-1 sm:grid-cols-5 gap-md items-end">
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Poli</label>
      <select name="poli" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Poli</option>
        <?php foreach ($polis as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $poliFilter == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Kategori</label>
      <select name="kategori" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoris as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>" <?= $kategoriFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Status Stok</label>
      <select name="status" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Status</option>
        <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Tersedia</option>
        <option value="low" <?= $statusFilter === 'low' ? 'selected' : '' ?>>Stok Rendah</option>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Pencarian</label>
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nama/SKU" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
    </div>
    <div class="flex gap-sm">
      <button type="submit" class="flex-1 flex items-center justify-center gap-1 bg-primary text-white py-2 rounded-xl font-bold text-[13px] hover:bg-primary-container transition-all">
        <span class="material-symbols-outlined text-[16px]">filter_alt</span> Terapkan
      </button>
      <a href="<?= BASE_URL ?>/super-admin/laporan-inventaris" class="flex-1 flex items-center justify-center py-2 border border-outline-variant rounded-xl font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">Reset</a>
    </div>
  </div>
</form>

<div class="animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="p-lg border-b border-outline-variant/60 flex justify-between items-center bg-surface-bright/50">
    <h3 class="font-display text-[16px] font-bold text-on-surface">Data Inventaris Global</h3>
    <span class="px-3 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[12px] font-bold"><?= count($items) ?> Barang</span>
  </div>
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
      <thead>
        <tr class="bg-surface-bright/50 border-b border-outline-variant/60">
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Barang & SKU</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Poli</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Kategori</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Stok</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Status</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Catatan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/30">
        <?php if(empty($items)): ?>
          <tr><td colspan="6" class="text-center py-xl text-outline-variant">Tidak ada barang yang cocok dengan filter.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $item): ?>
            <tr class="hover:bg-surface-variant/20 transition-colors">
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface"><?= htmlspecialchars($item['nama']) ?></p>
                <p class="text-[12px] text-outline font-mono"><?= htmlspecialchars($item['sku']) ?></p>
              </td>
              <td class="py-3 px-md">
                <span class="px-2.5 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[11px] font-bold whitespace-nowrap">
                  <?= htmlspecialchars($item['poli_nama'] ?? 'Tanpa Poli') ?>
                </span>
              </td>
              <td class="py-3 px-md">
                <span class="px-2.5 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[11px] font-bold uppercase tracking-wide whitespace-nowrap">
                  <?= htmlspecialchars($item['kategori']) ?>
                </span>
              </td>
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold <?= $item['stok'] <= $item['ambang_minimum'] ? 'text-error' : 'text-on-surface' ?>">
                  <?= (int)$item['stok'] ?> <span class="text-[12px] text-outline font-normal"><?= htmlspecialchars($item['satuan']) ?></span>
                </p>
              </td>
              <td class="py-3 px-md">
                <?php if ($item['status'] === 'low'): ?>
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-error bg-error-container/80 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-error"></span>Stok Rendah</span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-primary bg-primary-container/30 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Tersedia</span>
                <?php endif; ?>
              </td>
              <td class="py-3 px-md">
                <p class="text-[12px] text-outline italic line-clamp-2"><?= htmlspecialchars($item['catatan'] ?? '—') ?></p>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
