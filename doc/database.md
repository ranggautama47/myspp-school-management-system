# 🗄️ Database Documentation — MySPP

Dokumen ini menjelaskan struktur data, relasi, dan keputusan teknis yang diambil dalam merancang database MySPP. Desain ini fokus pada integritas data keuangan dan performa query.

---

## 1. Overview Desain Database

Database MySPP dirancang menggunakan prinsip Relational Database Management System (RDBMS) yang ternormalisasi untuk memastikan tidak ada redundansi data, terutama pada modul transaksi pembayaran.

### Prinsip Utama:

- **Data Integrity:** Menggunakan Foreign Key constraints di level database.
- **Precision:** Menggunakan tipe data `decimal` untuk semua nilai mata uang.
- **Auditability:** Mencatat setiap respon webhook dari payment gateway.

---

## 2. Tabel Utama (Core Tables)

| Tabel          | Fungsi                                                    | Detail Penting                                                                                          |
| -------------- | --------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| `users`        | Menyimpan data otentikasi admin dan siswa.                | Menggunakan SoftDeletes.                                                                                |
| `departments`  | Data jurusan atau kategori kelas (misal: IPA, IPS).       | Digunakan untuk mengelompokkan tarif SPP.                                                               |
| `transactions` | Jantung aplikasi. Mencatat setiap tagihan dan pembayaran. | Menggunakan indexed transaction code unik untuk keamanan transaksi dan mempermudah tracking pembayaran. |
| `payment_logs` | Log histori interaksi dengan Midtrans (Webhook/API).      | Menggunakan kolom JSON.                                                                                 |

---

## 3. Relasi Antar Tabel

Relasi dibangun secara eksplisit untuk menjaga referential integrity:

- **Users ↔ Transactions (1:M):** Satu user (siswa) dapat memiliki banyak histori transaksi SPP.
- **Departments ↔ Transactions (1:M):** Transaksi dikaitkan ke departemen untuk mempermudah pelaporan keuangan per jurusan.
- **Transactions ↔ Payment Logs (1:M):** Satu transaksi bisa memiliki banyak log (misal: log saat pending, log saat success, dan log saat expired).

---

## 4. Keputusan Desain & Best Practices

### 🔄 SoftDeletes

Kami menerapkan SoftDeletes pada tabel `users` dan `departments`.

**Alasan:** Data keuangan (transaksi) sangat bergantung pada histori user. Jika user dihapus secara permanen (hard delete), histori laporan keuangan akan rusak. Dengan SoftDeletes, data tetap ada di database tetapi tidak muncul di aplikasi.

### 🔢 Enum untuk Transaction Status

Status transaksi (`PENDING`, `SUCCESS`, `FAILED`, `EXPIRED`) Status transaksi menggunakan PHP Native Enum untuk menjaga konsistensi data dan menghindari hardcoded string di level aplikasi.

**Alasan:** Memastikan konsistensi data di level database sehingga tidak ada status "gaib" yang masuk selain nilai yang ditentukan.

### 📄 JSON Column (Payment Logs)

Respon mentah (raw response) dari Midtrans disimpan dalam kolom bertipe JSON di tabel `payment_logs`.

**Alasan:** Struktur respon API payment gateway bisa berubah atau sangat detail. Menyimpannya dalam bentuk JSON memudahkan proses debugging jika terjadi perselisihan data pembayaran tanpa harus membongkar struktur tabel.

### 💰 Decimal for Currency

Semua nominal uang menggunakan tipe `decimal(12,2)`.

**Alasan:** Menghindari masalah presisi angka pecahan yang sering terjadi jika menggunakan tipe `float` atau `double`.

---

## 5. Strategi Indexing & Performa

Untuk menjaga aplikasi tetap cepat saat data bertambah besar, kami menerapkan:

- **Primary Key Indexing:** Semua tabel menggunakan ID (BigInt/UUID) sebagai primary index.
- **Composite Index:** Pada tabel `transactions`, kami melakukan indexing pada kombinasi `user_id` dan `status`.
- **Foreign Key Index:** Laravel secara otomatis mengindeks kolom foreign key untuk mempercepat proses join antar tabel.
- **Balanced ID Strategy:** Sebagian tabel masih menggunakan auto increment BigInt untuk menjaga performa query dan kesederhanaan development pada skala aplikasi saat ini.

---

## 6. Keamanan & Scalability Sederhana

- **Foreign Key Constraints:** Kami menggunakan `ON DELETE RESTRICT` pada transaksi untuk mencegah penghapusan data master (user/dept) yang sudah memiliki riwayat bayar.
- **Database Scaling:** Struktur ini siap untuk didistribusikan menggunakan Database Read-Replicas jika beban baca laporan keuangan meningkat di masa depan.
- **Standard Laravel Migrations:** Memungkinkan team collaboration dan version control pada skema database secara konsisten.
