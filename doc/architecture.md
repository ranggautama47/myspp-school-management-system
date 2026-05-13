# 🏗️ Architecture Documentation — MySPP

Dokumen ini menjelaskan struktur arsitektur dan prinsip pengembangan yang digunakan dalam membangun **MySPP**.

Fokus utama arsitektur ini adalah menciptakan aplikasi yang:

- maintainable
- readable
- scalable
- mudah dikembangkan
- tetap realistis untuk solo developer maupun tim kecil

MySPP menggunakan pendekatan **Laravel Monolith Architecture** dengan pola **MVC + Service Layer**.

Pendekatan ini dipilih karena:

- lebih cepat dikembangkan
- lebih mudah dipelihara
- cocok untuk startup, UMKM, sekolah, dan freelance project
- tetap mengikuti best practice Laravel modern

---

# 1. Architecture Overview

MySPP menggunakan pola:

```text
Request
   ↓
Controller
   ↓
Service
   ↓
Model / Database
   ↓
Response
```

Controller hanya bertanggung jawab untuk:

- menerima request
- memanggil service
- mengembalikan response

Seluruh business logic dipindahkan ke dalam Service Layer agar controller tetap clean dan maintainable.

---

# 2. Core Architecture Principles

## MVC + Service Layer

Laravel MVC digunakan sebagai fondasi utama aplikasi.

Namun seluruh business logic penting dipindahkan ke folder `Services/` untuk menghindari:

- fat controller
- duplicated logic
- logic bercampur dengan HTTP layer

---

## Skinny Controller Pattern

Controller dibuat sesederhana mungkin.

Controller tidak menangani:

- payment logic
- Midtrans integration
- complex query
- transaction database process

Controller hanya menjadi penghubung antara Request dan Service.

---

## Business Logic in Services

Seluruh proses utama aplikasi dipusatkan di Service Layer.

Contoh:

- `TransactionService`
- `MidtransService`
- `ReportService`

Keuntungan:

- reusable
- lebih mudah di-test
- lebih mudah dikembangkan
- siap digunakan untuk API maupun Web

---

## Enum-Based Status Management

Status aplikasi menggunakan PHP Native Enum agar:

- type-safe
- menghindari typo string
- mudah di-maintain
- lebih clean dibanding hardcoded string

Contoh:

- `TransactionStatus`
- `PaymentMethod`
- `UserRole`

---

## Observer Pattern

Observer digunakan untuk menangani automation process di level model.

Contoh:

- otomatis mencatat payment log
- otomatis generate kode transaksi
- otomatis update status tertentu

Tujuan utama observer adalah menjaga service dan controller tetap clean.

---

## Policy-Based Authorization

Authorization menggunakan kombinasi:

- Laravel Policy
- Spatie Permission

Policy digunakan untuk authorization yang bersifat dinamis.

Contoh:

- siswa hanya bisa melihat transaksi miliknya sendiri
- admin dapat approve pembayaran

---

# 3. Folder Structure

```text
app/
├── Enums/
├── Filament/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Notifications/
├── Observers/
├── Policies/
├── Services/
└── Providers/
```

---

# 4. Folder Responsibilities

| Folder              | Responsibility                                                                                                                                                                 |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `Enums/`            | Menyimpan enum seperti status transaksi dan role agar lebih type-safe dan menghindari hardcoded string.                                                                        |
| `Filament/`         | Konfigurasi admin panel berbasis Filament v3 untuk mempercepat pengembangan dashboard internal tanpa membangun CRUD dari nol seperti Resources, Pages, Widgets, dan Dashboard. |
| `Http/Controllers/` | Menerima request dan mengembalikan response tanpa menyimpan business logic kompleks.                                                                                           |
| `Http/Requests/`    | Menyimpan validasi request agar controller tetap bersih dan terstruktur.                                                                                                       |
| `Http/Middleware/`  | Menangani filtering request seperti authentication, authorization, dan security layer.                                                                                         |
| `Models/`           | (Eloquent ORM models) Representasi tabel database beserta relationship dan query scopes.                                                                                       |
| `Notifications/`    | Mengelola email dan notifikasi sistem seperti status pembayaran dan invoice.                                                                                                   |
| `Observers/`        | Menangani otomatisasi event model seperti logging dan generate kode transaksi.                                                                                                 |
| `Policies/`         | Mengatur authorization berbasis user ownership dan role permission.                                                                                                            |
| `Services/`         | Menyimpan business logic utama aplikasi seperti payment processing dan Midtrans integration.                                                                                   |
| `Providers/`        | Registrasi service container, observer, policy, dan konfigurasi aplikasi lainnya.                                                                                              |

---

# 5. Service Layer Architecture

Service Layer menjadi pusat business logic aplikasi.

Contoh struktur service:

```text
Services/
├── DepartmentService.php
├── MidtransService.php
├── PaymentLogService.php
├── ReportService.php
├── StudentService.php
└── TransactionService.php
```

Contoh tanggung jawab service:

| Service              | Responsibility                        |
| -------------------- | ------------------------------------- |
| `TransactionService` | Membuat dan mengelola transaksi       |
| `MidtransService`    | Integrasi payment gateway Midtrans    |
| `PaymentLogService`  | Menyimpan webhook dan payment history |
| `ReportService`      | Dashboard analytics dan laporan       |
| `StudentService`     | Biodata dan profile siswa             |

---

# 6. Scalability Approach

Meskipun MySPP menggunakan arsitektur monolith, struktur aplikasi sudah disiapkan untuk scalable development.

## Queue Ready

Service Layer dibuat agar mudah dipindahkan ke Queue Job jika dibutuhkan.

Contoh:

- send email
- export PDF
- webhook processing

---

## API Ready

Karena business logic dipisahkan ke Service Layer, API dan Web dapat menggunakan logic yang sama tanpa duplikasi kode.

---

## Maintainable Codebase

Pemisahan responsibility membantu project tetap mudah dipelihara meskipun fitur bertambah besar.

---

# 7. Development Philosophy

MySPP menggunakan pendekatan:

> “Pragmatic Laravel Architecture”

> "Arsitektur MySPP menerapkan beberapa prinsip SOLID, terutama Single Responsibility Principle, melalui pemisahan business logic, validation, dan authorization ke layer yang terpisah."

Artinya:

- tidak over-engineering
- tidak terlalu banyak abstraction
- tetap mengikuti best practice modern Laravel
- fokus pada maintainability dan scalability realistis

Arsitektur ini dipilih agar project:

- mudah diselesaikan
- cocok untuk portfolio
- cocok untuk freelance
- tetap terlihat profesional saat technical interview

---

# 8. Technology Stack

| Technology        | Purpose              |
| ----------------- | -------------------- |
| Laravel 12        | Backend Framework    |
| PHP 8.3           | Programming Language |
| Filament v3       | Admin Panel          |
| MySQL 8           | Database             |
| Spatie Permission | Role & Permission    |
| Laravel Sanctum   | API Authentication   |
| Midtrans          | Payment Gateway      |
| Tailwind CSS      | UI Styling           |

---

# 9. Conclusion

MySPP dirancang sebagai modern Laravel application dengan fokus pada:

- clean architecture
- reusable business logic
- maintainable structure
- realistic scalability
- modern Laravel best practices

Meskipun tidak menggunakan microservices atau clean architecture kompleks, struktur ini sudah sangat cukup untuk kebutuhan production application modern dan portfolio professional Laravel developer.

MySPP tetap menggunakan pendekatan Laravel Monolith Architecture karena:

- lebih sederhana untuk dikelola
- lebih cepat dikembangkan
- lebih mudah di-deploy
- lebih cocok untuk solo developer dan tim kecil
- tetap mampu menangani kebutuhan production skala kecil hingga menengah

Pendekatan ini dipilih secara pragmatis untuk menjaga keseimbangan antara scalability, maintainability, dan development speed tanpa menambahkan kompleksitas yang belum diperlukan.
