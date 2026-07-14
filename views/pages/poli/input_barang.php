<div class="mb-lg flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Input Barang Baru</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Tambahkan obat, alat kesehatan, atau barang habis pakai ke inventaris.</p>
  </div>
  <a href="<?= BASE_URL ?>/dashboard" class="flex items-center justify-center gap-sm border border-outline-variant text-on-surface-variant px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-surface-variant/40 transition-all w-full sm:w-auto">
    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
    <span>Kembali ke Dashboard</span>
  </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg items-start">

  <!-- Form Tambah Barang -->
  <div class="lg:col-span-2 animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <?php if (!empty($errors['general'])): ?>
      <div class="mb-md flex items-center gap-sm bg-error-container text-on-error-container px-md py-3 rounded-xl text-[13px] font-bold">
        <span class="material-symbols-outlined text-[20px]">error</span><?= htmlspecialchars($errors['general']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/items/store" novalidate>
      <div class="mb-lg">
        <h3 class="font-display text-[15px] font-bold text-on-surface mb-1">Informasi Barang</h3>
        <p class="text-[12px] text-outline">Field bertanda <span class="text-error">*</span> wajib diisi.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-md mb-md">
        <div class="sm:col-span-2">
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Nama Barang <span class="text-error">*</span></label>
          <input type="text" name="nama" value="<?= htmlspecialchars($old['nama']) ?>" placeholder="Contoh: Paracetamol 500mg"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['nama']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
          <?php if (isset($errors['nama'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['nama']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">SKU <span class="text-error">*</span></label>
          <input type="text" name="sku" value="<?= htmlspecialchars($old['sku']) ?>" placeholder="Contoh: OB-0142"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['sku']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all font-mono">
          <?php if (isset($errors['sku'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['sku']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Kategori <span class="text-error">*</span></label>
          <select name="kategori" id="input-kategori"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['kategori']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
            <option value="" disabled <?= $old['kategori'] === '' ? 'selected' : '' ?>>Pilih kategori…</option>
            <?php foreach ($daftarKategori as $kat): ?>
              <option value="<?= htmlspecialchars($kat) ?>" <?= $old['kategori'] === $kat ? 'selected' : '' ?>><?= htmlspecialchars($kat) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['kategori'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['kategori']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Stok Awal <span class="text-error">*</span></label>
          <input type="number" name="stok" id="input-stok" min="0" value="<?= htmlspecialchars($old['stok']) ?>" placeholder="0"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['stok']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
          <?php if (isset($errors['stok'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['stok']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Satuan <span class="text-error">*</span></label>
          <input type="text" name="satuan" list="satuan-options" value="<?= htmlspecialchars($old['satuan']) ?>" placeholder="Contoh: strip, box, pcs"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['satuan']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
          <datalist id="satuan-options">
            <?php foreach ($daftarSatuan as $s): ?><option value="<?= htmlspecialchars($s) ?>"><?php endforeach; ?>
          </datalist>
          <?php if (isset($errors['satuan'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['satuan']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Ambang Batas Minimum <span class="text-error">*</span></label>
          <input type="number" name="ambang_minimum" id="input-ambang" min="0" value="<?= htmlspecialchars($old['ambang_minimum']) ?>" placeholder="15"
            class="w-full px-md py-2.5 bg-surface border <?= isset($errors['ambang_minimum']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
          <?php if (isset($errors['ambang_minimum'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['ambang_minimum']) ?></p><?php endif; ?>
          <p class="text-[11px] text-outline mt-1">Sistem akan menandai barang "Stok Rendah" jika stok ≤ nilai ini.</p>
        </div>

        <div class="sm:col-span-2 flex items-center gap-sm bg-surface-container-low rounded-xl px-md py-3">
          <span class="text-[12px] font-bold text-outline uppercase tracking-wide">Status Awal:</span>
          <span id="status-preview-dot" class="w-1.5 h-1.5 rounded-full bg-primary status-preview-dot"></span>
          <span id="status-preview-text" class="text-[13px] font-bold text-primary">Tersedia</span>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Catatan (opsional)</label>
          <textarea name="catatan" rows="3" placeholder="Contoh: disimpan di lemari pendingin, batch khusus, dll."
            class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all resize-none"><?= htmlspecialchars($old['catatan']) ?></textarea>
        </div>
      </div>

      <div class="flex gap-sm mt-lg">
        <a href="<?= BASE_URL ?>/dashboard" class="flex-1 sm:flex-none text-center py-2.5 px-lg rounded-xl border border-outline-variant font-bold text-[14px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</a>
        <button type="submit" class="flex-1 flex items-center justify-center gap-sm py-2.5 px-lg rounded-xl bg-primary text-white font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all">
          <span class="material-symbols-outlined text-[20px]">save</span>Simpan Barang
        </button>
      </div>
    </form>
  </div>

  <!-- Panel: Barang yang baru ditambahkan -->
  <div class="animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden" style="animation-delay:.06s">
    <div class="p-lg border-b border-outline-variant/60">
      <h3 class="font-display text-[15px] font-bold text-on-surface">Baru Ditambahkan</h3>
      <p class="text-[12px] text-outline mt-0.5">5 barang terakhir masuk ke inventaris</p>
    </div>
    <?php if (empty($recentItems)): ?>
      <div class="p-lg text-center">
        <span class="material-symbols-outlined text-[32px] text-outline-variant mb-1">inventory_2</span>
        <p class="text-[13px] text-outline">Belum ada barang ditambahkan.</p>
      </div>
    <?php else: ?>
      <div class="divide-y divide-outline-variant/30">
        <?php foreach ($recentItems as $i => $ri): ?>
        <div class="p-md flex items-center gap-sm animate-row" style="animation-delay:<?= $i * 0.05 ?>s">
          <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg text-[18px] shrink-0"><?= $ri['kategori'] === 'Obat' ? 'medication' : ($ri['kategori'] === 'Alkes' ? 'medical_services' : 'inventory_2') ?></span>
          <div class="min-w-0 flex-1">
            <p class="text-[13px] font-bold text-on-surface truncate"><?= htmlspecialchars($ri['nama']) ?></p>
            <div class="flex items-center gap-xs mt-0.5">
              <span class="px-2.5 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[11px] font-bold uppercase tracking-wide whitespace-nowrap"><?= htmlspecialchars($ri['kategori']) ?></span>
              <span class="text-[11px] text-outline"><?= (int) $ri['stok'] ?> <?= htmlspecialchars($ri['satuan']) ?></span>
            </div>
          </div>
          <?php if ($ri['status'] === 'low'): ?>
            <span class="w-1.5 h-1.5 rounded-full bg-error shrink-0" title="Stok Rendah"></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
  // === Preview status (Tersedia / Rendah) berdasarkan stok vs ambang ===
  const stokInput   = document.getElementById('input-stok');
  const ambangInput = document.getElementById('input-ambang');
  const dot  = document.getElementById('status-preview-dot');
  const text = document.getElementById('status-preview-text');

  function updateStatusPreview() {
    const stok   = parseInt(stokInput?.value || '0', 10) || 0;
    const ambang = parseInt(ambangInput?.value || '0', 10) || 0;
    const isLow  = stok <= ambang;
    dot.classList.toggle('bg-primary', !isLow);
    dot.classList.toggle('bg-error', isLow);
    text.classList.toggle('text-primary', !isLow);
    text.classList.toggle('text-error', isLow);
    text.textContent = isLow ? 'Stok Rendah' : 'Tersedia';
  }
  if (stokInput && ambangInput) {
      [stokInput, ambangInput].forEach(el => el.addEventListener('input', updateStatusPreview));
      updateStatusPreview();
  }

  // === Toast notifikasi ===
  function showToast(status, message) {
    if (!message) return;
    const container = document.getElementById('toast-container');
    const isError = status === 'error';
    const el = document.createElement('div');
    el.className = `toast-enter flex items-center gap-sm p-md rounded-xl shadow-card-hover text-[13px] font-bold text-white ${isError ? 'bg-error' : 'bg-primary'}`;
    el.innerHTML = `<span class="material-symbols-outlined text-[20px]">${isError ? 'error' : 'check_circle'}</span><span class="flex-1">${message}</span>`;
    container.appendChild(el);
    setTimeout(() => {
      el.classList.remove('toast-enter');
      el.classList.add('toast-leave');
      setTimeout(() => el.remove(), 250);
    }, 3500);
  }

  window.addEventListener('DOMContentLoaded', () => {
    <?php if ($flashMsg): ?>
    showToast(<?= json_encode($flashStatus) ?>, <?= json_encode($flashMsg) ?>);
    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
    <?php endif; ?>
  });
</script>
