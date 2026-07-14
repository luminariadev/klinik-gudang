<div class="mb-lg">
  <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-2">Kelola Poli</h2>
  <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Tambah atau hapus master data Poli.</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="mb-md text-[13px] text-primary bg-primary/10 rounded-lg px-3 py-2.5 flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">check_circle</span><?= htmlspecialchars($_GET['msg'] ?? 'Berhasil.') ?>
  </div>
<?php endif; ?>

<div class="grid lg:grid-cols-5 gap-lg items-start">
  <!-- Form Tambah Poli -->
  <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg animate-card">
    <form method="POST" action="<?= BASE_URL ?>/super-admin/polis/store" class="space-y-md">
      <div>
        <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Nama Poli <span class="text-error">*</span></label>
        <input type="text" name="nama" required
          class="w-full px-3.5 py-2.5 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px]"
          placeholder="Contoh: Poli KIA">
      </div>
      <div>
        <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Deskripsi</label>
        <textarea name="deskripsi" rows="3"
          class="w-full px-3.5 py-2.5 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px] resize-none"
          placeholder="Opsional"></textarea>
      </div>
      <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl font-bold text-[14px] hover:bg-primary-container transition-colors shadow-card flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add_circle</span>Tambah Poli
      </button>
    </form>
  </div>

  <!-- Daftar Poli -->
  <div class="lg:col-span-3 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden animate-card" style="animation-delay:.08s">
    <div class="p-lg border-b border-outline-variant/60 flex items-center justify-between">
      <h3 class="font-display text-[16px] font-bold text-on-surface">Daftar Poli Terdaftar</h3>
      <span class="text-[12px] text-outline"><?= count($polis) ?> poli</span>
    </div>

    <?php if (empty($polis)): ?>
      <div class="py-14 px-lg text-center">
        <span class="material-symbols-outlined text-[36px] text-outline-variant mb-2">domain_disabled</span>
        <p class="text-on-surface-variant text-[13px] font-medium">Belum ada poli terdaftar.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container text-outline text-[11px] border-b border-outline-variant/60">
              <th class="py-md px-lg font-bold uppercase tracking-wider">Nama Poli</th>
              <th class="py-md px-md font-bold uppercase tracking-wider">Deskripsi</th>
              <th class="py-md px-lg font-bold uppercase tracking-wider text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <?php foreach ($polis as $i => $poli): ?>
            <tr class="hover:bg-primary/[.03] transition-colors">
              <td class="py-md px-lg font-bold text-[14px] text-on-surface">
                <?= htmlspecialchars($poli['nama']) ?>
              </td>
              <td class="py-md px-md text-[13px] text-on-surface-variant truncate max-w-[200px]">
                <?= htmlspecialchars($poli['deskripsi'] ?: '—') ?>
              </td>
              <td class="py-md px-lg text-right">
                <form method="POST" action="<?= BASE_URL ?>/super-admin/polis/delete" onsubmit="return confirm('Hapus poli <?= htmlspecialchars(addslashes($poli['nama'])) ?>? Tindakan ini tidak dapat dibatalkan.');" class="inline-block">
                  <input type="hidden" name="id" value="<?= (int) $poli['id'] ?>">
                  <button type="submit" title="Hapus Poli" class="p-2 text-outline hover:text-error hover:bg-error-container/40 rounded-lg transition-colors inline-flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
