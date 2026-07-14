<!DOCTYPE html>
<html class="light" lang="id">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Login | Campus Clinic Inventory</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "surface": "#f8faf9",
          "secondary": "#3e6750",
          "on-tertiary-fixed-variant": "#274e3d",
          "surface-bright": "#fcfdfc",
          "error-container": "#ffdad6",
          "surface-container-high": "#e6e9e8",
          "on-tertiary-fixed": "#002114",
          "outline-variant": "#bfc9c1",
          "on-surface-variant": "#404943",
          "surface-dim": "#d8dada",
          "on-tertiary-container": "#b8e3cb",
          "secondary-fixed": "#c0edd0",
          "on-primary-fixed-variant": "#0e5138",
          "secondary-fixed-dim": "#a4d1b4",
          "on-error": "#ffffff",
          "tertiary": "#274f3d",
          "primary-fixed": "#b1f0ce",
          "error": "#ba1a1a",
          "on-tertiary": "#ffffff",
          "on-surface": "#191c1c",
          "on-primary-fixed": "#002114",
          "primary-fixed-dim": "#95d4b3",
          "on-error-container": "#93000a",
          "tertiary-fixed-dim": "#a5d0b9",
          "outline": "#707973",
          "surface-container-highest": "#e1e3e2",
          "primary": "#0f5238",
          "surface-container": "#eceeed",
          "inverse-primary": "#95d4b3",
          "on-secondary-fixed-variant": "#264f39",
          "on-background": "#191c1c",
          "on-primary": "#ffffff",
          "on-secondary-container": "#426b54",
          "inverse-surface": "#2e3131",
          "surface-tint": "#2c694e",
          "tertiary-container": "#3f6754",
          "on-secondary": "#ffffff",
          "secondary-container": "#bdeacd",
          "background": "#f8faf9",
          "primary-container": "#2d6a4f",
          "tertiary-fixed": "#c1ecd4",
          "on-primary-container": "#a8e7c5",
          "surface-container-low": "#f2f4f3",
          "surface-container-lowest": "#ffffff",
          "inverse-on-surface": "#eff1f0",
          "surface-variant": "#e1e3e2",
          "on-secondary-fixed": "#002112"
        },
        borderRadius: {
          DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", "2xl": "1rem", "3xl": "1.5rem", full: "9999px"
        },
        spacing: {
          "margin-mobile": "16px", xl: "40px", xs: "4px", md: "16px",
          "margin-desktop": "64px", unit: "4px", sm: "8px", gutter: "24px", lg: "24px"
        },
        fontFamily: {
          "display-lg": ["Inter", "sans-serif"], "label-sm": ["Inter", "sans-serif"],
          "headline-md": ["Inter", "sans-serif"], "body-lg": ["Inter", "sans-serif"], "body-md": ["Inter", "sans-serif"]
        },
        fontSize: {
          "display-lg": ["48px", {lineHeight: "1.1", letterSpacing: "-0.04em", fontWeight: "800"}],
          "label-sm": ["13px", {lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "600"}],
          "headline-md": ["28px", {lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700"}],
          "body-lg": ["18px", {lineHeight: "28px", fontWeight: "400"}],
          "body-md": ["16px", {lineHeight: "24px", fontWeight: "400"}]
        },
        boxShadow: {
          "organic-sm": "0 2px 4px rgba(15, 82, 56, 0.04), 0 4px 12px rgba(15, 82, 56, 0.04)",
          "organic": "0 4px 6px -1px rgba(15, 82, 56, 0.05), 0 10px 30px -5px rgba(15, 82, 56, 0.08)",
          "organic-xl": "0 20px 40px -10px rgba(15, 82, 56, 0.12), 0 10px 20px -5px rgba(15, 82, 56, 0.05)"
        }
      }
    }
  }
</script>
<style>
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
  .pulse-animation { animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite; }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.15)} }
  .grain-texture { position: fixed; inset:0; pointer-events:none; z-index:9999; opacity:.04;
    background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
  .floating-shape { animation: float 8s ease-in-out infinite; }
  @keyframes float { 0%,100%{transform:translateY(0) rotate(0)} 50%{transform:translateY(-30px) rotate(3deg)} }
  .bg-pattern { background-image: radial-gradient(circle at 2px 2px, #2d6a4f08 1px, transparent 0); background-size:40px 40px; }
  .glass-morphism { background: rgba(255,255,255,.75); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%); }
  .loading-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background-color:currentColor; animation: dot-pulse 1.4s infinite ease-in-out both; }
  @keyframes dot-pulse { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }

  /* === Penyesuaian responsif tambahan === */
  @media (max-width: 480px) {
    .login-card { padding: 28px 20px !important; border-radius: 28px !important; }
    .login-card h2 { font-size: 26px !important; }
  }
  @media (min-width: 481px) and (max-width: 1023px) {
    .login-card { max-width: 460px; }
  }

  /* === Hapus ikon mata bawaan Edge/IE === */
  input::-ms-reveal,
  input::-ms-clear {
    display: none;
  }
</style>
</head>
<body class="bg-surface font-body-md text-on-surface min-h-screen flex flex-col antialiased">
<div class="grain-texture"></div>

<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-margin-desktop py-5 md:py-6 bg-transparent">
  <div class="flex items-center gap-2 md:gap-3 group cursor-pointer">
    <div class="w-9 h-9 md:w-11 md:h-11 flex items-center justify-center transition-all group-hover:rotate-6 group-hover:scale-105">
      <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2bgG_PN81oSAve8ujm_8w_VCgDjnhLIZjsxbKoVyxbr82quoehGW_mjTfvI3DY2xReMVnEvFy9e4pg2C9n3oYgt85Xa2ZTB6Nz3q_vCmcIsPtgdJviwP45xkBVAuQ7zYVaHHNy_2ER96SOGsvEdo0F7YEo-yBXpWVc--Qz43Q5fEHl8UVEgB0tvgVdbbmMN3dpzTuRCmnyJOer-GdWPgrnw7DqU0r5xPPyjUHFQMf4-axqeeb_aBMbOCEeCx6rXDkBEStRfizvEuJ" alt="UIN Logo" class="w-full h-full object-contain">
    </div>
    <div class="flex flex-col gap-1 leading-none">
      <span class="font-display-lg text-[18px] md:text-[22px] text-primary tracking-tight font-extrabold">Klinik UIN Bandung</span>
      <span class="text-[9px] md:text-[10px] uppercase tracking-[0.25em] text-secondary/60 font-bold">Medical Inventory</span>
    </div>
  </div>

</header>

<main class="flex-grow flex items-center justify-center relative overflow-hidden pt-24 pb-10 md:pb-16">
  <div class="absolute top-[-5%] right-[-5%] w-[300px] h-[300px] md:w-[600px] md:h-[600px] bg-secondary-container/10 rounded-full blur-[80px] md:blur-[120px] floating-shape"></div>
  <div class="absolute bottom-[-10%] left-[-5%] w-[280px] h-[280px] md:w-[500px] md:h-[500px] bg-primary-fixed/15 rounded-full blur-[80px] md:blur-[120px] floating-shape" style="animation-delay:-3s;"></div>
  <div class="absolute inset-0 bg-pattern opacity-100 pointer-events-none"></div>

  <div class="container mx-auto px-margin-mobile md:px-margin-desktop grid lg:grid-cols-12 gap-xl items-center relative z-10">

    <!-- Sisi kiri: hanya tampil di layar besar -->
    <div class="hidden lg:flex lg:col-span-6 flex-col gap-12">
      <div class="max-w-xl">
        <h1 class="font-display-lg text-display-lg text-primary mb-6">Sistem Management <br><span class="text-secondary/70 font-light italic">Barang Klinik</span> <span class="text-primary-container">UIN Sunan Gunung Djati</span></h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant/80 mb-8 max-w-md leading-relaxed">Solusi manajemen inventaris modern untuk ekosistem kesehatan kampus. <span class="font-semibold text-secondary">Pemantauan stok akurat</span> untuk mendukung pelayanan klinik yang prima.</p>
      </div>
      <div class="relative group">
        <div class="relative z-10 w-full max-w-lg aspect-[16/10] rounded-[40px] overflow-hidden shadow-organic-xl border border-white/40">
          <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent mix-blend-overlay"></div>
          <img alt="Laboratory minimalist view" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQRloKku7m4evOrLJTtLC6zrkXnKApP9Sgce5b7ZF7BL-9elc-lYOFLHLFj-2Mkvv03grzEDBkTNYkELND6kyL_I5V5CLrOPYVDV4kJR5gMcBlLBG_g8GeltV8pCXfYp-J0aQBlv7y6GuYE0P0Jpw4nat5psjnOsvO59JRonV7LlmgZ7B99o22LXW3uOAsQGYBcxolGx85xx35rbPoM49fm-AY6ckpVHy-9e8tvLtZFuXs-mdVosHf6VK_wIVARaWGIwrHOa5fdVog">
          <div class="absolute bottom-6 left-6 right-6 glass-morphism p-5 rounded-2xl border border-white/50 flex items-center justify-between shadow-organic">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[20px]">insights</span>
              </div>
              <div>
                <div class="text-[12px] font-bold text-primary uppercase tracking-tighter">Inventory Pulse</div>
                <div class="text-[14px] text-on-surface-variant">Real-time sync active</div>
              </div>
            </div>
            <div class="flex gap-1">
              <div class="loading-dot" style="animation-delay:0s"></div>
              <div class="loading-dot" style="animation-delay:.2s"></div>
              <div class="loading-dot" style="animation-delay:.4s"></div>
            </div>
          </div>
        </div>
        <div class="absolute -bottom-4 -right-4 w-full h-full bg-secondary/5 rounded-[40px] -z-10 translate-x-4 translate-y-4 blur-xl"></div>
      </div>
    </div>

    <!-- Sisi kanan: Form Login -->
    <div class="lg:col-span-6 flex flex-col items-center lg:items-end w-full">
      <div class="login-card w-full max-w-[480px] glass-morphism p-6 sm:p-8 md:p-12 rounded-[32px] sm:rounded-[48px] border border-white/80 shadow-organic-xl relative">
        <div class="flex flex-col gap-6 md:gap-8">
          <div>
            <h2 class="font-display-lg text-[26px] sm:text-[32px] text-primary mb-2 tracking-tight">System Login</h2>
            <p class="font-body-md text-on-surface-variant/70 text-sm sm:text-base">Select your workspace and identify yourself.</p>
          </div>

          <?php if (!empty($error)): ?>
            <div class="flex items-center gap-2 bg-error-container text-on-error-container text-sm font-medium px-4 py-3 rounded-2xl border border-error/20">
              <span class="material-symbols-outlined text-[18px]">error</span>
              <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
          <?php endif; ?>

          <form class="space-y-5" method="POST" action="">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-secondary tracking-wider uppercase ml-1" for="username">Username</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary/40 group-focus-within:text-primary transition-colors">
                  <span class="material-symbols-outlined text-[20px]">alternate_email</span>
                </div>
                <input id="username" name="username" class="w-full bg-white/60 border border-outline-variant/40 rounded-2xl py-3.5 sm:py-4 pl-12 pr-4 focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary/50 transition-all placeholder:text-outline-variant/60" placeholder="clinic.admin" type="text" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-secondary tracking-wider uppercase ml-1" for="password">Password</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary/40 group-focus-within:text-primary transition-colors">
                  <span class="material-symbols-outlined text-[20px]">key</span>
                </div>
                <input id="password" name="password" class="w-full bg-white/60 border border-outline-variant/40 rounded-2xl py-3.5 sm:py-4 pl-12 pr-12 focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary/50 transition-all placeholder:text-outline-variant/60" placeholder="••••••••" type="password" required>
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-outline-variant hover:text-primary transition-colors">
                  <span class="material-symbols-outlined text-[20px]" id="toggleIcon">visibility</span>
                </button>
              </div>
            </div>

            <div class="flex items-center py-1">
              <label class="flex items-center gap-2.5 cursor-pointer group">
                <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20 cursor-pointer" type="checkbox" name="remember">
                <span class="text-[14px] text-on-surface-variant font-medium group-hover:text-primary transition-colors">Remember device</span>
              </label>
            </div>

            <button type="submit" class="w-full bg-primary text-white font-bold text-[16px] py-3.5 sm:py-4 rounded-2xl shadow-organic hover:shadow-organic-xl hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 mt-2 overflow-hidden relative group">
              <span class="relative z-10">Access Dashboard</span>
              <span class="material-symbols-outlined relative z-10 text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
              <div class="absolute inset-0 bg-secondary transition-transform translate-y-full group-hover:translate-y-0 duration-300"></div>
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</main>

<script>
  // Toggle visibility password
  const toggleBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('toggleIcon');
  toggleBtn.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    toggleIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
  });

  // Parallax lembut pada bentuk background (hanya aktif di layar besar agar hemat performa mobile)
  if (window.matchMedia('(min-width: 1024px)').matches) {
    document.addEventListener('mousemove', (e) => {
      const shapes = document.querySelectorAll('.floating-shape');
      const x = (e.clientX - window.innerWidth / 2) / 50;
      const y = (e.clientY - window.innerHeight / 2) / 50;
      shapes.forEach((shape, index) => {
        const multiplier = (index + 1) * 0.5;
        shape.style.transform = `translate(${x * multiplier}px, ${y * multiplier}px)`;
      });
    });
  }
</script>
</body>
</html>
