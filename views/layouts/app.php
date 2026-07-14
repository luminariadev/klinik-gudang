<?php
use App\Helpers\Auth;
$user = Auth::user();
$fullName = $user['full_name'] ?? 'Administrator';
$role     = $user['role'] ?? 'Admin';
$initial  = strtoupper(substr($fullName, 0, 1));
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title><?= $title ?? 'Dashboard' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "outline": "#707973", "on-surface-variant": "#404943", "error-container": "#ffdad6",
          "on-error": "#ffffff", "error": "#ba1a1a", "tertiary-container": "#3f6754",
          "primary-fixed-dim": "#95d4b3", "on-secondary": "#ffffff", "surface-container-high": "#e6e9e8",
          "tertiary-fixed": "#c1ecd4", "primary-fixed": "#b1f0ce", "on-tertiary": "#ffffff",
          "on-secondary-fixed": "#002112", "tertiary": "#274f3d", "secondary-fixed": "#c0edd0",
          "on-background": "#191c1c", "surface-dim": "#d8dada", "on-secondary-fixed-variant": "#264f39",
          "on-primary-container": "#a8e7c5", "on-tertiary-fixed-variant": "#274e3d", "on-tertiary-fixed": "#002114",
          "surface-container": "#eceeed", "surface": "#f8faf9", "secondary-fixed-dim": "#a4d1b4",
          "tertiary-fixed-dim": "#a5d0b9", "surface-variant": "#e1e3e2", "surface-container-lowest": "#ffffff",
          "on-surface": "#191c1c", "secondary": "#3e6750", "surface-container-highest": "#e1e3e2",
          "primary": "#0f5238", "on-error-container": "#93000a", "background": "#f6f8f7",
          "primary-container": "#2d6a4f", "surface-tint": "#2c694e", "on-tertiary-container": "#b8e3cb",
          "on-secondary-container": "#426b54", "inverse-on-surface": "#eff1f0", "secondary-container": "#bdeacd",
          "outline-variant": "#bfc9c1", "inverse-surface": "#2e3131", "on-primary-fixed": "#002114",
          "surface-bright": "#f8faf9", "on-primary-fixed-variant": "#0e5138", "on-primary": "#ffffff",
          "inverse-primary": "#95d4b3", "surface-container-low": "#f2f4f3"
        },
        borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", "2xl": "1.25rem", full: "9999px" },
        spacing: { unit: "4px", md: "16px", xs: "4px", "margin-desktop": "48px", gutter: "20px", sm: "8px", "margin-mobile": "16px", xl: "40px", lg: "24px" },
        fontFamily: {
          display: ["Lexend", "sans-serif"],
          body: ["Inter", "sans-serif"]
        },
        boxShadow: {
          card: "0 1px 2px rgba(15,82,56,.04), 0 8px 24px -8px rgba(15,82,56,.10)",
          "card-hover": "0 4px 8px rgba(15,82,56,.06), 0 16px 32px -12px rgba(15,82,56,.16)"
        }
      }
    }
  }
</script>
<style>
  body { font-family: 'Inter', sans-serif; }
  .font-display { font-family: 'Lexend', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; line-height: 1; }

  .pulse-dot { position: relative; }
  .pulse-dot::after {
    content: ''; position: absolute; width: 100%; height: 100%; top: 0; left: 0;
    background: currentColor; border-radius: 50%; opacity: .45; animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite;
  }
  @keyframes pulse { 0%{transform:scale(1);opacity:.45} 100%{transform:scale(2.6);opacity:0} }

  /* === Layout dasar: sidebar tetap di desktop, off-canvas di mobile/tablet === */
  #sidebar { transition: transform .25s ease, width .25s ease; }
  #main-content, #topbar, #footer-shell { transition: margin-left .25s ease, width .25s ease; }

  /* === Sidebar collapse (desktop) === */
  @media (min-width: 1024px) {
    body.sidebar-collapsed #sidebar { width: 84px !important; }
    body.sidebar-collapsed #sidebar .sidebar-label { display: none !important; }
    body.sidebar-collapsed #sidebar .logo-row { justify-content: center; }
    body.sidebar-collapsed #sidebar nav a { justify-content: center; }
    body.sidebar-collapsed #sidebar .mt-auto > div { justify-content: center; }
    body.sidebar-collapsed #main-content,
    body.sidebar-collapsed #topbar,
    body.sidebar-collapsed #footer-shell { margin-left: 84px !important; }
    body.sidebar-collapsed #collapse-chevron { transform: rotate(180deg); }
  }

  /* === Efek ripple saat menu navigasi diklik === */
  .ripple-container { position: relative; overflow: hidden; }
  .ripple {
    position: absolute; border-radius: 50%; transform: scale(0);
    background: rgba(255,255,255,.7); mix-blend-mode: overlay;
    animation: rippleEffect .55s ease-out forwards; pointer-events: none;
  }
  @keyframes rippleEffect { to { transform: scale(4.5); opacity: 0; } }
  .nav-link:active { transform: scale(.97); }
  .nav-link { transition: transform .15s ease, background-color .2s ease, color .2s ease; }

  @media (max-width: 1023px) {
    #sidebar { transform: translateX(-100%); width: 272px; }
    #sidebar.open { transform: translateX(0); }
    #topbar, #main-content, #footer-shell { margin-left: 0 !important; width: 100% !important; }
    #sidebar-overlay.show { display: block; }
  }
  #sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(10,20,15,.45); z-index: 40; backdrop-filter: blur(2px); }

  @media (max-width: 640px) { #topbar .search-box { display: none; } }

  /* Tabel ↔ kartu: tabel hanya tampil di layar lebar, kartu tampil di mobile */
  .desktop-table { display: none; }
  .mobile-cards { display: block; }
  @media (min-width: 768px) {
    .desktop-table { display: block; }
    .mobile-cards { display: none; }
  }

  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #bfc9c1; border-radius: 99px; }

  /* === Animasi === */
  @keyframes fadeInUp { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  @keyframes toastIn { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: translateX(0); } }
  @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(24px); } }
  @keyframes shake { 10%,90%{transform:translateX(-1px)} 20%,80%{transform:translateX(2px)} 30%,50%,70%{transform:translateX(-4px)} 40%,60%{transform:translateX(4px)} }
  @keyframes floatBlob { 0%,100%{transform:translateY(0) translateX(0) scale(1)} 33%{transform:translateY(-24px) translateX(14px) scale(1.05)} 66%{transform:translateY(14px) translateX(-10px) scale(.97)} }
  @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
  @keyframes glowPulse { 0%,100%{box-shadow:0 0 0 0 rgba(15,82,56,.18)} 50%{box-shadow:0 0 0 8px rgba(15,82,56,0)} }
  @keyframes iconPop { 0%{transform:scale(.6) rotate(-8deg); opacity:0} 60%{transform:scale(1.12) rotate(3deg); opacity:1} 100%{transform:scale(1) rotate(0)} }
  @keyframes underlineGrow { from { transform: scaleX(0); } to { transform: scaleX(1); } }

  .animate-row { animation: fadeInUp .35s cubic-bezier(.22,1,.36,1) both; }
  .animate-card { animation: fadeInUp .5s cubic-bezier(.22,1,.36,1) both; }
  .icon-badge { animation: iconPop .55s cubic-bezier(.22,1,.36,1) both; }
  .kategori-row:active { transform: scale(0.995); }
  .status-chip { cursor: pointer; }
  .status-chip:active { transform: scale(0.95); }
  .status-chip { transition: all .2s ease; }

  .row-hidden { display: none !important; }
  .card-hidden { display: none !important; }

  .modal-overlay { transition: opacity .2s ease; }
  .modal-panel { transition: opacity .2s ease, transform .2s ease; }
  .modal-hidden .modal-overlay { opacity: 0; pointer-events: none; }
  .modal-hidden .modal-panel { opacity: 0; transform: scale(.95) translateY(8px); }
  .modal-hidden { pointer-events: none; }

  .toast-enter { animation: toastIn .25s ease both; }
  .toast-leave { animation: toastOut .25s ease both; }

  .shake { animation: shake .4s; }

  .count-up { transition: color .3s ease; }

  .stat-card { position: relative; overflow: hidden; transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s ease; }
  .stat-card::after {
    content: ''; position: absolute; top: -30%; right: -15%; width: 90px; height: 90px; border-radius: 999px;
    background: radial-gradient(circle, rgba(15,82,56,.08), transparent 70%);
    opacity: 0; transition: opacity .35s ease;
  }
  .stat-card:hover::after { opacity: 1; }
  .stat-card:hover { transform: translateY(-3px); }

  .chart-card:hover { box-shadow: 0 4px 8px rgba(15,82,56,.06), 0 16px 32px -12px rgba(15,82,56,.16); }

  .section-title-bar { position: relative; display: inline-block; }
  .section-title-bar::after {
    content: ''; position: absolute; left: 0; bottom: -6px; height: 3px; width: 100%;
    background: linear-gradient(90deg, #0f5238, #95d4b3); border-radius: 999px;
    transform-origin: left; animation: underlineGrow .6s cubic-bezier(.22,1,.36,1) .1s both;
  }

  .bg-blob {
    position: fixed; border-radius: 999px; filter: blur(70px); pointer-events: none; z-index: 0;
    animation: floatBlob 16s ease-in-out infinite;
  }
  .bg-pattern-dots {
    background-image: radial-gradient(circle at 1.5px 1.5px, rgba(15,82,56,.05) 1.2px, transparent 0);
    background-size: 28px 28px;
  }
  .live-dot { position: relative; }
  .live-dot::after {
    content: ''; position: absolute; inset: -6px; border-radius: 999px;
    border: 2px solid rgba(15,82,56,.35); animation: glowPulse 2s ease-out infinite;
  }

  table tbody tr, #cards-body > div { transition: background-color .2s ease, transform .15s ease; }
</style>
</head>
<body class="bg-background text-on-surface overflow-x-hidden">

<!-- Dekorasi background: blob gradasi lembut + pola titik -->
<div class="bg-blob w-[420px] h-[420px] bg-primary/10 top-[-8%] right-[-6%]"></div>
<div class="bg-blob w-[380px] h-[380px] bg-secondary/10 bottom-[-10%] left-[10%]" style="animation-delay:-6s;"></div>
<div class="fixed inset-0 bg-pattern-dots pointer-events-none z-0"></div>

<div id="sidebar-overlay" onclick="toggleSidebar()"></div>
<div id="toast-container" class="fixed top-20 right-4 z-[70] flex flex-col gap-2 w-[90vw] max-w-sm"></div>

<!-- Sidebar Navigation -->
<aside id="sidebar" class="h-screen w-64 fixed left-0 top-0 flex flex-col py-lg px-md z-50" style="background:linear-gradient(180deg,#0f5238,#0b3e2c);">
  <button id="sidebar-collapse-btn" onclick="toggleSidebarCollapse()" title="Ciutkan/Perluas sidebar" class="hidden lg:flex absolute -right-3 top-9 w-6 h-6 rounded-full bg-white border border-outline-variant/60 shadow-card items-center justify-center text-primary hover:scale-110 active:scale-90 transition-transform z-10">
    <span class="material-symbols-outlined text-[16px] transition-transform duration-300" id="collapse-chevron">chevron_left</span>
  </button>

  <div class="logo-row flex items-center gap-md mb-xl px-xs">
    <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 overflow-hidden">
      <!-- Menggunakan div pengganti logo jika gambar tidak ada -->
      <span class="material-symbols-outlined text-white">local_hospital</span>
    </div>
    <div class="sidebar-label overflow-hidden whitespace-nowrap">
      <h1 class="font-display text-[18px] font-bold text-white leading-tight">INVENTARIS</h1>
      <p class="text-[12px] text-white/60">Klinik Kampus</p>
    </div>
    <button onclick="toggleSidebar()" class="sidebar-label lg:hidden ml-auto p-1.5 text-white/70 hover:text-white">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <nav class="flex-1 space-y-2 mt-2">
    <?php if ($user['role'] === 'super_admin'): ?>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1 sidebar-label">Menu Utama</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= $_SERVER['REQUEST_URI'] == BASE_URL . '/super-admin' || $_SERVER['REQUEST_URI'] == BASE_URL . '/super-admin/' ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin" title="Dashboard Global">
            <span class="material-symbols-outlined text-[20px] shrink-0">dashboard</span><span class="sidebar-label text-[14px] whitespace-nowrap">Dashboard</span>
        </a>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mt-lg mb-1 sidebar-label">Data Master</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/super-admin/polis') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin/polis" title="Master Poli">
            <span class="material-symbols-outlined text-[20px] shrink-0">domain</span><span class="sidebar-label text-[14px] whitespace-nowrap">Master Poli</span>
        </a>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/super-admin/users') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin/users" title="Master User">
            <span class="material-symbols-outlined text-[20px] shrink-0">group</span><span class="sidebar-label text-[14px] whitespace-nowrap">Master User</span>
        </a>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/super-admin/kategori') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin/kategori" title="Master Kategori">
            <span class="material-symbols-outlined text-[20px] shrink-0">category</span><span class="sidebar-label text-[14px] whitespace-nowrap">Master Kategori</span>
        </a>

        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mt-lg mb-1 sidebar-label">Manajemen Barang & Laporan</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/super-admin/barang') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin/barang" title="Master Barang">
            <span class="material-symbols-outlined text-[20px] shrink-0">inventory_2</span><span class="sidebar-label text-[14px] whitespace-nowrap">Master Barang</span>
        </a>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/super-admin/laporan-penggunaan') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/super-admin/laporan-penggunaan" title="Laporan Penggunaan">
            <span class="material-symbols-outlined text-[20px] shrink-0">summarize</span><span class="sidebar-label text-[14px] whitespace-nowrap">Laporan Penggunaan</span>
        </a>

    <?php elseif ($user['role'] === 'admin_poli'): ?>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1 sidebar-label">Menu Utama</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/items/create') === false && strpos($_SERVER['REQUEST_URI'], '/laporan') === false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/dashboard" title="Dashboard Poli">
            <span class="material-symbols-outlined text-[20px] shrink-0">dashboard</span><span class="sidebar-label text-[14px] whitespace-nowrap">Dashboard</span>
        </a>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mt-lg mb-1 sidebar-label">Manajemen Barang</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/items/create') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/items/create" title="Input Barang Baru">
            <span class="material-symbols-outlined text-[20px] shrink-0">add_circle</span><span class="sidebar-label text-[14px] whitespace-nowrap">Input Barang Baru</span>
        </a>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mt-lg mb-1 sidebar-label">Pelaporan</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl <?= strpos($_SERVER['REQUEST_URI'], '/laporan') !== false ? 'text-primary bg-white font-bold shadow-card' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' ?> transition-all duration-200" href="<?= BASE_URL ?>/laporan" title="Laporan & Export">
            <span class="material-symbols-outlined text-[20px] shrink-0">summarize</span><span class="sidebar-label text-[14px] whitespace-nowrap">Laporan & Export</span>
        </a>

    <?php else: // Perawat ?>
        <p class="px-md text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1 sidebar-label">Menu Utama</p>
        <a class="nav-link ripple-container relative overflow-hidden flex items-center gap-md py-2.5 px-md rounded-xl text-primary bg-white font-bold shadow-card transition-all duration-200" href="<?= BASE_URL ?>/dashboard" title="Dashboard">
            <span class="material-symbols-outlined text-[20px] shrink-0">dashboard</span><span class="sidebar-label text-[14px] whitespace-nowrap">Dashboard</span>
        </a>
    <?php endif; ?>
  </nav>

  <div class="mt-auto pt-md border-t border-white/15">
    <div class="flex items-center gap-md mt-md px-xs">
      <div class="w-9 h-9 rounded-full text-primary flex items-center justify-center font-bold shrink-0 shadow-sm bg-white ring-2 ring-white/30"><?= htmlspecialchars($initial) ?></div>
      <div class="sidebar-label overflow-hidden flex-1 whitespace-nowrap">
        <p class="text-[13px] font-bold truncate text-white"><?= htmlspecialchars($fullName) ?></p>
        <p class="text-[10px] text-white/60 uppercase tracking-wider"><?= htmlspecialchars(str_replace('_', ' ', $role)) ?></p>
      </div>
      <a href="<?= BASE_URL ?>/logout" title="Logout" class="sidebar-label ripple-container relative overflow-hidden p-1.5 text-white/70 hover:text-white rounded-lg hover:bg-white/10 transition-colors shrink-0">
        <span class="material-symbols-outlined text-[20px]">logout</span>
      </a>
    </div>
  </div>
</aside>

<!-- Top Navigation -->
<header id="topbar" class="fixed top-0 right-0 left-0 h-16 bg-surface/85 backdrop-blur-md z-30 flex justify-between items-center gap-md px-margin-mobile sm:px-margin-desktop ml-64 border-b border-outline-variant/50 shadow-[0_1px_0_rgba(15,82,56,.04)]">
  <div class="flex items-center gap-md flex-1 min-w-0">
    <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-on-surface-variant hover:text-primary shrink-0">
      <span class="material-symbols-outlined">menu</span>
    </button>
    <div class="relative w-full max-w-md search-box">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
      <input id="search-input" class="w-full pl-10 pr-md py-2 bg-surface-container-low border-none rounded-full focus:ring-2 focus:ring-primary/25 text-[14px] placeholder:text-outline" placeholder="Cari obat atau alat kesehatan…" type="text">
    </div>
  </div>
  <div class="flex items-center gap-md sm:gap-lg shrink-0">
    <button class="relative p-2 text-on-surface-variant hover:text-primary transition-colors">
      <span class="material-symbols-outlined">notifications</span>
      <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full border-2 border-surface"></span>
    </button>
    <div class="h-7 w-[1px] bg-outline-variant hidden sm:block"></div>
    <div class="flex items-center gap-sm">
      <span class="text-[12px] text-primary font-bold hidden sm:inline">Sistem Aktif</span>
      <div class="w-2 h-2 rounded-full bg-primary pulse-dot"></div>
    </div>
  </div>
</header>

<!-- Main Content -->
<main id="main-content" class="ml-64 pt-24 pb-16 px-margin-mobile sm:px-margin-desktop min-h-screen relative z-10">
    <?php 
        if (isset($contentView)) {
            require __DIR__ . '/../pages/' . $contentView . '.php';
        }
    ?>
</main>

<!-- Footer -->
<footer id="footer-shell" class="ml-64 bg-surface-container-lowest border-t border-outline-variant/60 py-md px-margin-mobile sm:px-margin-desktop flex flex-col sm:flex-row gap-sm justify-between items-center text-center sm:text-left z-10 relative">
  <div class="flex flex-col sm:flex-row items-center gap-xs sm:gap-md">
    <span class="text-[12px] font-bold text-primary">Sistem Inventaris</span>
    <span class="text-[12px] text-outline">© <?= date('Y') ?> Klinik Pratama UIN Bandung.</span>
  </div>
</footer>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
  }

  function toggleSidebarCollapse() {
    document.body.classList.toggle('sidebar-collapsed');
    // Save to local storage for persistence
    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebar_collapsed', isCollapsed);
  }

  // Check saved state
  if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
    document.body.classList.add('sidebar-collapsed');
  }
</script>
</body>
</html>
