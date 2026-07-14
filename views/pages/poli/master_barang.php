<div class="mb-lg flex flex-col sm:flex-row justify-between sm:items-end gap-md">
  <div>
    <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-1">Master Barang</h2>
    <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Kelola data semua barang di poli Anda secara penuh.</p>
  </div>
  <a href="<?= BASE_URL ?>/items/create" class="flex items-center justify-center gap-sm bg-primary text-white px-md py-2.5 rounded-xl font-bold text-[13px] hover:bg-primary-container transition-all shadow-sm">
    <span class="material-symbols-outlined text-[18px]">add</span>Tambah Barang
  </a>
</div>

<form method="GET" action="<?= BASE_URL ?>/items" class="no-print bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg mb-lg">
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
      <a href="<?= BASE_URL ?>/items" class="flex items-center justify-center px-3 py-2 border border-outline-variant rounded-xl font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-all">Reset</a>
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
          <th class="py-3 px-md text-[12px] font-bold text-outline uppercase tracking-wider text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/30">
        <?php if(empty($items)): ?>
          <tr><td colspan="5" class="text-center py-xl text-outline-variant">Tidak ada barang yang ditemukan.</td></tr>
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
              <td class="py-3 px-md text-right">
                <button type="button" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-transform hover:scale-110" title="Edit Barang"
                  onclick='openEditModal(<?= json_encode($item) ?>)'>
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                </button>
                <button type="button" class="p-2 text-error hover:bg-error/10 rounded-lg transition-transform hover:scale-110" title="Hapus Barang"
                  onclick='openDeleteModal(<?= (int) $item["id"] ?>, <?= json_encode($item["nama"]) ?>)'>
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="modal-hidden fixed inset-0 z-[65] flex items-center justify-center p-4">
  <div class="modal-overlay absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
  <form method="POST" action="<?= BASE_URL ?>/items/action" class="modal-panel relative bg-surface-container-lowest rounded-2xl shadow-card w-full max-w-md p-0 overflow-hidden">
    <div class="p-md border-b border-outline-variant/60 bg-surface-bright flex justify-between items-center">
      <h3 class="font-display font-bold text-[16px] text-on-surface">Edit Barang</h3>
      <button type="button" onclick="closeModal('modal-edit')" class="text-outline hover:text-error"><span class="material-symbols-outlined">close</span></button>
    </div>
    
    <div class="p-md space-y-md max-h-[70vh] overflow-y-auto">
      <input type="hidden" name="action" value="update_item">
      <input type="hidden" name="id" id="edit-id">
      
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Nama Barang</label>
        <input type="text" name="nama" id="edit-nama" required class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none">
      </div>
      
      <div class="grid grid-cols-2 gap-sm">
        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">SKU</label>
          <input type="text" name="sku" id="edit-sku" required class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none font-mono">
        </div>
        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Kategori</label>
          <select name="kategori" id="edit-kategori" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none">
            <?php foreach ($kategoris as $k): ?>
              <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      
      <div class="grid grid-cols-3 gap-sm">
        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Stok Fisik</label>
          <input type="number" name="stok" id="edit-stok" min="0" required class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none">
        </div>
        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Batas Min</label>
          <input type="number" name="ambang_minimum" id="edit-ambang" min="0" required class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none">
        </div>
        <div>
          <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Satuan</label>
          <input type="text" name="satuan" id="edit-satuan" required class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none">
        </div>
      </div>
      
      <div>
        <label class="block text-[12px] font-bold text-outline uppercase tracking-wide mb-1">Catatan</label>
        <textarea name="catatan" id="edit-catatan" rows="2" class="w-full px-md py-2 bg-surface border border-outline-variant rounded-xl text-[13px] focus:ring-2 focus:ring-primary/25 outline-none resize-none"></textarea>
      </div>
    </div>
    
    <div class="p-md border-t border-outline-variant/60 flex gap-sm bg-surface-bright">
      <button type="button" onclick="closeModal('modal-edit')" class="flex-1 py-2 rounded-xl border border-outline-variant font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</button>
      <button type="submit" class="flex-1 py-2 rounded-xl bg-primary text-white font-bold text-[13px] hover:bg-primary-container transition-all shadow-sm">Simpan Perubahan</button>
    </div>
  </form>
</div>

<!-- Modal Hapus -->
<div id="modal-delete" class="modal-hidden fixed inset-0 z-[65] flex items-center justify-center p-4">
  <div class="modal-overlay absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-delete')"></div>
  <form method="POST" action="<?= BASE_URL ?>/items/action" class="modal-panel relative bg-surface-container-lowest rounded-2xl shadow-card w-full max-w-sm p-lg text-center">
    <input type="hidden" name="action" value="delete_item">
    <input type="hidden" name="id" id="delete-id">
    
    <div class="w-12 h-12 rounded-full bg-error-container text-error flex items-center justify-center mx-auto mb-md">
      <span class="material-symbols-outlined text-[24px]">delete_forever</span>
    </div>
    <h3 class="font-display font-bold text-[18px] text-on-surface mb-2">Hapus Barang?</h3>
    <p class="text-[13px] text-outline mb-lg">Anda yakin ingin menghapus barang <strong id="delete-nama" class="text-on-surface"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
    
    <div class="flex gap-sm">
      <button type="button" onclick="closeModal('modal-delete')" class="flex-1 py-2 rounded-xl border border-outline-variant font-bold text-[13px] text-on-surface-variant hover:bg-surface-variant/40 transition-colors">Batal</button>
      <button type="submit" class="flex-1 py-2 rounded-xl bg-error text-white font-bold text-[13px] hover:bg-error/90 transition-all shadow-sm">Ya, Hapus</button>
    </div>
  </form>
</div>

<script>
  function openEditModal(item) {
    document.getElementById('edit-id').value = item.id;
    document.getElementById('edit-nama').value = item.nama;
    document.getElementById('edit-sku').value = item.sku;
    document.getElementById('edit-kategori').value = item.kategori;
    document.getElementById('edit-stok').value = item.stok;
    document.getElementById('edit-ambang').value = item.ambang_minimum;
    document.getElementById('edit-satuan').value = item.satuan;
    document.getElementById('edit-catatan').value = item.catatan || '';
    
    document.getElementById('modal-edit').classList.remove('modal-hidden');
  }
  
  function openDeleteModal(id, nama) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-nama').textContent = nama;
    document.getElementById('modal-delete').classList.remove('modal-hidden');
  }
  
  function closeModal(id) {
    document.getElementById(id).classList.add('modal-hidden');
  }
</script>
