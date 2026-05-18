<div align="center">

<h1>🏫 MySPP — School Management System</h1>

<p>A web-based school administration platform with integrated SPP payment management built for Indonesian schools</p>

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-FFC107?style=for-the-badge&logo=filament&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

<br/>

[🚀 Live Demo](#) · [🐛 Report Bug](../../issues) · [✨ Request Feature](../../issues)

<br/>

> **Portfolio Project** — A full-stack Laravel admin panel for school SPP (tuition) management,  
> featuring role-based access control, financial reporting, and Midtrans payment gateway integration.

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

**MySPP** is a school management system focused on student SPP (tuition fee) administration. It provides a complete admin panel for managing students, classrooms, payments, and financial reports — all in one place.

Built as a **portfolio project** to demonstrate real-world Laravel development: clean architecture, role-based permissions, financial module, and payment gateway integration.

**Designed for:**

- Private schools (SMP, SMA, SMK)
- Islamic boarding schools (pesantren)
- Educational foundations and training centers

---

## ✨ Features

### 👨‍💼 Admin Panel

| Feature                           | Status  |
| --------------------------------- | ------- |
| Department & Classroom Management | ✅ Done |
| Academic Year Management          | ✅ Done |
| Student Data Management           | ✅ Done |
| User & Role Management (Spatie)   | ✅ Done |
| Payment (Transaction) Management  | ✅ Done |
| Invoice Management                | ✅ Done |
| Expense Tracking                  | ✅ Done |
| Finance Report with Charts        | ✅ Done |
| Export Report to Excel (.xlsx)    | ✅ Done |
| Application Settings              | ✅ Done |
| Dashboard Widgets & Analytics     | ✅ Done |
| Dark Enterprise Theme             | ✅ Done |

### 🎓 Student Portal

| Feature                     | Status       |
| --------------------------- | ------------ |
| Student login via API       | ✅ Done      |
| View tuition bill status    | 🔄 Phase 4   |
| Pay SPP via Midtrans Snap   | 🔄 Phase 3–4 |
| Upload manual payment proof | 🔄 Phase 4   |
| Transaction history         | 🔄 Phase 4   |

### 💳 Payment Gateway

| Feature                    | Status             |
| -------------------------- | ------------------ |
| Midtrans service & config  | ✅ Structure ready |
| Snap token generation      | 🔄 Phase 3         |
| Webhook handler            | 🔄 Phase 3         |
| Payment status auto-update | 🔄 Phase 3         |

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

```
users               ← Students and admins (differentiated by role)
students            ← Student profile linked to user
departments         ← Major/program + SPP cost per semester
classrooms          ← Class linked to department & academic year
academic_years      ← Academic year records
transactions        ← SPP payment records + Midtrans status
payment_logs        ← Midtrans webhook audit trail
invoices            ← Billing issued to students
expenses            ← School operational expenses
settings            ← Application-wide configuration (key-value)

── Spatie Permission Tables ──
roles               ← Super Admin, Admin, Operator, Bendahara, Student
permissions         ← Granular action permissions
model_has_roles     ← Pivot: users ↔ roles
role_has_permissions← Pivot: roles ↔ permissions
```

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

**5. Run migrations & seeders**

```bash
php artisan migrate --seed
```

**6. Link storage & start server**

```bash
php artisan storage:link
php artisan serve
```

Visit `http://localhost:8000/admin`

---

## ⚙️ Configuration

### Midtrans (Payment Gateway)

Register at [midtrans.com](https://midtrans.com) and fill in `.env`:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

### Email (Resend)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MySPP"
```

### File Storage (Cloudinary)

```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

---

## 👤 Default Accounts

After running `php artisan migrate --seed`:

| Role        | Email             | Password |
| ----------- | ----------------- | -------- |
| Super Admin | admin@myspp.com   | password |
| Student     | student@myspp.com | password |

**Admin Panel:** `http://localhost:8000/admin`

---

## 🔌 API Endpoints

Base URL: `http://localhost:8000/api`

All endpoints (except auth) require header:

```
Authorization: Bearer {token}
```

### Auth

```http
POST  /api/auth/login
POST  /api/auth/register
POST  /api/auth/logout
GET   /api/auth/me
```

### Departments

```http
GET   /api/departments
GET   /api/departments/{id}
POST  /api/departments          [Admin]
PUT   /api/departments/{id}     [Admin]
DELETE /api/departments/{id}    [Admin]
```

### Transactions

```http
GET   /api/transactions         [Admin: all | Student: own]
GET   /api/transactions/{id}
POST  /api/transactions         [Admin]
POST  /api/transactions/{id}/pay      [Student]
POST  /api/transactions/{id}/approve  [Admin]
```

### Midtrans

```http
POST  /api/midtrans/snap-token  — Generate Snap token
POST  /api/midtrans/webhook     — Receive Midtrans notification
```

---

## 📍 Project Status

```
Phase 1 — Backend Foundation     ✅ Complete
Phase 2 — Filament Admin Panel   ✅ Complete
Phase 3 — Midtrans Integration   🔄 In Progress
Phase 4 — Student Portal         📋 Planned
Phase 5 — Deployment             📋 Planned
```

### Phase 1 — Backend Foundation ✅

- [x] Database migrations (users, departments, transactions, payment_logs, students, classrooms, academic_years, invoices, expenses, settings)
- [x] Eloquent models with relationships, scopes, and observers
- [x] TransactionStatus, InvoiceStatus, ExpenseCategory, UserRole enums
- [x] TransactionService, MidtransService, ReportService
- [x] TransactionObserver + TransactionPolicy
- [x] Form Requests (StoreTransaction, UploadProof, UpdateProfile, Login)
- [x] REST API routes with Sanctum auth

### Phase 2 — Filament Admin Panel ✅

- [x] Dark enterprise theme (Slate 950 + Emerald primary)
- [x] Custom brand logo and topbar profile
- [x] Dashboard: StatsOverview, PaymentTrends, PaymentOverview, RecentTransactions widgets
- [x] Academic module: Departments, Classrooms, Academic Years, Students
- [x] Finance module: Payments, Invoices, Expenses, Finance Report (charts + Excel export)
- [x] System module: Users, Roles (Spatie), Application Settings
- [x] Role-based navigation: Super Admin, Admin, Operator, Bendahara, Student

### Phase 3 — Midtrans Integration 🔄

- [x] MidtransService structure (createSnapToken, handleWebhook, verifySignature)
- [x] Webhook route and PaymentLog audit trail
- [ ] Install `midtrans/midtrans-php` package
- [ ] Test Snap token in sandbox mode
- [ ] Test webhook with ngrok
- [ ] Full payment flow: trigger → webhook → status update → PaymentLog

### Phase 4 — Student Portal 📋

- [ ] Student login page (Blade + Livewire)
- [ ] Dashboard: bill status + payment history
- [ ] Pay SPP via Midtrans Snap popup
- [ ] Upload manual payment proof
- [ ] Transaction history view

### Phase 5 — Deployment 📋

- [ ] Complete `.env.example`
- [ ] ERD diagram in `/docs`
- [ ] Deploy to Railway + PlanetScale
- [ ] Set production environment variables
- [ ] Live demo URL

---

## 📸 Screenshots

> Screenshots will be added after Phase 3 completion.

| Admin Dashboard | Finance Report |
| :-------------: | :------------: |
|  _coming soon_  | _coming soon_  |

| Payments Management | Student Management |
| :-----------------: | :----------------: |
|    _coming soon_    |   _coming soon_    |

---

## 👨‍💻 Developer

**Rangga Utama**
Full-stack Web Developer · Laravel & Filament Enthusiast

[![GitHub](https://img.shields.io/badge/GitHub-@ranggautama47-181717?style=flat&logo=github)](https://github.com/ranggautama47)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-rangga--utama-0077B5?style=flat&logo=linkedin)](https://www.linkedin.com/in/rangga-utama-6bb76b362/)

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">
  <p>⭐ If you find this project useful, consider giving it a star!</p>
  <p>Made with ❤️ in Indonesia</p>
</div>
