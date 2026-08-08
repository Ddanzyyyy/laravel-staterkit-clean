# Product Requirements Document (PRD)
## Task To Do — Management Aplikasi (Microsoft To Do style)

| | |
|---|---|
| **Nama Produk** | Task To Do Dashboard |
| **Versi** | 1.0 |
| **Tanggal** | 8 Agustus 2026 |
| **Status** | Draft |

---

## 1. Latar Belakang

Pengelolaan tugas pribadi/tim saat ini tersebar (chat, catatan manual, spreadsheet) sehingga mudah terlewat. Aplikasi ini menyediakan panel web untuk mencatat, mengorganisasi, dan memprioritaskan tugas harian dengan pola interaksi yang familiar dari Microsoft To Do: checklist simpel, daftar (list) custom, dan smart view (My Day, Important, Planned, Tasks).

---

## 2. Tujuan Produk

1. Menyediakan catatan tugas yang cepat dibuat (add-inline, tanpa pindah halaman).
2. Mengorganisasi tugas ke dalam list custom + smart list otomatis.
3. Membantu prioritas lewat due date, important flag, dan view Planned.
4. Basis arsitektur sederhana (Laravel + Inertia + Vue) yang mudah diperluas (recurring, reminder).

### Non-Goals (di luar cakupan versi ini)
- Aplikasi mobile native (web responsive dulu).
- Kolaborasi/sharing antar pengguna (single-user per akun).
- Notifikasi email/push (reminder in-app ditunda).
- Integrasi kalender eksternal.

---

## 3. Target Pengguna

| Role | Deskripsi |
|---|---|
| **User (terautentikasi)** | Semua fitur, data ter-isolasi per user |

---

## 4. User Stories Utama

1. Sebagai user, saya ingin menambah tugas langsung dari kolom list agar cepat.
2. Sebagai user, saya ingin mencentang tugas sebagai selesai.
3. Sebagai user, saya ingin membuat list custom (mis. Pribadi, Kerja) untuk mengelompokkan tugas.
4. Sebagai user, saya ingin menandai tugas penting dengan bintang.
5. Sebagai user, saya ingin mengatur due date agar muncul di My Day / Planned.
6. Sebagai user, saya ingin mencari tugas berdasarkan judul.

---

## 5. Ruang Lingkup Fitur (Scope)

### 5.1 Manajemen Tugas
- CRUD tugas: add inline, edit (judul, due date, note), hapus, toggle selesai.
- Flag `important` (bintang).
- Due date (tanggal saja) via `<input type="date">`.
- Note/catatan pada detail tugas.

### 5.2 Manajemen List
- CRUD list custom (nama + warna).
- Tugas bisa dipindah antar list.
- Smart list (computed, tanpa tabel): My Day, Important, Planned, Tasks (All).

### 5.3 Smart View (filter query)
| View | Filter |
|---|---|
| My Day | `due_date = hari ini`, belum selesai |
| Important | `is_important = true`, belum selesai |
| Planned | `due_date` ada, belum selesai, urut by due_date |
| Tasks | semua belum selesai |

### 5.4 Pencarian
- Filter `q` pada judul tugas (debounce, `preserveState`).

---

## 6. Menu Utama

```
✅ Tasks (halaman utama)
   ├─ Smart View: My Day / Important / Planned / Tasks
   ├─ List custom (Pribadi, Kerja, dst.)
   └─ Add task / Add list inline

Dashboard (starter kit, tetap ada)
Users (starter kit, tetap ada)
Settings (starter kit, tetap ada)
```

---

## 7. Alur Utama (Key Flows)

### 7.1 Tambah Tugas
1. User mengetik di input "Add a task" → enter.
2. Task tersimpan di view/list aktif, tampil di bawah.

### 7.2 Tandai Selesai
1. Klik checkbox pada task → `is_completed = true`.
2. Tugas hilang dari smart view (terkecuali "Tasks" jika toggle show-completed aktif — opsional).

### 7.3 Buat List & Pindahkan Tugas
1. Ketik nama list baru di sidebar → list muncul.
2. Pada detail tugas, pilih list tujuan → tugas berpindah.

---

## 8. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| **Performa** | Load view < 2s untuk 1000 tugas |
| **Keamanan** | Auth Laravel, data per-user (`user_id` scope) |
| **Kompatibilitas** | Responsive (desktop-first, tetap dapat diakses di tablet) |
| **Konsistensi** | Mengikuti pola starter kit (custom/ pages, UI radix-vue) |

---

## 9. Metrik Keberhasilan (Success Metrics)

- Tugas terkelola: ≥ 80% tugas punya due date/important dalam 3 bulan.
- Waktu pembuatan tugas < 3 detik (inline add).
- Adopsi: user aktif mingguan memakai ≥ 3 smart view.

---

## 10. Asumsi & Batasan (MVP)

- Tanpa recurring task, tanpa reminder/notifikasi (butuh queue).
- Tanpa subtasks, tags, drag-drop reorder (sorting by query dulu).
- Tanpa due time (hanya tanggal).
- My Day = tugas due hari ini (tanpa "Add to My Day" manual).

---

## 11. Roadmap Pengembangan

| Fase | Fitur |
|---|---|
| **Fase 1 (MVP)** | CRUD tugas & list, smart view, due date, important, note, search |
| **Fase 2** | Recurring task, reminder in-app/email, drag-drop, show completed |
| **Fase 3** | Subtasks, tags, sharing, kalender view |
