<div class="mb-lg flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Input Barang Baru (Global)</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Tambahkan barang ke poli mana saja.</p>
  </div>
  <a href="<?= BASE_URL ?>/super-admin/barang" class="flex items-center justify-center gap-sm border border-outline-variant text-on-surface-variant px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-surface-variant/40 transition-all w-full sm:w-auto">
    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
    <span>Kembali ke Master Barang</span>
  </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg items-start">
  <div class="lg:col-span-2 animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <?php if (!empty($errors['general'])): ?>
      <div class="mb-md flex items-center gap-sm bg-error-container text-on-error-container px-md py-3 rounded-xl text-[13px] font-bold">
        <span class="material-symbols-outlined text-[20px]">error</span><?= htmlspecialchars($errors['general']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/super-admin/barang/store" novalidate>
      <div class="mb-lg">
        <h3 class="font-display text-[15px] font-bold text-on-surface mb-1">Informasi Barang</h3>
        <p class="text-[12px] text-outline">Field bertanda <span class="text-error">*</span> wajib diisi.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-md mb-md">
        
        <!-- Dropdown Pilih Poli -->
        <div class="sm:col-span-2">
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Pilih Poli <span class="text-error">*</span></label>
          <select name="poli_id" class="w-full px-md py-2.5 bg-surface border <?= isset($errors['poli_id']) ? 'border-error field-error' : 'border-outline-variant' ?> rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
            <option value="" disabled <?= $old['poli_id'] === '' ? 'selected' : '' ?>>-- Pilih Poli --</option>
            <?php foreach ($daftarPoli as $poli): ?>
              <option value="<?= $poli['id'] ?>" <?= $old['poli_id'] == $poli['id'] ? 'selected' : '' ?>><?= htmlspecialchars($poli['nama']) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['poli_id'])): ?><p class="text-[12px] text-error mt-1"><?= htmlspecialchars($errors['poli_id']) ?></p><?php endif; ?>
        </div>

        <div class="sm:col-span-2 border-t border-outline-variant/40 my-2"></div>

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
        </div>

        <div class="sm:col-span-2">
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Catatan (opsional)</label>
          <textarea name="catatan" rows="2" placeholder="Contoh: disimpan di lemari pendingin, dll."
            class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all resize-none"><?= htmlspecialchars($old['catatan']) ?></textarea>
        </div>
      </div>

      <div class="flex gap-sm mt-lg">
        <a href="<?= BASE_URL ?>/super-admin/barang" class="flex-1 sm:flex-none text-center py-2.5 px-lg rounded-xl border border-outline-variant font-bold text-[14px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</a>
        <button type="submit" class="flex-1 flex items-center justify-center gap-sm py-2.5 px-lg rounded-xl bg-primary text-white font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all">
          <span class="material-symbols-outlined text-[20px]">save</span>Simpan Barang
        </button>
      </div>
    </form>
  </div>
</div>
