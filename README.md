<div align="center">

<h1>🏫 MySPP — School Management System</h1>

<p>Platform manajemen sekolah berbasis web, lengkap dengan sistem pembayaran SPP terintegrasi Midtrans</p>

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-FFC107?style=for-the-badge&logo=filament&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Midtrans](https://img.shields.io/badge/Midtrans-Payment-003D7A?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

<br/>

[🚀 Live Demo](#) · [📖 Dokumentasi](#) · [🐛 Report Bug](issues) · [✨ Request Feature](issues)

<br/>

> **Portfolio Project** — Full-stack Web App untuk manajemen sekolah/yayasan  
> mencakup CRUD admin panel, role management, hingga payment gateway terintegrasi.

</div>

---

## 📋 Daftar Isi

- [Tentang Project](#-tentang-project)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [ERD Database](#-erd-database)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [API Endpoints](#-api-endpoints)
- [Screenshots](#-screenshots)
- [Roadmap](#-roadmap)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Project

**MySPP** adalah aplikasi web School Management System yang dirancang untuk memudahkan pengelolaan administrasi sekolah, khususnya pembayaran SPP. Dibangun menggunakan **Laravel 12** dengan admin panel **Filament v3** dan integrasi payment gateway **Midtrans**.

### Target Pengguna

- 🏫 Sekolah swasta (SMP, SMA, SMK)
- 🕌 Pesantren & yayasan pendidikan
- 🎓 Lembaga kursus & pelatihan

### Highlights

- ✅ CMS Admin lengkap berbasis Filament v3
- ✅ Multi-role: Admin & Siswa via Spatie Permission
- ✅ Payment gateway Midtrans (QRIS, GoPay, OVO, Transfer Bank)
- ✅ REST API siap pakai
- ✅ Export laporan PDF & Excel
- ✅ Notifikasi email otomatis

---

## ✨ Fitur Utama

### 👨‍💼 CMS Admin Panel

| Fitur                          | Status              |
| ------------------------------ | ------------------- |
| Manajemen Jurusan/Kelas (CRUD) | ✅ Selesai          |
| Manajemen Data Siswa & Admin   | ✅ Selesai          |
| Buat & kelola tagihan SPP      | ✅ Selesai          |
| Approve / reject pembayaran    | ✅ Selesai          |
| Dashboard analytics & grafik   | ✅ Selesai          |
| Laporan keuangan per periode   | 🔄 Dalam pengerjaan |
| Export PDF & Excel             | 🔄 Dalam pengerjaan |
| Role & Permission management   | ✅ Selesai          |

### 🎓 Portal Siswa

| Fitur                          | Status              |
| ------------------------------ | ------------------- |
| Register & lengkapi biodata    | ✅ Selesai          |
| Upload scan ijazah & foto      | ✅ Selesai          |
| Dashboard status tagihan       | ✅ Selesai          |
| Bayar SPP via Midtrans Snap    | 🔄 Dalam pengerjaan |
| Upload bukti pembayaran manual | ✅ Selesai          |
| Riwayat transaksi              | ✅ Selesai          |
| Notifikasi status pembayaran   | 📋 Direncanakan     |

### 💳 Payment Gateway (Midtrans)

| Metode             | Status              |
| ------------------ | ------------------- |
| QRIS               | 🔄 Dalam pengerjaan |
| GoPay / OVO        | 🔄 Dalam pengerjaan |
| Transfer Bank (VA) | 🔄 Dalam pengerjaan |
| Kartu Kredit       | 📋 Direncanakan     |
| Webhook otomatis   | 🔄 Dalam pengerjaan |

---

## 🛠 Tech Stack

### Backend

```
Laravel 12          — PHP Framework
PHP 8.3             — Bahasa pemrograman
MySQL 8.0           — Database
Laravel Sanctum     — API Authentication
Spatie Permission   — Role & Permission Management
```

### Admin Panel

```
Filament v3         — Admin panel framework
Livewire            — Reactive UI components
Alpine.js           — Lightweight JS interactions
Tailwind CSS        — Utility-first CSS
```

### Payment & Integrasi

```
Midtrans Snap       — Payment gateway (sandbox & production)
DomPDF              — Generate PDF kwitansi & laporan
Laravel Excel       — Export data ke Excel
Laravel Queue       — Background jobs (notifikasi, email)
Resend / Mailtrap   — Email notification service
```

### DevOps & Deployment

```
Railway / Render    — App hosting (gratis)
PlanetScale         — MySQL cloud database (gratis)
Cloudinary          — File & image storage (gratis)
GitHub Actions      — CI/CD pipeline
```

---

## 🗄 ERD Database

```
users               — Data siswa dan admin (dibedakan via role)
roles               — Admin, Student (Spatie)
permissions         — Hak akses per role
model_has_roles     — Pivot: users ↔ roles
role_has_permissions— Pivot: roles ↔ permissions
departments         — Jurusan/kelas + biaya SPP per semester
transactions        — Tagihan SPP + status pembayaran Midtrans
payment_logs        — Log webhook Midtrans untuk audit trail
```

> Lihat diagram lengkap di [/docs/erd.png](docs/erd.png)

---

## 🚀 Instalasi

### Requirements

- PHP >= 8.3
- Composer >= 2.x
- MySQL >= 8.0 atau PlanetScale
- Node.js >= 20.x

### Langkah Instalasi

**1. Clone repository**

```bash
git clone https://github.com/username/myspp.git
cd myspp
```

**2. Install dependencies**

```bash
composer install
npm install && npm run build
```

**3. Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi database di `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myspp
DB_USERNAME=root
DB_PASSWORD=
```

**5. Jalankan migration & seeder**

```bash
php artisan migrate --seed
```

**6. Storage link & jalankan server**

```bash
php artisan storage:link
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

---

## ⚙️ Konfigurasi

### Midtrans (Payment Gateway)

Daftar di [midtrans.com](https://midtrans.com) lalu isi di `.env`:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### Email Notifikasi (Resend)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MySPP"
```

### Cloudinary (File Storage)

```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

---

## 📖 Penggunaan

### Default Akun Seeder

| Role    | Email             | Password |
| ------- | ----------------- | -------- |
| Admin   | admin@myspp.com   | password |
| Student | student@myspp.com | password |

### Akses Admin Panel

```
http://localhost:8000/admin
```

### Akses Portal Siswa

```
http://localhost:8000/
```

---

## 🔌 API Endpoints

Base URL: `http://localhost:8000/api`

### Auth

```http
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/logout
GET    /api/auth/me
```

### Departments

```http
GET    /api/departments
GET    /api/departments/{id}
POST   /api/departments          [Admin]
PUT    /api/departments/{id}     [Admin]
DELETE /api/departments/{id}     [Admin]
```

### Transactions

```http
GET    /api/transactions                  [Admin: semua | Student: milik sendiri]
GET    /api/transactions/{id}
POST   /api/transactions                  [Admin]
POST   /api/transactions/{id}/pay        [Student]
POST   /api/transactions/{id}/approve    [Admin]
```

### Midtrans

```http
POST   /api/midtrans/snap-token          — Generate Snap token
POST   /api/midtrans/webhook             — Terima notifikasi Midtrans
```

> Semua endpoint (kecuali auth) membutuhkan header:  
> `Authorization: Bearer {token}`

---

## 📸 Screenshots

| Admin Dashboard | Manajemen Transaksi |
| :-------------: | :-----------------: |
| _[screenshot]_  |   _[screenshot]_    |

|  Portal Siswa  | Pembayaran Midtrans |
| :------------: | :-----------------: |
| _[screenshot]_ |   _[screenshot]_    |

---

## 📍 Roadmap

- [x] Setup Laravel 12 + Filament v3
- [x] CRUD Departments, Users, Transactions
- [x] Spatie Role & Permission (Admin & Student)
- [x] Portal siswa (register, biodata, dashboard)
- [ ] Integrasi Midtrans Snap + Webhook
- [ ] Notifikasi email otomatis
- [ ] Export PDF kwitansi & laporan Excel
- [ ] REST API lengkap + dokumentasi Postman
- [ ] Feature test (PHPUnit)
- [ ] Fitur diskon / beasiswa / cicilan
- [ ] Multi tahun ajaran

---

## 🤝 Kontribusi

Pull request sangat diterima! Untuk perubahan besar, buka issue terlebih dahulu.

```bash
# Fork repo ini
# Buat branch baru
git checkout -b feature/nama-fitur

# Commit perubahan
git commit -m "feat: tambah fitur xyz"

# Push ke branch
git push origin feature/nama-fitur

# Buat Pull Request
```

---

## 👨‍💻 Developer

**ranggautama**  
Full-stack Web Developer · Laravel Enthusiast

[![GitHub](https://img.shields.io/badge/GitHub-@ranggautama47-181717?style=flat&logo=github)](https://github.com/ranggautama47)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-ranggautama-0077B5?style=flat&logo=linkedin)](https://www.linkedin.com/in/rangga-utama-6bb76b362/)
[![Portfolio](https://img.shields.io/badge/Portfolio-webkamu.com-FF5722?style=flat)](https://webkamu.com)

---

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">
  <p>⭐ Jika project ini bermanfaat, jangan lupa kasih star ya!</p>
  <p>Made with ❤️ in Indonesia</p>
</div>
