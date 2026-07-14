<div class="mb-lg flex flex-col sm:flex-row sm:items-end sm:justify-between gap-md">
  <div>
    <h2 class="section-title-bar font-display text-[22px] sm:text-[26px] font-bold text-primary mb-2">Ringkasan Seluruh Poli</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px] mt-2">Pantau inventaris semua Poli dalam satu tampilan terpusat.</p>
  </div>
  <div class="flex items-center gap-md self-start sm:self-auto">
    <a href="<?= BASE_URL ?>/super-admin/users" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2.5 rounded-xl text-[13px] font-bold shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all">
      <span class="material-symbols-outlined text-[18px]">person_add</span>Kelola Pengguna
    </a>
    <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/60 rounded-full px-4 py-2 shadow-card">
      <div class="w-2 h-2 rounded-full bg-primary live-dot"></div>
      <span class="text-[12px] font-bold text-primary">Data real-time</span>
    </div>
  </div>
</div>

<!-- Stat cards gabungan -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-md mb-8">
  <div class="stat-card animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card" style="animation-delay:.02s">
    <div class="flex items-center justify-between mb-md relative z-10">
      <span class="icon-badge material-symbols-outlined text-white p-2.5 rounded-xl text-[20px] shadow-sm" style="background:linear-gradient(135deg,#0f5238,#2d6a4f); animation-delay:.15s">inventory_2</span>
      <span class="text-[10px] font-bold uppercase tracking-wider text-outline bg-surface-container px-2 py-1 rounded-full">Gabungan</span>
    </div>
    <div class="text-[28px] font-display font-bold text-on-surface count-up relative z-10"><?= number_format($stats['total_items'], 0, ',', '.') ?></div>
    <div class="text-[12px] text-outline font-medium mt-1 relative z-10">Total Item Terdaftar</div>
  </div>
  <div class="stat-card animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card" style="animation-delay:.08s">
    <div class="flex items-center justify-between mb-md relative z-10">
      <span class="icon-badge material-symbols-outlined text-white p-2.5 rounded-xl text-[20px] shadow-sm" style="background:linear-gradient(135deg,#0f5238,#2d6a4f); animation-delay:.3s">domain</span>
      <span class="text-[10px] font-bold uppercase tracking-wider text-outline bg-surface-container px-2 py-1 rounded-full">Aktif</span>
    </div>
    <div class="text-[28px] font-display font-bold text-on-surface count-up relative z-10"><?= number_format($stats['total_polis'], 0, ',', '.') ?></div>
    <div class="text-[12px] text-outline font-medium mt-1 relative z-10">Poli Terdaftar</div>
  </div>
  <div class="stat-card animate-card bg-surface-container-lowest p-lg rounded-2xl border <?= $stats['low_stock'] > 0 ? 'border-error/30' : 'border-outline-variant/60' ?> shadow-card" style="animation-delay:.14s">
    <div class="flex items-center justify-between mb-md relative z-10">
      <span class="icon-badge material-symbols-outlined text-white p-2.5 rounded-xl text-[20px] shadow-sm <?= $stats['low_stock'] > 0 ? '' : 'opacity-80' ?>" style="background:linear-gradient(135deg,#ba1a1a,#e6534d); animation-delay:.25s">warning</span>
      <?php if ($stats['low_stock'] > 0): ?>
        <span class="text-[10px] font-bold uppercase tracking-wider text-error bg-error-container/60 px-2 py-1 rounded-full animate-pulse">Perlu perhatian</span>
      <?php else: ?>
        <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 px-2 py-1 rounded-full">Aman</span>
      <?php endif; ?>
    </div>
    <div class="text-[28px] font-display font-bold <?= $stats['low_stock'] > 0 ? 'text-error' : 'text-on-surface' ?> count-up relative z-10"><?= number_format($stats['low_stock'], 0, ',', '.') ?></div>
    <div class="text-[12px] text-outline font-medium mt-1 relative z-10">Barang Stok Rendah</div>
  </div>
  <div class="stat-card animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card" style="animation-delay:.20s">
    <div class="flex items-center justify-between mb-md relative z-10">
      <span class="icon-badge material-symbols-outlined text-white p-2.5 rounded-xl text-[20px] shadow-sm" style="background:linear-gradient(135deg,#0f5238,#2d6a4f); animation-delay:.3s">group</span>
      <span class="text-[10px] font-bold uppercase tracking-wider text-outline bg-surface-container px-2 py-1 rounded-full">Pengguna</span>
    </div>
    <div class="text-[28px] font-display font-bold text-on-surface count-up relative z-10"><?= number_format($stats['total_users'], 0, ',', '.') ?></div>
    <div class="text-[12px] text-outline font-medium mt-1 relative z-10">Total Admin & Perawat</div>
  </div>
</div>

<!-- Kartu ringkasan per poli -->
<div class="mb-md">
  <span class="text-[11px] font-bold uppercase tracking-widest text-outline">Distribusi per Poli</span>
</div>
<div class="grid md:grid-cols-2 gap-md mb-8">
  <?php foreach ($polis as $index => $poli): 
      $accent = ($index % 2 === 0) ? '#0f5238' : '#3e6750';
  ?>
  <div class="poli-card group animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card hover:shadow-card-hover transition-all hover:-translate-y-1 flex items-center gap-lg" style="animation-delay:<?= 0.26 + ($index * 0.05) ?>s">
    <div class="relative w-16 h-16 shrink-0">
      <svg viewBox="0 0 60 60" class="w-16 h-16 -rotate-90">
        <circle cx="30" cy="30" r="26" fill="none" stroke="#e1e3e2" stroke-width="6"></circle>
        <circle cx="30" cy="30" r="26" fill="none" stroke="<?= $accent ?>" stroke-width="6" stroke-linecap="round"
                stroke-dasharray="163.36" stroke-dashoffset="0"
                class="ring-progress"></circle>
      </svg>
      <span class="poli-icon material-symbols-outlined absolute inset-0 flex items-center justify-center text-primary text-[24px]">local_hospital</span>
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-[16px] text-on-surface"><?= htmlspecialchars($poli['nama']) ?></h3>
      </div>
      <div class="flex flex-wrap gap-x-lg gap-y-1 mt-2 text-[12px]">
        <span class="text-on-surface-variant"><b class="text-on-surface"><?= number_format($poli['item_count'], 0, ',', '.') ?></b> jenis item</span>
        <span class="text-on-surface-variant"><b class="text-on-surface"><?= number_format($poli['total_stok'], 0, ',', '.') ?></b> unit stok</span>
      </div>
      <div class="w-full h-1.5 rounded-full bg-surface-variant mt-3 overflow-hidden">
        <div class="h-full rounded-full transition-all duration-1000 ease-out" style="width:100%; background:<?= $accent ?>;"></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  
  <?php if (empty($polis)): ?>
    <div class="md:col-span-2 py-10 px-lg text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/60 border-dashed">
      <span class="material-symbols-outlined text-[32px] text-outline-variant mb-1">domain_disabled</span>
      <p class="text-[13px] text-outline">Belum ada poli terdaftar. <a href="<?= BASE_URL ?>/super-admin/polis" class="text-primary font-bold hover:underline">Kelola Poli</a></p>
    </div>
  <?php endif; ?>
</div>
