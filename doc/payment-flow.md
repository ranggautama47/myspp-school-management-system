# 💳 Payment Gateway Integration — MySPP

Dokumen ini menjelaskan arsitektur dan alur kerja integrasi pembayaran menggunakan **Midtrans Snap** pada sistem manajemen sekolah MySPP. Dokumentasi ini bertujuan untuk memberikan gambaran teknis bagi pengembang mengenai bagaimana transaksi diproses, diverifikasi, hingga dicatat ke dalam sistem secara otomatis.

---

## 1. Overview Alur Pembayaran

Sistem pembayaran di MySPP dirancang untuk meminimalkan intervensi manual Admin. Kami menggunakan pola **Asynchronous Notification** (Webhook) untuk memastikan status transaksi tetap akurat meskipun user menutup browser saat proses pembayaran berlangsung.

### Alur Kerja Utama:

1. **Bill Creation:** Admin men-generate tagihan untuk siswa (status awal: `pending`).
2. **Checkout:** Siswa memilih tagihan di dashboard dan menekan tombol "Bayar Sekarang".
3. **Token Generation:** Server Laravel mengirimkan detail transaksi ke API Midtrans untuk mendapatkan `snap_token`.
4. **Payment Interface:** Midtrans Snap Popup muncul di dashboard siswa tanpa _refresh_ halaman.
5. **Payment Process:** Siswa menyelesaikan pembayaran melalui metode yang dipilih (VA, E-Wallet, dll).
6. **Notification:** Midtrans mengirimkan data JSON (Webhook) ke endpoint `/api/midtrans/callback`.
7. **Finalization:** Server memvalidasi _signature_, memperbarui tabel `transactions`, mencatat `payment_logs`, dan mengirim notifikasi keberhasilan.

---

## 2. Diagram Alur (Sequence Diagram)

```text
Siswa (Browser)          Server Laravel (MySPP)          Midtrans API
      |                         |                             |
      |--- 1. Klik Bayar ------>|                             |
      |                         |--- 2. Request Snap Token -->|
      |                         |                             |
      |                         |<-- 3. Snap Token -----------|
      |<-- 4. Tampil Popup -----|                             |
      |                         |                             |
      |--- 5. Bayar (VA/QRIS) ->|                             |
      |                         |                             |
      |                         |<-- 6. HTTP Notification ----|
      |                         |       (Webhook Callback)    |
      |                         |                             |
      |                         |--- 7. Validasi Signature -->|
      |                         |                             |
      |<-- 8. Notif Berhasil ---|--- 9. Update DB Status ---->|
```

## 3. Integrasi Midtrans Snap

Kami menggunakan Midtrans Snap untuk memberikan pengalaman pengguna yang mulus (Seamless UI).

    Server-Side: Controller memanggil PaymentService untuk mengirimkan order_id unik dan gross_amount.

    Client-Side: Frontend menerima snap_token dan menggunakan library snap.js untuk memicu popup.

    Fallback: Jika siswa menutup popup sebelum membayar, tombol bayar akan tetap tersedia di menu "Riwayat Transaksi" selama status masih pending dan token belum kedaluwarsa.

## 4. Penanganan Webhook (Callback)

Webhook adalah bagian krusial yang menangani perubahan status secara real-time di latar belakang tanpa bergantung pada interaksi user di frontend.
Pemetaan Status (Enum Mapping)
Status Midtrans Status MySPP Deskripsi
settlement paid Pembayaran berhasil dan dana telah diamankan.
capture paid Khusus kartu kredit yang berhasil diautorisasi.
pending pending Menunggu pembayaran dari siswa.
expire expired Batas waktu pembayaran habis (transaksi hangus).
cancel / deny cancelled Transaksi dibatalkan secara manual atau ditolak sistem.

## 5. Audit Trail & Payment Logs

    Setiap respon JSON yang diterima dari Midtrans disimpan secara utuh ke dalam tabel payment_logs.

    Tujuan: Memudahkan debugging dan audit jika terjadi perselisihan status pembayaran.

    Relasi: Tabel ini menggunakan hubungan One-to-Many terhadap transactions, mencatat setiap fase perubahan status (misal: histori dari pending menuju paid).

    Data Integrity: Menggunakan tipe data JSON pada MySQL 8 agar data respon Midtrans tetap terstruktur dan mudah di-query.

## 6. Keamanan Webhook (Security)

Untuk mencegah serangan spoofing (pihak luar yang berpura-pura menjadi Midtrans), kami menerapkan Signature Key Validation:

    Aplikasi menerima payload JSON dari Midtrans.

    Sistem men-generate hash SHA512 secara mandiri menggunakan kombinasi: order_id + status_code + gross_amount + Server Key.

    Membandingkan hash buatan sistem dengan signature_key yang dikirimkan Midtrans dalam header.

    Proses update database hanya dilakukan jika kedua kunci tersebut identik.

## 7. Retry Handling & Queue

Untuk menjaga performa dan reliabilitas aplikasi, MySPP menggunakan Laravel Queue:

    Notifications: Pengiriman email atau notifikasi sistem setelah bayar dilakukan di background agar tidak membebani proses callback.

    Idempotency: Sistem melakukan pengecekan status transaksi terlebih dahulu sebelum melakukan update. Ini mencegah terjadinya duplikasi data jika Midtrans mengirimkan notifikasi Webhook yang sama berkali-kali.

## 8. Mengapa Menggunakan Midtrans?

    Cakupan Luas: Mendukung bank lokal (VA), QRIS, hingga gerai retail (Indomaret/Alfamart).

    Sandbox Mode: Menyediakan lingkungan simulasi yang sangat membantu selama tahap pengembangan tanpa menggunakan uang sungguhan.

    Keamanan Tinggi: Sudah tersertifikasi PCI-DSS, menjamin keamanan data transaksi siswa.

## 9. Future Scalability

Arsitektur pembayaran ini dirancang untuk pengembangan di masa depan:

    Recurring Billing: Automasi pembuatan tagihan SPP bulanan yang dikirim langsung ke email siswa.

    Financial Reports: Export laporan keuangan otomatis yang ditarik langsung dari data payment_logs.

    Split Payment: Kemampuan untuk membagi dana pembayaran ke beberapa rekening yayasan yang berbeda secara otomatis.

---

Last Updated: 12 Mei 2026
