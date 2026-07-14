<div class="mb-lg flex flex-col sm:flex-row justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Master Barang (Global)</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Kelola semua barang dari seluruh poli.</p>
  </div>
  <div class="flex gap-sm">
    <a href="<?= BASE_URL ?>/super-admin/barang/create" class="flex items-center justify-center gap-sm bg-primary text-white px-lg py-3 rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all shadow-card w-full sm:w-auto">
      <span class="material-symbols-outlined text-[20px]">add_circle</span>Tambah Barang
    </a>
  </div>
</div>

<div class="animate-card bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
      <thead>
        <tr class="bg-surface-bright/50 border-b border-outline-variant/60">
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Barang & SKU</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Poli</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Kategori</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Stok</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider">Status</th>
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/30">
        <?php if(empty($items)): ?>
          <tr><td colspan="6" class="text-center py-xl text-outline-variant">Belum ada barang terdaftar.</td></tr>
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
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-error bg-error-container px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-error"></span>Stok Rendah</span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 text-[12px] font-bold text-primary bg-primary-container/30 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Tersedia</span>
                <?php endif; ?>
              </td>
              <td class="py-3 px-md text-right">
                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($item)) ?>)" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit Stok">
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                </button>
                <form method="POST" action="<?= BASE_URL ?>/super-admin/barang/action" class="inline-block" onsubmit="return confirm('Hapus barang ini?');">
                  <input type="hidden" name="action" value="delete_item">
                  <input type="hidden" name="id" value="<?= $item['id'] ?>">
                  <button type="submit" class="p-2 text-error hover:bg-error-container hover:text-on-error-container rounded-lg transition-colors" title="Hapus Barang">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Edit Stok -->
<div id="modal-edit" class="modal-hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
  <div class="modal-overlay absolute inset-0 bg-on-surface/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
  <form method="POST" action="<?= BASE_URL ?>/super-admin/barang/action" class="modal-panel relative bg-surface-container-lowest rounded-2xl shadow-card-hover w-full max-w-sm p-lg">
    <input type="hidden" name="action" value="update_item">
    <input type="hidden" name="id" id="edit-id">
    
    <div class="flex justify-between items-start mb-md">
      <div>
        <h3 class="font-display text-[18px] font-bold text-on-surface">Edit Stok</h3>
        <p id="edit-nama" class="text-[13px] text-outline mt-0.5 font-medium"></p>
      </div>
      <button type="button" onclick="closeEditModal()" class="text-outline hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    
    <div class="space-y-md mb-lg">
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Sisa Stok Fisik</label>
        <div class="flex items-center gap-2">
          <input type="number" name="stok" id="edit-stok" required min="0" class="flex-1 px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none font-bold">
          <span id="edit-satuan" class="text-[13px] text-outline font-bold bg-surface-container px-3 py-2.5 rounded-xl border border-outline-variant"></span>
        </div>
      </div>
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Ambang Minimum Peringatan</label>
        <input type="number" name="ambang_minimum" id="edit-ambang" required min="0" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none">
      </div>
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1.5">Catatan Baru (opsional)</label>
        <textarea name="catatan" id="edit-catatan" rows="2" class="w-full px-md py-2.5 bg-surface border border-outline-variant rounded-xl text-[14px] focus:ring-2 focus:ring-primary/25 outline-none resize-none"></textarea>
      </div>
    </div>
    
    <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl font-bold text-[14px] hover:bg-primary-container active:scale-[0.98] transition-all shadow-card">
      Simpan Perubahan
    </button>
  </form>
</div>

<script>
  function openEditModal(item) {
    document.getElementById('edit-id').value = item.id;
    document.getElementById('edit-nama').textContent = item.nama;
    document.getElementById('edit-stok').value = item.stok;
    document.getElementById('edit-satuan').textContent = item.satuan;
    document.getElementById('edit-ambang').value = item.ambang_minimum;
    document.getElementById('edit-catatan').value = item.catatan || '';
    document.getElementById('modal-edit').classList.remove('modal-hidden');
  }

  function closeEditModal() {
    document.getElementById('modal-edit').classList.add('modal-hidden');
  }

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
