# 🧠 PROJECT CONTEXT — MySPP School Management System

> **File ini adalah blueprint untuk AI agent (Kilo, Claude, Cursor, dll).**
> Baca dan ikuti seluruh aturan di bawah sebelum menulis kode apapun.
> Jangan membuat asumsi di luar konteks ini.

---

## 1. IDENTITAS PROJECT

```
Nama project   : MySPP — School Management System
Tipe           : Full-stack Web Application (Monolith + REST API)
Bahasa         : Indonesia (komentar kode boleh Inggris)
Status         : Aktif dikerjakan (solo developer)
Tujuan         : Portofolio profesional + siap pakai untuk sekolah/yayasan
```

---

## 2. TECH STACK — WAJIB DIIKUTI

> ⚠️ Jangan sarankan library/framework di luar daftar ini kecuali diminta.

### Backend

| Komponen          | Pilihan                                             | Versi          |
| ----------------- | --------------------------------------------------- | -------------- |
| Framework         | Laravel                                             | **12.x**       |
| PHP               | PHP                                                 | **>= 8.3**     |
| Database          | MySQL                                               | 8.0            |
| ORM               | Eloquent                                            | bawaan Laravel |
| Auth API          | Laravel Sanctum                                     | bawaan Laravel |
| Role & Permission | Spatie Laravel Permission                           | v6             |
| Queue             | Laravel Queue (database driver)                     | bawaan         |
| Storage           | Cloudinary (via cloudinary-labs/cloudinary-laravel) | -              |
| Email             | Resend (via SMTP)                                   | -              |
| PDF               | barryvdh/laravel-dompdf                             | -              |
| Excel             | maatwebsite/excel                                   | v3             |

### Admin Panel

| Komponen    | Pilihan      | Versi  |
| ----------- | ------------ | ------ |
| Admin panel | Filament     | **v3** |
| Reactive UI | Livewire     | v3     |
| JS ringan   | Alpine.js    | v3     |
| CSS         | Tailwind CSS | v3     |

### Payment Gateway

| Komponen | Pilihan                                 |
| -------- | --------------------------------------- |
| Provider | **Midtrans** (bukan Xendit, bukan Doku) |
| Metode   | Midtrans Snap                           |
| Package  | midtrans/midtrans-php                   |
| Mode     | Sandbox dulu, lalu production           |

### Deployment (gratis)

| Komponen     | Pilihan                        |
| ------------ | ------------------------------ |
| App hosting  | Railway                        |
| Database     | PlanetScale (MySQL-compatible) |
| File storage | Cloudinary                     |
| CI/CD        | GitHub Actions                 |

---

## 3. STRUKTUR DATABASE

### Tabel & Relasi

```
users                — Satu tabel untuk admin dan siswa, dibedakan via role
roles                — Spatie: 'admin' dan 'student'
permissions          — Spatie: hak akses granular per role
model_has_roles      — Pivot Spatie: users ↔ roles
role_has_permissions — Pivot Spatie: roles ↔ permissions
departments          — Jurusan/kelas (name, semester, cost)
transactions         — Tagihan SPP per siswa per department
payment_logs         — Log setiap response webhook Midtrans
```

### Kolom Penting di `transactions`

```
id, code, user_id (FK), department_id (FK),
payment_method, payment_status (pending|paid|rejected),
snap_token, midtrans_url, proof_of_payment,
paid_at, created_at, updated_at
```

### Aturan Database

- Selalu gunakan **soft deletes** untuk tabel `users`, `departments`, `transactions`
- Gunakan **unsigned bigInteger** untuk semua FK
- Format `code` transaksi: `TRX-{YYYYMMDD}-{5 digit random}` contoh: `TRX-20250511-A7K2M`
- `payment_status` ENUM: `pending`, `paid`, `failed`, `expired`, `cancelled`

---

## 4. ROLES & PERMISSION

### Role: `admin`

Bisa akses:

- Semua halaman Filament admin panel
- CRUD departments, users, transactions
- Approve/reject pembayaran
- Export laporan
- Kelola role & permission user lain

### Role: `student`

Bisa akses:

- Portal siswa (bukan admin panel)
- Lihat tagihan milik sendiri saja
- Bayar SPP via Midtrans
- Upload bukti bayar
- Edit biodata sendiri

### Aturan Permission

```php
// Admin permissions
'view-dashboard', 'manage-departments', 'manage-users',
'manage-transactions', 'approve-payment', 'export-reports'

// Student permissions
'view-own-transactions', 'make-payment', 'upload-proof', 'edit-profile'
```

---

## 5. STRUKTUR FOLDER (wajib ikuti)

```
app/
├── Filament/
│   ├── Resources/           ← Filament CRUD resources
│   │   ├── DepartmentResource.php
│   │   ├── TransactionResource.php
│   │   └── UserResource.php
│   ├── Pages/               ← Custom Filament pages
│   └── Widgets/             ← Dashboard widgets
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/             ← Semua API controller di sini
│   │   │   ├── AuthController.php
│   │   │   ├── TransactionController.php
│   │   │   └── MidtransController.php
│   │   └── Student/         ← Controller portal siswa
│   ├── Middleware/
│   └── Requests/            ← Form Request untuk semua input
│
├── Models/
│   ├── User.php
│   ├── Department.php
│   ├── Transaction.php
│   └── PaymentLog.php
│
├── Services/                ← Business logic, BUKAN di controller
│   ├── TransactionService.php
│   ├── MidtransService.php
│   └── ReportService.php
│
├── Policies/                ← Authorization policies
│   ├── TransactionPolicy.php
│   └── UserPolicy.php
│
└── Observers/               ← Model observers jika dibutuhkan
    └── TransactionObserver.php

resources/
├── views/
│   ├── student/             ← View portal siswa
│   └── emails/              ← Email templates
│
routes/
├── api.php                  ← Semua API route
├── web.php                  ← Route portal siswa & auth
└── filament.php (auto)      ← Filament handle sendiri
```

---

## 6. KONVENSI KODE

### Naming Convention

```
Model          : PascalCase singular       → Transaction, Department
Controller     : PascalCase + Controller   → TransactionController
Service        : PascalCase + Service      → MidtransService
Migration      : snake_case               → create_transactions_table
Route name     : kebab-case               → student.transactions.index
Blade view     : kebab-case               → payment-success.blade.php
Variable       : camelCase                → $unpaidTransactions
```

### Aturan Controller

- Controller **hanya boleh** memanggil Service, tidak boleh ada business logic
- Semua validasi wajib pakai **Form Request** (`php artisan make:request`)
- Response API wajib pakai format standar:

```php
// Success
return response()->json([
    'success' => true,
    'message' => 'Transaksi berhasil dibuat',
    'data'    => $transaction,
], 201);

// Error
return response()->json([
    'success' => false,
    'message' => 'Transaksi tidak ditemukan',
    'errors'  => $validator->errors(),
], 404);
```

### Aturan Service

- Semua business logic ada di `app/Services/`
- Service boleh memanggil model langsung (tidak wajib repository pattern)
- Gunakan `try-catch` di setiap method Service yang berhubungan dengan external API

```php
// Contoh struktur Service
class MidtransService
{
    public function createSnapToken(Transaction $transaction): array
    {
        try {
            // logic here
        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
            throw $e;
        }
    }
}
```

---

## 7. ALUR PEMBAYARAN SPP (Midtrans)

```
Siswa klik "Bayar"
    ↓
TransactionController@pay
    ↓
MidtransService@createSnapToken
    → Kirim ke Midtrans API
    → Dapat snap_token
    → Simpan ke transactions.snap_token
    ↓
Return snap_token ke frontend
    ↓
Frontend buka Midtrans Snap popup
    ↓
Siswa bayar (QRIS / GoPay / Transfer)
    ↓
Midtrans kirim webhook ke /api/midtrans/webhook
    ↓
MidtransController@webhook
    → Verifikasi signature key
    → Update transactions.payment_status
    → Simpan ke payment_logs
    → Kirim email konfirmasi (via Queue)
    ↓
Selesai — status terupdate real-time
```

---

## 8. ATURAN FILAMENT ADMIN

- Semua Resource Filament ada di `app/Filament/Resources/`
- Gunakan **Filament Actions** untuk approve/reject pembayaran (bukan halaman baru)
- Dashboard wajib punya widget: total pemasukan, jumlah siswa, transaksi pending
- Gunakan **Filament Notifications** untuk feedback aksi admin
- Tabel list wajib ada **filter** dan **search**

---

## 9. API RULES

- Prefix semua API: `/api/v1/`
- Auth: Bearer Token via Sanctum
- Rate limit: 60 request/menit per user
- Semua endpoint dilindungi middleware `auth:sanctum` kecuali:
    - `POST /api/v1/auth/login`
    - `POST /api/v1/auth/register`
    - `POST /api/v1/midtrans/webhook`

---

## 10. LARANGAN (jangan lakukan ini)

```
❌ Jangan taruh business logic di Controller
❌ Jangan gunakan raw query SQL kecuali terpaksa (pakai Eloquent)
❌ Jangan hardcode credential / API key di kode
❌ Jangan buat halaman admin baru tanpa Filament
❌ Jangan ubah struktur tabel tanpa buat migration baru
❌ Jangan sarankan Vue.js / React / Next.js — project ini Blade + Livewire
❌ Jangan gunakan jQuery
❌ Jangan commit file .env
```

---

## 11. CHECKLIST SEBELUM GENERATE KODE

Sebelum AI menulis kode, pastikan:

- [ ] Apakah ini untuk admin panel? → Gunakan Filament
- [ ] Apakah ini API? → Taruh di `Api/` controller, gunakan Form Request
- [ ] Apakah ada business logic? → Taruh di Service
- [ ] Apakah butuh akses database baru? → Buat migration
- [ ] Apakah ada role check? → Gunakan Policy atau `$this->authorize()`

---

## 12. ENVIRONMENT VARIABLE YANG ADA

```env
# App
APP_NAME=MySPP
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Storage
CLOUDINARY_URL=

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com

# Queue
QUEUE_CONNECTION=database
```

---

_File ini diperbarui setiap ada perubahan arsitektur besar._
_Versi: 1.0 — Mei 2025_
