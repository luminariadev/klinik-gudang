<div class="mb-lg flex flex-col sm:flex-row justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Data Barang</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Lihat daftar ketersediaan logistik di poli Anda.</p>
  </div>
</div>

<form method="GET" action="<?= BASE_URL ?>/perawat/barang" class="no-print bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg mb-lg">
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-md items-end">
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Kategori</label>
      <select name="kategori" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoris as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>" <?= $kategoriFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="sm:col-span-2">
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Pencarian</label>
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nama/SKU" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
    </div>
    <div class="flex gap-sm">
      <button type="submit" class="flex-1 flex items-center justify-center gap-1 bg-primary text-white py-2 rounded-xl font-bold text-[13px] hover:bg-primary-container transition-all">
        <span class="material-symbols-outlined text-[16px]">search</span> Cari
      </button>
      <a href="<?= BASE_URL ?>/perawat/barang" class="flex items-center justify-center px-3 py-2 border border-outline-variant rounded-xl font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">Reset</a>
    </div>
  </div>
</form>

<div class="animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
      <thead>
        <tr class="bg-surface-bright/50 border-b border-outline-variant/60">
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Nama & SKU</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Kategori</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Stok & Batas</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/30">
        <?php if(empty($items)): ?>
          <tr><td colspan="4" class="text-center py-xl text-outline-variant">Tidak ada barang yang ditemukan.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $item): ?>
            <tr class="hover:bg-surface-variant/20 transition-colors">
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface"><?= htmlspecialchars($item['nama']) ?></p>
                <p class="text-[12px] text-outline font-mono"><?= htmlspecialchars($item['sku']) ?></p>
              </td>
              <td class="py-3 px-md">
                <span class="px-2.5 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[11px] font-bold uppercase tracking-wide">
                  <?= htmlspecialchars($item['kategori']) ?>
                </span>
              </td>
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface">
                  <?= (int)$item['stok'] ?> <span class="text-[12px] text-outline font-normal"><?= htmlspecialchars($item['satuan']) ?></span>
                </p>
                <p class="text-[11px] text-outline">Batas Min: <?= (int)$item['ambang_minimum'] ?></p>
              </td>
              <td class="py-3 px-md">
                <?php if ($item['status'] === 'low'): ?>
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-error bg-error-container/80 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-error"></span>Stok Rendah</span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-primary bg-primary-container/30 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Tersedia</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
