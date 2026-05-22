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
| Pay SPP via Midtrans Snap   | 🔄 Phase 4   |
| Upload manual payment proof | 🔄 Phase 4   |
| Transaction history         | 🔄 Phase 4   |

### 💳 Payment Gateway

| Feature                    | Status             |
| -------------------------- | ------------------ |
| Midtrans service & config  | ✅ Done            |
| Snap token generation      | ✅ Done            |
| Webhook handler            | ✅ Done            |
| Payment status auto-update | ✅ Done            |

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

# 🗄 Database Schema — MySPP

| Table | Description |
|---|---|
| users | Students and admins (differentiated by role) |
| students | Student profile linked to user |
| departments | Major/program + SPP cost per semester |
| classrooms | Class linked to department & academic year |
| academic_years | Academic year records |
| transactions | SPP payment records + Midtrans status |
| payment_logs | Midtrans webhook audit trail |
| invoices | Billing issued to students |
| expenses | School operational expenses |
| settings | Application-wide configuration (key-value) |

---

# 🔐 Spatie Permission Tables

| Table | Description |
|---|---|
| roles | Super Admin, Admin, Operator, Bendahara, Student |
| permissions | Granular action permissions |
| model_has_roles | Pivot: users ↔ roles |
| role_has_permissions | Pivot: roles ↔ permissions |
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
git clone [https://github.com/ranggautama47/myspp-school-management-system.git](https://github.com/ranggautama47/myspp-school-management-system.git)
cd myspp-school-management-system
```
**2. Install PHP & JS dependencies**
``` Bash composer install
npm install && npm run build
```
**3. Setup environment** 
```Bash
cp .env.example .env
php artisan key:generate
```
**4. Configure database in <span style="background-color: #2d2d2d; color: #ffffff; padding: 2px 6px; border-radius: 4px;">.env</span>**

### Cuplikan kode :

<pre style="background-color: #2d2d2d; color: #f8f8f2; padding: 10px; border-radius: 5px;">
<code style="color: #66d9ef;">DB_CONNECTION</code>=<span style="color: #a6e22e;">mysql</span>
<code style="color: #66d9ef;">DB_HOST</code>=<span style="color: #a6e22e;">127.0.0.1</span>
<code style="color: #66d9ef;">DB_PORT</code>=<span style="color: #e6db74;">3306</span>
<code style="color: #66d9ef;">DB_DATABASE</code>=<span style="color: #a6e22e;">myspp</span>
<code style="color: #66d9ef;">DB_USERNAME</code>=<span style="color: #a6e22e;">root</span>
<code style="color: #66d9ef;">DB_PASSWORD</code>=<span style="color: #a6e22e;"></span>
</code>
</pre>

**5. Run migrations & seeders**
```Bash
php artisan migrate --seed
```

**6. 🔗 Link storage & start server**
```Bash
php artisan storage:link
php artisan serve
```
<strong style="color: #4caf50;">🌐 Access:</strong>

👉 <code>http://localhost:8000/admin</code>

## ⚙️ Configuration
**Midtrans (Payment Gateway)**
**Register at midtrans.com and fill in <span style="background-color: #2d2d2d; color: #ffffff; padding: 2px 6px; border-radius: 4px;">.env :</span>**
```Cuplikan kode
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```
## Email (Resend)
```Cuplikan kode
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MySPP"
```
## File Storage (Cloudinary)
```Cuplikan kode
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```
## 👤 Default Accounts
**After running <span style="background-color: #2d2d2d; color: #e21212; padding: 2px 6px; border-radius: 4px;">php artisan migrate --seed :</span>**

| Role | Email | Password |
|------|-------|----------|
| 👑 Super Admin | admin@myspp.com | password |
| 🧑‍🎓 Student | student@myspp.com | password |

🔗 **Admin Panel:** [http://localhost:8000/admin](http://localhost:8000/admin)

## 🔌 API Endpoints

**Base:** <span style="background-color: #2d2d2d; color: #ffffff; padding: 2px 6px; border-radius: 4px;">[php artisan migrate --seed :](http://localhost:8000/api)</span> 

📌**All endpoints** <span style="background-color: #2d2d2d; color: #55cdfc; padding: 2px 6px; border-radius: 4px;">(except auth)</span> require header:
`Authorization: Bearer {token}`

---
### 🔐 Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login user |
| POST | `/api/auth/register` | Register user |
| POST | `/api/auth/logout` | Logout user |
| GET | `/api/auth/me` | Get authenticated user |
---

### 🏢 Departments
| Method | Endpoint | Role |
|--------|----------|------|
| GET | `/api/departments` | All users |
| GET | `/api/departments/{id}` | All users |
| POST | `/api/departments` | **Admin** |
| PUT | `/api/departments/{id}` | **Admin** |
| DELETE | `/api/departments/{id}` | **Admin** |
---

### 💰 Transactions

| Method | Endpoint | Role |
|--------|----------|------|
| GET | `/api/transactions` | Admin: all, Student: own |
| GET | `/api/transactions/{id}` | All users |
| POST | `/api/transactions` | **Admin** |
| POST | `/api/transactions/{id}/pay` | **Student** |
| POST | `/api/transactions/{id}/approve` | **Admin** |

---
### 💳 Midtrans

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/midtrans/snap-token` | Generate Snap token |
| POST | `/api/midtrans/webhook` | Receive Midtrans notification 

---

## 📋 Ringkasan Role & Akses
| Role | Akses |
|------|-------|
| 👑 **Super Admin** | Semua endpoint (create, read, update, delete) |
| 🧑‍🎓 **Student** | Read only, dan melakukan pembayaran (`/pay`) |
---

## 📍 Project Status
```
Phase 1 — Backend Foundation     ✅ Complete
Phase 2 — Filament Admin Panel   ✅ Complete
Phase 3 — Midtrans Integration   ✅ Complete
Phase 4 — Student Portal         🔄 In Progress
Phase 5 — Deployment             📋 Planned
```
# 📊 Project Progress Report

| Phase | Status | Components |
|-------|--------|------------|
| **Phase 1** — Backend Foundation | ✅ | Database migrations, Eloquent models (relationships, scopes, observers), Enums (TransactionStatus, InvoiceStatus, ExpenseCategory, UserRole), Services (Transaction, Midtrans, Report), TransactionObserver + Policy, Form Requests, REST API + Sanctum |
| **Phase 2** — Filament Admin Panel | ✅ | Dark enterprise theme (Slate 950 + Emerald), Custom branding, Dashboard widgets (StatsOverview, PaymentTrends, PaymentOverview, RecentTransactions), Academic Module (Departments, Classrooms, Academic Years, Students), Finance Module (Payments, Invoices, Expenses, Report + Excel), System Module (Users, Roles, Settings), Role-based navigation |
| **Phase 3** — Midtrans Integration | ✅ | MidtransService (createSnapToken, handleWebhook, verifySignature), Webhook + PaymentLog audit, midtrans/midtrans-php package, Sandbox + ngrok testing, Full payment flow |
| **Phase 4** — Student Portal | 🔄 | Student login (Blade + Livewire), Dashboard (bill + history), Pay SPP (Midtrans Snap), Upload proof, Transaction history |
| **Phase 5** — Deployment | ⬜ | .env.example, ERD diagram in /docs, Deploy to Railway + PlanetScale, Production env vars, Live demo URL |

---

## 📸 Screenshots

> ⏳ *Screenshots will be added after Phase 4 completion.*

| Module | Preview |
|--------|---------|
| Admin Dashboard | ![Dashboard](screenshot/Admin%20Dashboard.png) |
| Finance Report | ![Finance Report](screenshot/Finance-Report.png) |
| Payments Management | ![Payments Management](screenshot/Payments-Management.png) |
| Student Management | ![Coming Soon](https://via.placeholder.com/400x200?text=Student+Management+Coming+Soon) |

---

## 👨‍💻 Developer

<div align="left">
  <strong>Rangga Utama</strong><br />
  <em>Full-stack Web Developer · Laravel & Filament Enthusiast</em>
</div>

---

## 📄 License

<div align="left">
  Distributed under the <strong>MIT License</strong>.<br />
  
See full license [here](LICENSE). for more information.
</div>
