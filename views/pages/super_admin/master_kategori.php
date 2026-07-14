<div class="mb-lg">
  <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Master Kategori</h2>
  <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Kelola kategori barang untuk seluruh poli.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg items-start">
  <!-- Form Tambah Kategori -->
  <div class="lg:col-span-1 animate-card bg-surface-container-lowest p-lg rounded-2xl border border-outline-variant/60 shadow-card">
    <h3 class="font-display text-[16px] font-bold text-on-surface mb-md">Tambah Kategori Baru</h3>
    
    <?php if (!empty($flashError)): ?>
      <div class="mb-md flex items-center gap-sm bg-error-container text-on-error-container px-md py-3 rounded-xl text-[13px] font-bold">
        <span class="material-symbols-outlined text-[20px]">error</span>
        <span><?= htmlspecialchars($flashError) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/super-admin/kategori/store" class="space-y-md">
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Nama Kategori <span class="text-error">*</span></label>
        <input type="text" name="nama" required placeholder="Contoh: Obat Bebas"
          class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all">
      </div>
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Deskripsi</label>
        <textarea name="deskripsi" rows="3" placeholder="Opsional"
          class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none transition-all resize-none"></textarea>
      </div>
      <button type="submit" class="w-full flex items-center justify-center gap-sm py-2.5 bg-primary text-white rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all">
        <span class="material-symbols-outlined text-[20px]">add</span>Tambah Kategori
      </button>
    </form>
  </div>

  <!-- Daftar Kategori -->
  <div class="lg:col-span-2 animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden" style="animation-delay:.1s">
    <div class="p-lg border-b border-outline-variant/60 flex justify-between items-center bg-surface-bright/50">
      <h3 class="font-display text-[16px] font-bold text-on-surface">Daftar Kategori</h3>
      <span class="px-3 py-1 bg-surface-variant text-on-surface-variant rounded-full text-[12px] font-bold"><?= count($categories) ?> Kategori</span>
    </div>

    <?php if (empty($categories)): ?>
      <div class="p-xl text-center">
        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-4 opacity-50">category</span>
        <p class="text-[14px] font-medium text-on-surface-variant mb-1">Belum ada data kategori</p>
        <p class="text-[13px] text-outline">Tambahkan kategori pertama Anda melalui form di samping.</p>
      </div>
    <?php else: ?>
      <div class="divide-y divide-outline-variant/30">
        <?php foreach ($categories as $cat): ?>
          <div class="p-md sm:p-lg flex flex-col sm:flex-row sm:items-center justify-between gap-md hover:bg-surface-variant/20 transition-colors">
            <div>
              <h4 class="text-[15px] font-bold text-on-surface mb-0.5"><?= htmlspecialchars($cat['nama']) ?></h4>
              <p class="text-[13px] text-outline line-clamp-2"><?= htmlspecialchars($cat['deskripsi'] ?? 'Tidak ada deskripsi') ?></p>
            </div>
            <div class="flex items-center gap-sm shrink-0">
              <form method="POST" action="<?= BASE_URL ?>/super-admin/kategori/delete" onsubmit="return confirm('Hapus kategori <?= htmlspecialchars(addslashes($cat['nama'])) ?>? Tindakan ini tidak dapat dibatalkan.');" class="inline-block">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <button type="submit" class="p-2 text-error hover:bg-error-container hover:text-on-error-container rounded-lg transition-colors" title="Hapus Kategori">
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
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
