# 📱 About App

**XYZ Football API** adalah sistem backend RESTful yang dirancang untuk mengelola tim sepakbola amatir, pemain, jadwal pertandingan, hasil pertandingan, dan laporan performa. Dibuat untuk perusahaan XYZ, aplikasi ini membantu merapikan pengelolaan data terkait tim sepakbola yang sebelumnya dilakukan secara manual.

Administrator memiliki akses untuk:

- Membuat dan mengelola tim internal maupun eksternal (lawan)
- Mendaftarkan pemain dan memastikan nomor punggung unik per tim
- Menjadwalkan pertandingan antar tim
- Mencatat hasil pertandingan termasuk peristiwa gol lengkap dengan menit terjadinya
- Menghasilkan laporan pertandingan dengan status menang/seri dan informasi top skor

---

### 🎯 Catatan Branding

Untuk fleksibilitas branding, aplikasi ini bisa dinamai ulang menjadi:

- **FootballManage API** — branding sederhana dan modern untuk manajemen tim sepakbola
- **SoccerManage API** — alternatif nama yang menyasar audiens global

> Aplikasi ini dirancang agar scalable dan fleksibel jika di masa depan ingin digunakan untuk olahraga lain.

---

## ⚙️ Tech Stack

| Component      | Technology                         |
| -------------- | ---------------------------------- |
| Framework      | Laravel 12                         |
| Authentication | Laravel Sanctum (token-based auth) |
| Database       | MySQL                              |
| API Format     | RESTful JSON                       |
| Faker/Seeding  | Laravel Seeder & Factory           |
| Testing Tool   | Postman (for API docs & testing)   |
| Deployment     | Localhost                          |

---

## 🚀 Instalasi & Setup

1. **Clone Repository**

```bash
git clone https://github.com/your-username/xyz-football-api.git
cd xyz-football-api
```

2. **Install Dependencies**

```bash
composer install
cp .env.example .env
php artisan key:generate
```

3. **Konfigurasi Database**\
   Edit file `.env` dan sesuaikan koneksi database:

```env
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

4. **Migrasi & Seeder Dummy Data**

```bash
php artisan migrate --seed
```

5. **Storage Link (untuk logo)**

```bash
php artisan storage:link
```

6. **Jalankan Server Lokal**

```bash
php artisan serve
```

---

## ✅ Fitur Utama

- CRUD Tim dan Pemain
- Jadwal Pertandingan & Hasil
- Pencatatan Gol dan Top Skor
- Laporan Pertandingan Lengkap

---

## 📢 Dokumentasi API Lengkap

Seluruh dokumentasi endpoint lengkap tersedia di Postman berikut:\
➡️ [**Postman Collection Link**](https://www.postman.com/cryosat-observer-63638233/workspace/xyz)

> ⚠️ Rate limit berlaku untuk login dan akses API, default 60 request/menit.

---

## 🧩 Asumsi dan Ide

- **Pencatatan tim dan pemain lawan**\
  Tim dan pemain lawan perlu diinputkan untuk kebutuhan penjadwalan pertandingan dan pencatatan hasil pertandingan secara lengkap.

- **Pemain bisa tidak terikat tim**\
  Pemain dapat berstatus free agent.

- **Foto atau avatar pemain**\
  Bisa ditambahkan untuk profil pemain.

- **Pencatatan histori transfer in/out pemain**\
  Pencatatan histori transfer in/out pemain dapat dikembangkan untuk merekam riwayat perpindahan pemain antar tim.
  

- **Result dan Gol terpisah**\
  Hasil pertandingan dan gol dicatatkan secara terpisah.

- **Match Result unik per jadwal**\
  Tiap pertandingan hanya boleh punya satu hasil (match\_schedule\_id unique di match\_results).

- **Goal wajib refer ke player dan tim**\
  Agar laporan top scorer valid, data gol wajib menyertakan player\_id dan team\_id.

- **Report per match saja**\
  Laporan lengkap (skor, status menang/draw, top scorer, total win tim) hanya tersedia per pertandingan via /matches/{id}/report.

---
