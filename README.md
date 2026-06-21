# 📦 Sistem Manajemen Inventory - PT Sinar Abadi

Sistem Aplikasi Manajemen Inventaris barang berbasis web untuk **PT Sinar Abadi** yang dirancang dengan antarmuka modern, premium, dan dinamis menggunakan AdminLTE 4, Bootstrap 5, dan integrasi SweetAlert2.

---

## 🏢 Profil Perusahaan

**PT Sinar Abadi** adalah perusahaan distributor perlengkapan, aksesoris, dan komponen komputer berkualitas yang berdiri sejak tahun 2018. Perusahaan berkomitmen menyediakan produk teknologi terpercaya, orisinal, dan bergaransi resmi untuk memenuhi kebutuhan retail, kantor, sekolah, maupun workstation profesional. Didukung oleh tim yang berpengalaman, kami menawarkan berbagai pilihan peripheral komputer seperti mouse, keyboard, monitor, RAM, SSD, dan komponen hardware lainnya dari brand-brand global ternama dengan proses distribusi yang efisien, transparan, dan terintegrasi.

### 👁️ Visi
> “Menjadi distributor perlengkapan komputer terpercaya dan terdepan di Indonesia dengan menyediakan produk berkualitas tinggi serta jaringan distribusi yang luas dan responsif.”

### 🎯 Misi
1. **Produk Berkualitas & Orisinal**: Menyediakan berbagai perlengkapan dan komponen komputer orisinal, bergaransi resmi, dan berkualitas tinggi.
2. **Jaringan Distribusi Efisien**: Menjamin ketersediaan stok dan kelancaran rantai pasokan produk ke seluruh wilayah operasional mitra bisnis.
3. **Layanan Profesional**: Memberikan pelayanan purnajual (after-sales) yang cepat, ramah, dan solutif.
4. **Kemitraan Jangka Panjang**: Membangun sinergi yang saling menguntungkan dengan para reseller, dealer, dan mitra korporat.
5. **Inovasi Berkelanjutan**: Terus mengikuti tren teknologi terbaru dan mengembangkan sistem manajemen inventory secara digital dan modern.

---

## 🎨 Logo Perusahaan & Tampilan Aplikasi

### 🔖 Logo Perusahaan
<p align="center">
  <img width="250" alt="PT Sinar Abadi Logo" src="https://github.com/user-attachments/assets/0544a88f-1bbe-4971-8232-bd3ed58592ba" style="border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.06);" />
</p>

### 📸 Galeri Antarmuka Website

#### 🔐 Halaman Login
<img width="100%" alt="Halaman Login" src="https://github.com/user-attachments/assets/50007b3a-d388-4480-921a-f20be6e0fdcf" />
*Gambar 1.1: Halaman Login (Tampilan Premium dengan Background Blur & Glassmorphism)*

#### 📝 Halaman Registrasi
<img width="100%" alt="Halaman Registrasi" src="https://github.com/user-attachments/assets/f8005040-211a-4e7d-b389-dae0330b8412" />
*Gambar 1.2: Halaman Registrasi (Pendaftaran Akun Pengguna)*

#### 💻 Dasbor Admin & Pengaturan
<img width="100%" alt="Dasbor Admin" src="https://github.com/user-attachments/assets/1c96147e-640c-4a50-a779-d141ff480d12" />
<img width="100%" alt="Pengaturan Aplikasi" src="https://github.com/user-attachments/assets/97737a66-55ef-46bd-9845-029466b596ee" />
*Gambar 1.3: Tampilan Halaman Admin & Menu Pengaturan Aplikasi*

#### 👤 Halaman Profil
<p align="center">
  <img width="1598" height="784" alt="profil" src="https://github.com/user-attachments/assets/75a328f3-e541-41b8-a896-b219c3fef39e" />
</p>
*Gambar 1.4: Detail Profil Pengguna*

#### 👥 Dasbor Pengguna (User)
<img width="100%" alt="Dasbor Pengguna" src="https://github.com/user-attachments/assets/3bc63035-a7b2-4169-8a63-8e4403b96d43" />
*Gambar 1.5: Tampilan Halaman Pengguna Biasa (Akses Data Inventaris & Laporan)*

#### ➕ Tambah & Edit Data
<img width="100%" alt="Tambah & Edit Data" src="https://github.com/user-attachments/assets/4929cd67-afbf-400b-b3c2-3ad2269a2e8d" />
*Gambar 1.6: Halaman Tambah & Edit Master Data Barang*

---

## 🛠️ Fitur-Fitur Utama & Pembaruan Sistem

1. **Dashboard Inventory Terkonsolidasi**: Seluruh file administrasi telah dipindahkan ke direktori utama (root level) demi performa loading halaman yang lebih optimal dan struktur file yang bersih.
2. **Desain Cardview Premium**: Seluruh tabel database menggunakan kontainer card modern (`border-radius: 12px`, `overflow: hidden`, dan `shadow-sm border-0`) dengan header warna primary (`bg-primary`).
3. **Integrasi SweetAlert2**: Operasi penghapusan data dan notifikasi aksi sukses/gagal kini menggunakan SweetAlert2 dengan tombol interaktif bergaya modern.
4. **Desain Responsif & Tema Adaptif**:
   - Layout responsif yang mendeteksi resolusi layar, mencegah pemotongan form login/registrasi.
   - Komponen footer dinamis yang mendeteksi mode malam (`dark theme`) secara native.
5. **Manajemen Master Data Terintegrasi**: Pengelolaan data stok barang, vendor supplier, status produk, penyimpanan gudang, log distribusi barang masuk/keluar, dan akun pengguna.

---

## 💻 Tech Stack yang Digunakan

- **Core**: PHP 8.x, HTML5, Javascript (ES6+)
- **Styling**: Vanilla CSS, Bootstrap 5.3 (Tema Light/Dark Mode)
- **Admin Template**: AdminLTE v4.0.0-rc7
- **Database**: MySQL / MariaDB
- **Libraries**: SweetAlert2, OverlayScrollbars 2.x, Bootstrap Icons

---

## ⚙️ Cara Instalasi & Konfigurasi

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/adisoekamthi-cloud/pratikum_inventory.git
   ```
2. **Pindahkan ke Server Lokal**:
   Pindahkan folder `pratikum_inventory` ke direktori root server lokal Anda (misal `C:/xampp/htdocs/`).
3. **Import Database**:
   - Aktifkan MySQL di XAMPP Panel.
   - Buka `http://localhost/phpmyadmin/`.
   - Buat database baru bernama `pratikum`.
   - Import file SQL yang tersedia: `pratikum.sql`.
4. **Konfigurasi Koneksi**:
   Sesuaikan detail akun database Anda pada file `koneksi.php`.
5. **Jalankan Aplikasi**:
   Akses di browser melalui URL: `http://localhost/pratikum_inventory/login.php`.

---

## 🔗 Tautan Penting

- **Repositori GitHub**: [adisoekamthi-cloud/pratikum_inventory](https://github.com/adisoekamthi-cloud/pratikum_inventory)
- **Link Presentasi**: [Tonton Video Demo YouTube/TikTok](https://github.com/adisoekamthi-cloud/pratikum_inventory)
