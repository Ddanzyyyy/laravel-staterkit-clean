# Product Requirements Document (PRD)
## Web Admin — Aplikasi Booking & Reservasi Hotel

| | |
|---|---|
| **Nama Produk** | Admin Dashboard – Hotel Booking & Reservation System |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 28 Juli 2026 |
| **Status** | Draft |

---

## 1. Latar Belakang

Bisnis perhotelan membutuhkan sistem terpusat untuk mengelola operasional reservasi kamar dan tiket secara efisien. Saat ini proses pengaturan jadwal kamar, persetujuan reservasi, dan penentuan harga musiman sering dilakukan secara manual (spreadsheet, WhatsApp, atau pencatatan kertas), yang menyebabkan:

- Risiko *double booking* kamar.
- Keterlambatan approval reservasi tiket/booking.
- Harga kamar tidak fleksibel terhadap musim (high season/low season/event).
- Sulitnya monitoring okupansi dan pendapatan secara real-time.

Web Admin ini dibangun untuk memberikan kontrol penuh kepada tim operasional/hotel terhadap seluruh siklus reservasi — dari ketersediaan kamar, approval booking, hingga strategi pricing.

---

## 2. Tujuan Produk (Goals)

1. Menyediakan panel admin terpusat untuk mengelola jadwal & ketersediaan kamar hotel.
2. Mempercepat proses approval/rejection reservasi tiket/booking tamu.
3. Memungkinkan admin menentukan dan mengatur harga musiman (seasonal pricing) secara fleksibel.
4. Memberikan visibilitas data melalui dashboard analitik (okupansi, pendapatan, tren booking).
5. Mengurangi kesalahan manual dan meningkatkan efisiensi operasional tim hotel.

### Non-Goals (di luar cakupan versi ini)
- Aplikasi mobile untuk tamu (customer-facing app) — hanya sisi admin.
- Payment gateway integration penuh (hanya status pembayaran dicatat, bukan proses transaksi).
- Multi-property/multi-hotel chain management (asumsi: 1 hotel/properti per instance, dapat diperluas di roadmap berikutnya).

---

## 3. Target Pengguna & Role

| Role | Deskripsi | Hak Akses Utama |
|---|---|---|
| **Super Admin** | Owner/manajemen hotel | Akses penuh ke semua modul, termasuk pengaturan sistem & user management |
| **Admin Operasional** | Staf front office/reservasi | Kelola jadwal kamar, approve/reject reservasi, lihat laporan |
| **Finance/Revenue Manager** | Staf keuangan/revenue | Kelola harga musiman, lihat laporan pendapatan |
| **Staff (View Only)** | Staf pendukung | Hanya bisa melihat jadwal & status reservasi, tanpa edit |

---

## 4. User Stories Utama

1. Sebagai **admin operasional**, saya ingin melihat kalender ketersediaan kamar agar saya dapat mencegah *double booking*.
2. Sebagai **admin operasional**, saya ingin menyetujui atau menolak reservasi yang masuk beserta alasannya, agar tamu mendapat kepastian status.
3. Sebagai **revenue manager**, saya ingin mengatur harga kamar berdasarkan periode musiman (misal: Lebaran, Natal, akhir pekan), agar pendapatan dapat dioptimalkan.
4. Sebagai **super admin**, saya ingin melihat dashboard ringkasan okupansi dan pendapatan harian/bulanan, agar saya dapat mengambil keputusan bisnis dengan cepat.
5. Sebagai **admin**, saya ingin menerima notifikasi ketika ada reservasi baru masuk, agar dapat segera ditindaklanjuti.
6. Sebagai **admin**, saya ingin mengelola data tipe kamar dan fasilitasnya, agar informasi ke tamu selalu akurat.

---

## 5. Ruang Lingkup Fitur (Scope)

### 5.1 Manajemen Jadwal & Ketersediaan Kamar
- Kalender ketersediaan kamar (per tipe kamar, per tanggal).
- Blokir kamar manual (maintenance, renovasi, alasan internal).
- Deteksi konflik jadwal otomatis (anti double-booking).
- Manajemen tipe kamar (Standard, Deluxe, Suite, dll) beserta jumlah unit, kapasitas, dan fasilitas.

### 5.2 Manajemen Reservasi/Tiket Booking
- Daftar reservasi masuk (status: Pending, Approved, Rejected, Cancelled, Checked-in, Checked-out).
- Detail reservasi (data tamu, tanggal check-in/out, tipe kamar, jumlah tamu, catatan khusus).
- Aksi approve/reject dengan catatan alasan.
- Riwayat & log perubahan status reservasi.
- Filter & pencarian reservasi (by tanggal, status, nama tamu, kode booking).
- Notifikasi reservasi baru (in-app, opsional email).

### 5.3 Manajemen Harga Musiman (Seasonal Pricing)
- Buat aturan harga berdasarkan rentang tanggal (musim/event tertentu).
- Set harga per tipe kamar per periode.
- Prioritas aturan harga jika ada tumpang tindih periode.
- Preview simulasi harga sebelum publish.
- Riwayat perubahan harga.

### 5.4 Dashboard & Laporan
- Ringkasan okupansi (harian/mingguan/bulanan).
- Ringkasan pendapatan (revenue) per periode.
- Grafik tren booking & pembatalan.
- Kamar paling laris / tipe kamar dengan okupansi tertinggi.
- Ekspor laporan (Excel/PDF).

### 5.5 Manajemen Pengguna & Akses
- CRUD user admin dengan role-based access control (RBAC).
- Log aktivitas admin (audit trail).

### 5.6 Pengaturan Sistem
- Data master hotel (nama, alamat, kontak, kebijakan check-in/out).
- Pengaturan notifikasi.
- Pengaturan umum (bahasa, mata uang, zona waktu).

---

## 6. Daftar Menu Utama Dashboard Admin

```
📊 Dashboard (Beranda)
   ├─ Ringkasan Okupansi Hari Ini
   ├─ Ringkasan Pendapatan
   ├─ Grafik Tren Booking
   └─ Reservasi Menunggu Approval (Quick Access)

🛏️ Manajemen Kamar
   ├─ Daftar Tipe Kamar
   ├─ Kalender Ketersediaan Kamar
   ├─ Blokir/Buka Kamar (Maintenance)
   └─ Fasilitas & Detail Kamar

📅 Reservasi & Booking
   ├─ Daftar Reservasi (All Status)
   ├─ Reservasi Pending (Approval)
   ├─ Reservasi Disetujui
   ├─ Reservasi Ditolak/Dibatalkan
   ├─ Detail & Riwayat Reservasi
   └─ Check-in / Check-out Management

💰 Manajemen Harga
   ├─ Harga Reguler (Default)
   ├─ Harga Musiman (Seasonal Pricing)
   ├─ Kalender Harga (Price Calendar View)
   └─ Riwayat Perubahan Harga

📈 Laporan & Analitik
   ├─ Laporan Okupansi
   ├─ Laporan Pendapatan
   ├─ Laporan Reservasi (Booking/Cancel Rate)
   └─ Ekspor Laporan (Excel/PDF)

🔔 Notifikasi
   └─ Log Notifikasi (Reservasi Baru, Reminder, dll)

👥 Manajemen Pengguna
   ├─ Daftar Admin/Staff
   ├─ Role & Hak Akses
   └─ Log Aktivitas (Audit Trail)

⚙️ Pengaturan
   ├─ Profil Hotel
   ├─ Kebijakan Check-in/Check-out
   ├─ Pengaturan Notifikasi
   └─ Pengaturan Umum (Bahasa, Mata Uang, Zona Waktu)
```

---

## 7. Alur Utama (Key Flows)

### 7.1 Alur Approval Reservasi
1. Tamu/sistem booking eksternal mengirim reservasi → masuk ke status **Pending**.
2. Admin menerima notifikasi reservasi baru.
3. Admin membuka detail reservasi, mengecek ketersediaan kamar.
4. Admin memilih **Approve** atau **Reject** (wajib isi alasan jika reject).
5. Status reservasi terupdate, notifikasi terkirim ke tamu (jika terintegrasi channel notifikasi).
6. Jadwal kamar otomatis ter-update (kamar terkunci untuk tanggal terkait jika approved).

### 7.2 Alur Pengaturan Harga Musiman
1. Revenue manager membuka menu **Harga Musiman**.
2. Membuat aturan baru: nama musim, rentang tanggal, tipe kamar, harga baru.
3. Sistem mengecek konflik dengan aturan harga lain pada periode yang sama.
4. Preview simulasi dampak harga.
5. Publish aturan → harga otomatis berlaku pada tanggal terkait.

### 7.3 Alur Manajemen Jadwal Kamar
1. Admin membuka kalender ketersediaan.
2. Melihat status kamar per tanggal (Available/Booked/Blocked).
3. Admin dapat memblokir kamar manual (misal untuk maintenance).
4. Sistem mencegah reservasi baru masuk ke kamar yang sedang diblokir/terisi.

---

## 8. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| **Performa** | Halaman dashboard & kalender harus load < 2 detik untuk data hingga 1000 reservasi aktif |
| **Keamanan** | Autentikasi berbasis role (RBAC), enkripsi password, audit log aktivitas |
| **Skalabilitas** | Arsitektur mendukung penambahan jumlah kamar/tipe kamar tanpa perubahan struktur besar |
| **Ketersediaan** | Target uptime 99.5% |
| **Kompatibilitas** | Responsive (desktop-first, tetap dapat diakses di tablet) |
| **Auditability** | Semua perubahan status reservasi & harga tercatat dengan timestamp & user pelaku |

---

## 9. Metrik Keberhasilan (Success Metrics)

- Rata-rata waktu approval reservasi berkurang (target: < 15 menit dari sebelumnya manual).
- Penurunan kasus double-booking menjadi 0%.
- Peningkatan pendapatan melalui optimasi harga musiman (target: naik minimal 10% pada periode high season).
- Adopsi penggunaan dashboard oleh tim admin (target: 100% approval dilakukan via sistem, bukan manual).

---

## 10. Asumsi & Batasan

- Sistem ini adalah aplikasi internal (admin-only), tidak menyediakan sisi pemesanan untuk tamu langsung (asumsi ada sumber booking dari channel lain: website publik, OTA, walk-in yang diinput manual oleh admin).
- Satu properti/hotel per instance sistem di versi awal.
- Pembayaran dicatat sebagai status (Lunas/Belum/DP), bukan pemrosesan transaksi langsung.

---

## 11. Roadmap Pengembangan (Opsional)

| Fase | Fitur |
|---|---|
| **Fase 1 (MVP)** | Manajemen kamar, reservasi & approval, harga musiman dasar, dashboard sederhana |
| **Fase 2** | Laporan & analitik lanjutan, ekspor data, notifikasi email |
| **Fase 3** | Multi-properti, integrasi payment gateway, integrasi channel manager (OTA) |

---

## 12. Lampiran

- **Glosarium:**
  - *Seasonal Pricing*: Penentuan harga kamar berdasarkan periode/musim tertentu.
  - *Double Booking*: Dua reservasi berbeda untuk kamar & tanggal yang sama.
  - *Occupancy Rate*: Persentase kamar terisi dibanding total kamar tersedia.
  - *RBAC*: Role-Based Access Control, pengaturan hak akses berdasarkan peran pengguna.
