<div align="center">

<h1>🏫 MySPP — School Management System</h1>

<p>Platform administrasi sekolah berbasis web dengan manajemen pembayaran SPP terintegrasi, dirancang untuk sekolah-sekolah di Indonesia</p>

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-FFC107?style=for-the-badge&logo=filament&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

<br/>

[🚀 Live Demo](#) · [🐛 Report Bug](../../issues) · [✨ Request Feature](../../issues)

<br/>

> **Portfolio Project** — Full-stack Laravel admin panel untuk manajemen SPP sekolah,  
> dilengkapi role-based access control, portal siswa, laporan keuangan, dan integrasi payment gateway Midtrans.

</div>

---

## 📋 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Default Accounts](#-default-accounts)
- [API Endpoints](#-api-endpoints)
- [Project Status](#-project-status)
- [Screenshots](#-screenshots)
- [License](#-license)

---

## 🎯 About

**MySPP** adalah sistem manajemen sekolah yang berfokus pada administrasi pembayaran SPP (Sumbangan Pembinaan Pendidikan). Menyediakan panel admin lengkap untuk mengelola siswa, kelas, tagihan, pembayaran, dan laporan keuangan — semuanya dalam satu platform.

Dibangun sebagai **portfolio project** untuk mendemonstrasikan pengembangan Laravel dunia nyata: clean architecture, role-based permissions, modul keuangan, integrasi payment gateway, email notifikasi otomatis, dan portal siswa berbasis Blade + Livewire.

**Cocok untuk:**

- Sekolah swasta (SMP, SMA, SMK)
- Pondok pesantren
- Yayasan pendidikan dan lembaga pelatihan

---

## ✨ Features

### 👨‍💼 Admin Panel (Filament v3)

| Feature                          | Status  |
| -------------------------------- | ------- |
| Manajemen Jurusan & Kelas        | ✅ Done |
| Manajemen Tahun Ajaran           | ✅ Done |
| Manajemen Data Siswa             | ✅ Done |
| Manajemen User & Role (Spatie)   | ✅ Done |
| Manajemen Pembayaran (Transaksi) | ✅ Done |
| Manajemen Invoice / Tagihan      | ✅ Done |
| Pencatatan Pengeluaran Sekolah   | ✅ Done |
| Laporan Keuangan + Grafik        | ✅ Done |
| Export Laporan ke Excel (.xlsx)  | ✅ Done |
| Pengaturan Aplikasi              | ✅ Done |
| Dashboard Widgets & Analytics    | ✅ Done |
| Dark Enterprise Theme            | ✅ Done |

### 🎓 Student Portal

| Feature                               | Status  |
| ------------------------------------- | ------- |
| Login siswa (Blade + role validation) | ✅ Done |
| Dashboard tagihan & ringkasan bayar   | ✅ Done |
| Bayar SPP via Midtrans Snap           | ✅ Done |
| Upload bukti pembayaran manual        | ✅ Done |
| Riwayat transaksi                     | ✅ Done |
| Edit profil & ganti password          | ✅ Done |
| Upload scan ijazah                    | ✅ Done |

### 💳 Payment Gateway (Midtrans)

| Feature                                  | Status  |
| ---------------------------------------- | ------- |
| Konfigurasi & service Midtrans           | ✅ Done |
| Generate Snap token                      | ✅ Done |
| Webhook handler + signature verification | ✅ Done |
| Auto-update status pembayaran            | ✅ Done |
| PaymentLog audit trail                   | ✅ Done |

### 📧 Email Notifikasi

| Feature                                                     | Status  |
| ----------------------------------------------------------- | ------- |
| Email tagihan baru ke siswa (`InvoiceCreatedMail`)          | ✅ Done |
| Email konfirmasi pembayaran berhasil (`PaymentSuccessMail`) | ✅ Done |
| Background queue (database driver)                          | ✅ Done |

---

## 🛠 Tech Stack

| Layer             | Technology                   |
| ----------------- | ---------------------------- |
| Backend Framework | Laravel 12                   |
| PHP               | 8.3                          |
| Database          | MySQL 8                      |
| Admin Panel       | Filament v3                  |
| Reactive UI       | Livewire v3                  |
| CSS Framework     | Tailwind CSS                 |
| Authentication    | Laravel Sanctum              |
| Role & Permission | Spatie Laravel Permission v6 |
| Payment Gateway   | Midtrans Snap                |
| File Storage      | Cloudinary                   |
| Email             | Resend (SMTP)                |
| Queue             | Database driver              |
| Excel Export      | PhpSpreadsheet               |

---

## 🗄 Database Schema

| Table            | Description                                    |
| ---------------- | ---------------------------------------------- |
| `users`          | Siswa dan admin (dibedakan by role)            |
| `students`       | Profil siswa yang terhubung ke user            |
| `departments`    | Jurusan/program + biaya SPP per semester       |
| `classrooms`     | Kelas yang terhubung ke jurusan & tahun ajaran |
| `academic_years` | Data tahun ajaran                              |
| `transactions`   | Catatan pembayaran SPP + status Midtrans       |
| `payment_logs`   | Audit trail webhook Midtrans                   |
| `invoices`       | Tagihan yang diterbitkan ke siswa              |
| `expenses`       | Pengeluaran operasional sekolah                |
| `settings`       | Konfigurasi aplikasi (key-value)               |
| `jobs`           | Antrian background job (email, dll)            |

### 🔐 Spatie Permission Tables

| Table                  | Description                                      |
| ---------------------- | ------------------------------------------------ |
| `roles`                | Super Admin, Admin, Operator, Bendahara, Student |
| `permissions`          | Granular action permissions                      |
| `model_has_roles`      | Pivot: users ↔ roles                             |
| `role_has_permissions` | Pivot: roles ↔ permissions                       |

---

## 🚀 Installation

### Requirements

- PHP >= 8.3
- Composer >= 2.x
- MySQL >= 8.0
- Node.js >= 20.x

### Steps

**1. Clone the repository**

```bash
git clone https://github.com/ranggautama47/myspp-school-management-system.git
cd myspp-school-management-system
```

**2. Install PHP & JS dependencies**

```bash
composer install
npm install && npm run build
```

**3. Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure database in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myspp
DB_USERNAME=root
DB_PASSWORD=
```

**5. Run migrations, queue table & seeders**

```bash
php artisan queue:table
php artisan migrate --seed
```

> `queue:table` diperlukan agar background job email bisa berjalan via database driver.

**6. Link storage & start server**

```bash
php artisan storage:link
php artisan serve
```

**7. Jalankan queue worker** _(di terminal terpisah)_

```bash
php artisan queue:work
```

🌐 **Akses:** `http://localhost:8000/admin`

---

## ⚙️ Configuration

**Midtrans (Payment Gateway)**

Daftar di [midtrans.com](https://midtrans.com) lalu isi di `.env`:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**Email (Resend / Mailtrap)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MySPP"
```

> Untuk development lokal, gunakan [Mailtrap](https://mailtrap.io): `MAIL_HOST=sandbox.smtp.mailtrap.io`

**File Storage (Cloudinary)**

```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

**Queue**

```env
QUEUE_CONNECTION=database
```

---

## 👤 Default Accounts

Setelah menjalankan `php artisan migrate --seed`:

| Role           | Email             | Password |
| -------------- | ----------------- | -------- |
| 👑 Super Admin | admin@myspp.com   | password |
| 🧑‍🎓 Student     | student@myspp.com | password |

🔗 **Admin Panel:** `http://localhost:8000/admin`  
🔗 **Student Portal:** `http://localhost:8000/login`

---

## 🔌 API Endpoints

**Base URL:** `http://localhost:8000/api`

> Semua endpoint _(kecuali auth)_ memerlukan header: `Authorization: Bearer {token}`

### 🔐 Auth

| Method | Endpoint             | Description            |
| ------ | -------------------- | ---------------------- |
| POST   | `/api/auth/login`    | Login user             |
| POST   | `/api/auth/register` | Register user          |
| POST   | `/api/auth/logout`   | Logout user            |
| GET    | `/api/auth/me`       | Get authenticated user |

### 🏢 Departments

| Method | Endpoint                | Role      |
| ------ | ----------------------- | --------- |
| GET    | `/api/departments`      | All users |
| GET    | `/api/departments/{id}` | All users |
| POST   | `/api/departments`      | **Admin** |
| PUT    | `/api/departments/{id}` | **Admin** |
| DELETE | `/api/departments/{id}` | **Admin** |

### 💰 Transactions

| Method | Endpoint                              | Role                                 |
| ------ | ------------------------------------- | ------------------------------------ |
| GET    | `/api/transactions`                   | Admin: semua, Student: milik sendiri |
| GET    | `/api/transactions/{id}`              | All users                            |
| POST   | `/api/transactions`                   | **Admin**                            |
| POST   | `/api/transactions/{id}/pay`          | **Student**                          |
| POST   | `/api/transactions/{id}/approve`      | **Admin**                            |
| POST   | `/api/transactions/{id}/upload-proof` | **Student**                          |

### 💳 Midtrans

| Method | Endpoint                   | Description                           |
| ------ | -------------------------- | ------------------------------------- |
| POST   | `/api/midtrans/snap-token` | Generate Snap token                   |
| POST   | `/api/midtrans/webhook`    | Terima notifikasi Midtrans _(public)_ |

### 📋 Role & Akses

| Role                    | Akses                                            |
| ----------------------- | ------------------------------------------------ |
| 👑 **Super Admin**      | Semua endpoint (CRUD penuh + settings)           |
| 🛠️ **Admin / Operator** | Manajemen akademik & keuangan                    |
| 💰 **Bendahara**        | Modul keuangan & laporan                         |
| 🧑‍🎓 **Student**          | Read only + pembayaran (`/pay`, `/upload-proof`) |

---

## 📍 Project Status

```
Phase 1 — Backend Foundation      ✅ Complete
Phase 2 — Filament Admin Panel    ✅ Complete
Phase 3 — Midtrans Integration    ✅ Complete
Phase 4 — Student Portal          ✅ Complete
Phase 5 — Deployment              📋 Planned
```

### 📊 Progress Detail

| Phase                              | Status | Komponen Utama                                                                                                                                                                                          |
| ---------------------------------- | ------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Phase 1** — Backend Foundation   | ✅     | Migrations, Eloquent models (relationships, scopes, observers), Enums, Services (Transaction, Midtrans, Report), Observer + Policy, Form Requests, REST API + Sanctum                                   |
| **Phase 2** — Filament Admin Panel | ✅     | Dark enterprise theme (Slate 950 + Emerald), Custom branding, Dashboard widgets, Academic Module, Finance Module (Payments, Invoices, Expenses, Report + Excel), System Module (Users, Roles, Settings) |
| **Phase 3** — Midtrans Integration | ✅     | MidtransService (createSnapToken, handleWebhook, verifySignature), Webhook + PaymentLog audit, Sandbox + ngrok testing, Full payment flow                                                               |
| **Phase 4** — Student Portal       | ✅     | Student login, Dashboard (tagihan + riwayat), Bayar SPP via Snap, Upload bukti, Riwayat transaksi, Edit profil, Email notifikasi otomatis (InvoiceCreated + PaymentSuccess)                             |
| **Phase 5** — Deployment           | 📋     | `.env.example`, ERD diagram di `/docs`, Deploy ke Railway + PlanetScale, Env vars production, Live demo URL, Tag v1.0.0                                                                                 |

---

## 📸 Screenshots & Application Preview

The MySPP system provides two distinct user flows: a comprehensive **Admin Dashboard** for school administrators and a personalized **Student Portal** for students.

### 🔑 Authentication Portals

| Portal            | Description                                                     | Preview                                        |
| :---------------- | :-------------------------------------------------------------- | :--------------------------------------------- |
| **Admin Login**   | Secure entry point for school administrators and IT staff.      | ![Admin Login](screenshot/Admin-Login.png)     |
| **Student Login** | Dedicated login interface for students to access their billing. | ![Student Login](screenshot/Student-Login.png) |

### 🖥️ Admin Panel Modules

| Module                  | Description                                                                   | Preview                                                    |
| :---------------------- | :---------------------------------------------------------------------------- | :--------------------------------------------------------- |
| **Admin Dashboard**     | High-level analytics of school revenues, active student metrics, and logs.    | ![Admin Dashboard](screenshot/Admin-Dashboard.png)         |
| **Finance Report**      | Detailed financial breakdown, charts, and downloadable reports.               | ![Finance Report](screenshot/Finance-Report.png)           |
| **Payments Management** | Core management system to issue invoices, verify payments, and manage quotas. | ![Payments Management](screenshot/Payments-Management.png) |

### 🎓 Student Portal Modules

| Feature / Screen      | Description                                                                 | Preview                                            |
| :-------------------- | :-------------------------------------------------------------------------- | :------------------------------------------------- |
| **Student Dashboard** | Main welcome screen showing outstanding balances and announcements.         | ![Student Portal](screenshot/Student-Portal.png)   |
| **Payment History**   | Complete ledger of student's past payments, receipt statuses, and invoices. | ![Payment History](screenshot/Payment-History.png) |
| **My Profile**        | Student identity verification page and account security settings.           | ![My Profile](screenshot/My-Profile.png)           |

## 🐛 Known Issues & Notes

- **GoPay di Midtrans sandbox** = QRIS (tidak ada simulator). Gunakan Bank Transfer VA + `simulator.sandbox.midtrans.com`
- **Ngrok URL berubah** setiap restart. Update webhook URL di dashboard Midtrans setiap kali ngrok di-restart

---

## 🛠 Dev Commands

```bash
php artisan serve                    # Start server
php artisan optimize:clear           # Clear all cache
php artisan migrate:fresh --seed     # Reset DB
php artisan queue:work               # Queue worker (email notifikasi)
npm run build                        # Build assets
ngrok http 8000                      # Expose untuk webhook Midtrans
```

---

## 👨‍💻 Developer

<div align="left">
  <strong>Rangga Utama</strong><br />
  <em>Full-stack Web Developer · Laravel & Filament Enthusiast</em>
</div>

---

## 📄 License

Distributed under the **MIT License**. See [LICENSE](LICENSE) for more information.
