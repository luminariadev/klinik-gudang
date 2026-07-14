# Vitalis Admin - Sistem Manajemen Inventaris Klinik

Vitalis Admin adalah Sistem Informasi Manajemen Inventaris yang dibangun khusus untuk mengelola stok barang (Obat, Alkes, Habis Pakai, dll) di **Klinik Pratama UIN Sunan Gunung Djati Bandung**. Aplikasi ini menggunakan arsitektur MVC (Model-View-Controller) murni dengan PHP Native (tanpa framework besar seperti Laravel) demi efisiensi dan kecepatan.

Aplikasi ini mendukung **Multi-Role (Tingkatan Hak Akses)** untuk memastikan manajemen stok yang aman dan terpusat:
- **Super Admin:** Memiliki akses global, bisa membuat data poli baru, manajemen akun pengguna, hingga mencetak laporan dari seluruh poli secara komprehensif.
- **Admin Poli:** Mengelola inventaris secara spesifik untuk polinya masing-masing. Dapat menambah (Master Barang), mengedit stok, menghapus barang, dan menarik laporan.
- **Perawat:** Hanya dapat melihat ketersediaan barang (Read-Only) dan melakukan pencatatan / pengambilan barang (Catat Penggunaan) sesuai kebutuhan operasional lapangan.

---

## 🌟 Fitur Utama
1. **Pessimistic Locking (Anti Race-Condition):** Transaksi pengambilan stok sangat aman. Walaupun ratusan perawat menekan tombol 'Ambil Barang' di milidetik yang sama, database tidak akan bocor/minus dan antrean stok tetap akurat.
2. **Role-Based Access Control (RBAC):** Middleware cerdas yang mencegah akses silang. Perawat tidak bisa meretas masuk ke tautan Admin Poli, dan Admin Poli tidak bisa mengakses tautan Super Admin.
3. **Pelaporan & Ekspor Data:** Mendukung pencetakan laporan penggunaan dan sisa inventaris ke format **PDF** dan **CSV/Excel**.
4. **Log Aktivitas:** Mencatat keterangan pengambilan barang secara rinci (Siapa, untuk apa, kapan).

---

## 📂 Struktur Folder (Directory Structure)

```text
stock_gudang/
│
├── config/              # File konfigurasi utama
│   └── database.example.php # (Template) Konfigurasi koneksi database PDO
│
├── database/            # Kumpulan struktur tabel dan file SQL
│   ├── schema_v2.sql           # Struktur dasar database
│   ├── update_be_terbaru.sql   # Patch update tabel terbaru (keterangan, activity_logs)
│   └── seed_dummy_data.sql     # File berisi data bohongan (dummy) untuk testing 
│
├── public/              # Semua aset yang dapat diakses langsung oleh browser
│   ├── assets/          # Gambar, logo (logo-uin.png)
│   ├── css/             # File stylesheet 
│   ├── js/              # Script interaksi UI
│   └── material-symbols/# Icon offline berbasis Material UI Google
│
├── src/                 # Inti Backend / Logic Program (Arsitektur MVC)
│   ├── Controllers/     # Menangani alur data tiap Role (ItemController, PerawatController, dll)
│   ├── Helpers/         # Fungsi bantuan (Auth checker, rendering View)
│   └── Middleware/      # Pengawal keamanan halaman (AuthMiddleware)
│
├── views/               # Tampilan UI HTML (Front-End)
│   ├── auth/            # Halaman Login
│   ├── layouts/         # Template dasar HTML (Header, Sidebar, Navbar)
│   └── pages/           # Pecahan halaman berdasarkan Role (super_admin, poli, perawat)
│
├── .gitignore           # Daftar file yang dikecualikan dari GitHub (seperti file database.php asli)
├── index.php            # Entry-point utama & Simple Router penentu rute URL (Backend)
└── migrate.php          # (Opsional) Script untuk memigrasikan database otomatis
```

---

## 🚀 Cara Menjalankan (Installation & Setup)

Berikut adalah langkah-langkah untuk menjalankan *project* ini di komputer lokal Anda (menggunakan XAMPP, Laragon, dsb).

### 1. Persiapan Database (MySQL / MariaDB)
1. Buka phpMyAdmin (biasanya di `http://localhost/phpmyadmin`).
2. Buat database baru bernama: `stock_gudang`.
3. Buka menu **Import** dan masukkan file SQL secara berurutan:
   - Pertama, import `database/schema_v2.sql`.
   - Kedua, import `database/update_be_terbaru.sql`.
   - Ketiga (Opsional untuk testing), import `database/seed_dummy_data.sql` agar Anda langsung memiliki data contoh.

### 2. Persiapan Konfigurasi Server
1. Clone / Download project ini lalu simpan ke folder *root* server Anda (contoh: di `C:\laragon\www\stock_gudang` atau `C:\xampp\htdocs\stock_gudang`).
2. Masuk ke folder `config/`.
3. Buat *copy* (duplikat) dari file `database.example.php` dan ubah namanya menjadi `database.php`.
4. Buka `database.php` menggunakan teks editor (VSCode), lalu sesuaikan kredensialnya jika diperlukan (Bawaan default biasanya User: `root` dan Password: *kosong*).

### 3. Akses Website
1. Nyalakan Apache dan MySQL Anda (di panel XAMPP/Laragon).
2. Buka Browser, lalu ketik URL:
   👉 `http://localhost/stock_gudang`

---

## 🔑 Akun Uji Coba (Dummy Accounts)
Jika Anda sudah melakukan Import `database/seed_dummy_data.sql`, gunakan akun berikut untuk mencoba aplikasinya:

| Role | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `super` | `123` | Akses global seluruh aplikasi. |
| **Admin Poli** | `admingigi` | `123` | Mengelola CRUD barang spesifik Poli Gigi. |
| **Admin Poli** | `adminumum` | `123` | Mengelola CRUD barang spesifik Poli Umum. |
| **Perawat** | `perawatgigi` | `123` | Pengguna harian (Read & Ambil Barang) Poli Gigi. |
| **Perawat** | `perawatumum` | `123` | Pengguna harian (Read & Ambil Barang) Poli Umum. |

---

*Dikembangkan untuk Klinik Pratama UIN Sunan Gunung Djati Bandung.*