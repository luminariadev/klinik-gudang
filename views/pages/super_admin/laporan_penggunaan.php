<div class="mb-lg">
  <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Laporan Penggunaan (Global)</h2>
  <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Pantau seluruh histori pemakaian barang dari semua poli.</p>
</div>

<!-- Filter Box -->
<form method="GET" action="<?= BASE_URL ?>/super-admin/laporan-penggunaan" class="no-print bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg mb-lg">
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-md items-end">
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Kategori Barang</label>
      <select name="kategori" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoris as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>" <?= $kategoriFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Poli</label>
      <select name="poli" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Poli</option>
        <?php foreach ($polis as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $poliFilter == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Pengguna</label>
      <select name="perawat" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none transition-all">
        <option value="">Semua Pengguna</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= $perawatFilter == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars(str_replace('_', ' ', $u['role'])) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="flex gap-sm">
      <button type="submit" class="flex-1 flex items-center justify-center gap-sm bg-primary text-white px-md py-2.5 rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all">
        <span class="material-symbols-outlined text-[18px]">filter_alt</span> Terapkan
      </button>
      <a href="<?= BASE_URL ?>/super-admin/laporan-penggunaan" class="flex items-center justify-center px-md py-2.5 border border-outline-variant rounded-xl font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">Reset</a>
    </div>
  </div>
</form>

<div class="animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
      <thead>
        <tr class="bg-surface-bright/50 border-b border-outline-variant/60">
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Tanggal & Waktu</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Poli</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Pengguna</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Barang (SKU)</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Jumlah Keluar</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/30">
        <?php if(empty($logs)): ?>
          <tr><td colspan="5" class="text-center py-xl text-outline-variant">Tidak ada data penggunaan yang ditemukan.</td></tr>
        <?php else: ?>
          <?php foreach ($logs as $log): ?>
            <tr class="hover:bg-surface-variant/20 transition-colors">
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface"><?= date('d/m/Y', strtotime($log['created_at'])) ?></p>
                <p class="text-[12px] text-outline"><?= date('H:i', strtotime($log['created_at'])) ?> WIB</p>
              </td>
              <td class="py-3 px-md">
                <span class="px-2.5 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[11px] font-bold whitespace-nowrap">
                  <?= htmlspecialchars($log['poli_nama'] ?? 'Tanpa Poli') ?>
                </span>
              </td>
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface"><?= htmlspecialchars($log['user_nama']) ?></p>
                <p class="text-[11px] text-outline uppercase tracking-wide"><?= htmlspecialchars(str_replace('_', ' ', $log['role'])) ?></p>
              </td>
              <td class="py-3 px-md">
                <p class="text-[14px] font-bold text-on-surface"><?= htmlspecialchars($log['item_nama']) ?></p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[12px] text-outline font-mono"><?= htmlspecialchars($log['sku']) ?></span>
                  <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                  <span class="text-[11px] font-bold text-primary"><?= htmlspecialchars($log['kategori']) ?></span>
                </div>
              </td>
              <td class="py-3 px-md">
                <span class="inline-flex items-center gap-1.5 text-[14px] font-bold text-error bg-error-container/30 px-3 py-1 rounded-full border border-error/20">
                  <span class="material-symbols-outlined text-[16px]">remove</span>
                  <?= (int)$log['jumlah'] ?> <?= htmlspecialchars($log['satuan']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
