<div class="mb-lg">
  <h2 class="font-display text-[22px] sm:text-[26px] font-bold text-primary mb-2">Kelola Pengguna</h2>
  <p class="text-on-surface-variant text-[14px] sm:text-[15px]">Buat akun baru untuk Admin Poli atau Perawat dan tugaskan ke poli tertentu.</p>
</div>

<div class="grid lg:grid-cols-5 gap-lg items-start">

  <!-- Kolom kiri: Form / Hasil -->
  <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card p-lg animate-card">

    <?php if ($hasilAdmin): ?>
      <!-- === Tampilan hasil sukses (sekali tampil) === -->
      <div class="flex items-center gap-2 text-primary mb-md">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="font-bold text-[15px]"><?= !empty($hasilAdmin['isReset']) ? 'Password berhasil direset!' : 'Akun berhasil dibuat!' ?></span>
      </div>
      <p class="text-[12px] text-outline mb-md">Catat / salin kredensial ini sekarang. Password tidak akan ditampilkan lagi setelah halaman ini ditutup atau dimuat ulang.</p>
      <div class="bg-surface-container-low rounded-xl p-md space-y-2 text-[13px] mb-md">
        <div class="flex justify-between"><span class="text-outline">Nama</span><span class="font-bold"><?= htmlspecialchars($hasilAdmin['fullName']) ?></span></div>
        <div class="flex justify-between"><span class="text-outline">Peran</span><span class="font-bold"><?= htmlspecialchars($hasilAdmin['role']) ?></span></div>
        <div class="flex justify-between items-center"><span class="text-outline">Username</span><span id="hasil-username" class="font-mono font-bold"><?= htmlspecialchars($hasilAdmin['username']) ?></span></div>
        <div class="flex justify-between items-center"><span class="text-outline">Password</span><span id="hasil-password" class="font-mono font-bold"><?= htmlspecialchars($hasilAdmin['password']) ?></span></div>
      </div>
      <div class="flex gap-2">
        <button type="button" onclick="salinKredensial()" class="flex-1 py-2.5 border-2 border-primary text-primary rounded-xl font-bold text-[13px] hover:bg-primary/5 transition-colors">Salin</button>
        <a href="<?= BASE_URL ?>/super-admin/users" class="flex-1 py-2.5 bg-primary text-on-primary rounded-xl font-bold text-[13px] text-center hover:bg-primary/90 transition-colors">Selesai</a>
      </div>

    <?php else: ?>
      <?php if ($flashInfo): ?>
        <div class="mb-md text-[13px] text-primary bg-primary/10 rounded-lg px-3 py-2.5 flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">info</span><?= htmlspecialchars($flashInfo) ?>
        </div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="mb-md text-[13px] text-error bg-error-container/40 rounded-lg px-3 py-2.5 flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">error</span><?= htmlspecialchars($flashError) ?>
        </div>
      <?php endif; ?>

      <!-- === Form tambah user === -->
      <form method="POST" action="<?= BASE_URL ?>/super-admin/users/store" class="space-y-md">
        <div>
          <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Nama Lengkap</label>
          <input type="text" name="full_name" required minlength="3"
            class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant/60 rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px]"
            placeholder="Contoh: Siti Nurhaliza">
        </div>
        <div>
          <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Pilih Poli</label>
          <select name="poli_id" required class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant/60 rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px]">
            <option value="" disabled selected>-- Pilih Poli --</option>
            <?php foreach ($polis as $poli): ?>
                <option value="<?= $poli['id'] ?>"><?= htmlspecialchars($poli['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Peran</label>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center gap-2 px-3.5 py-2.5 border-2 border-outline-variant/60 rounded-xl cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
              <input type="radio" name="role" value="admin_poli" checked class="accent-[#0f5238]">
              <span class="text-[13px] font-bold">Admin Poli</span>
            </label>
            <label class="flex items-center gap-2 px-3.5 py-2.5 border-2 border-outline-variant/60 rounded-xl cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
              <input type="radio" name="role" value="perawat" class="accent-[#0f5238]">
              <span class="text-[13px] font-bold">Perawat</span>
            </label>
          </div>
        </div>
        <div>
          <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Username</label>
          <input type="text" name="username" required minlength="4" pattern="[a-zA-Z0-9._]+"
            class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant/60 rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px] font-mono"
            placeholder="Contoh: admin.poli">
        </div>
        <div>
          <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Password</label>
          <input type="password" name="password" required minlength="8"
            class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant/60 rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px]"
            placeholder="Minimal 8 karakter">
        </div>
        <button type="submit" class="w-full py-2.5 bg-primary text-on-primary rounded-xl font-bold text-[14px] hover:bg-primary/90 transition-colors shadow-card">
          Buat Akun Pengguna
        </button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Kolom kanan: Daftar admin yang sudah ada -->
  <div class="lg:col-span-3 bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-card overflow-hidden animate-card" style="animation-delay:.08s">
    <div class="p-lg border-b border-outline-variant/60 flex items-center justify-between">
      <h3 class="font-display text-[16px] font-bold text-on-surface">Daftar Pengguna Terdaftar</h3>
      <span class="text-[12px] text-outline"><?= count($users) ?> pengguna</span>
    </div>

    <?php if (empty($users)): ?>
      <div class="py-14 px-lg text-center">
        <span class="material-symbols-outlined text-[36px] text-outline-variant mb-2">group_off</span>
        <p class="text-on-surface-variant text-[13px] font-medium">Belum ada pengguna yang ditambahkan.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container text-outline text-[11px] border-b border-outline-variant/60">
              <th class="py-md px-lg font-bold uppercase tracking-wider">Nama</th>
              <th class="py-md px-md font-bold uppercase tracking-wider">Username</th>
              <th class="py-md px-md font-bold uppercase tracking-wider">Peran & Poli</th>
              <th class="py-md px-lg font-bold uppercase tracking-wider text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <?php foreach ($users as $i => $user): ?>
            <tr class="hover:bg-primary/[.03] transition-colors">
              <td class="py-md px-lg font-bold text-[14px] text-on-surface"><?= htmlspecialchars($user['full_name']) ?></td>
              <td class="py-md px-md font-mono text-[13px] text-outline"><?= htmlspecialchars($user['username']) ?></td>
              <td class="py-md px-md">
                <div class="flex flex-col gap-1 items-start">
                  <span class="px-2.5 py-1 <?= $user['role'] === 'admin_poli' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary' ?> rounded-full text-[10px] font-bold uppercase tracking-wide">
                    <?= $user['role'] === 'admin_poli' ? 'Admin Poli' : 'Perawat' ?>
                  </span>
                  <span class="text-[12px] text-on-surface-variant font-medium"><?= htmlspecialchars($user['nama_poli'] ?? '—') ?></span>
                </div>
              </td>
              <td class="py-md px-lg text-right">
                <div class="flex justify-end gap-1">
                  <button type="button" title="Reset Password" onclick="openResetModal(<?= (int) $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['full_name'])) ?>')"
                    class="p-2 text-outline hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                  </button>
                  <form method="POST" action="<?= BASE_URL ?>/super-admin/users/delete" onsubmit="return confirm('Hapus akun <?= htmlspecialchars(addslashes($user['full_name'])) ?>? Tindakan ini tidak dapat dibatalkan.');">
                    <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                    <button type="submit" title="Hapus Pengguna" class="p-2 text-outline hover:text-error hover:bg-error-container/40 rounded-lg transition-colors">
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ===== Modal: Reset Password (manual) ===== -->
<div id="modal-reset-password" class="fixed inset-0 z-[100] hidden items-center justify-center p-md bg-[#0a140f]/55 backdrop-blur-[2px]">
  <div class="bg-surface-container-lowest w-full max-w-sm rounded-2xl shadow-card-hover overflow-hidden">
    <div class="p-lg border-b border-outline-variant/60 flex items-center justify-between">
      <h3 class="font-display font-bold text-[16px] text-on-surface">Reset Password</h3>
      <button type="button" onclick="closeResetModal()" class="p-1.5 text-outline hover:text-error rounded-lg hover:bg-error-container/30">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/super-admin/users/reset-password" class="p-lg space-y-md">
      <input type="hidden" name="user_id" id="reset-user-id" value="">
      <p class="text-[13px] text-on-surface-variant">Password baru untuk <span id="reset-admin-name" class="font-bold text-on-surface"></span>:</p>
      <div>
        <label class="block text-[12px] font-bold text-on-surface-variant mb-1.5">Password Baru</label>
        <input type="password" name="new_password" required minlength="8"
          class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant/60 rounded-xl focus:ring-2 focus:ring-primary/25 outline-none text-[14px]"
          placeholder="Minimal 8 karakter">
      </div>
      <button type="submit" class="w-full py-2.5 bg-primary text-on-primary rounded-xl font-bold text-[14px] hover:bg-primary/90 transition-colors shadow-sm">
        Simpan Password Baru
      </button>
    </form>
  </div>
</div>

<script>
  function salinKredensial() {
    const teks = `Username: ${document.getElementById('hasil-username').textContent}\nPassword: ${document.getElementById('hasil-password').textContent}`;
    navigator.clipboard.writeText(teks).then(() => alert('Kredensial disalin ke clipboard.'));
  }
  function openResetModal(userId, namaAdmin) {
    document.getElementById('reset-user-id').value = userId;
    document.getElementById('reset-admin-name').textContent = namaAdmin;
    const modal = document.getElementById('modal-reset-password');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }
  function closeResetModal() {
    const modal = document.getElementById('modal-reset-password');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>
