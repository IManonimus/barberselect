# 📋 Catatan: Cara Menjalankan Project BarberSelect

> Panduan langkah demi langkah untuk menjalankan backend Laravel + Flutter emulator Android.

---

## Prerequisites

Pastikan sudah terinstall:
- PHP 8.x + Composer
- Node.js + NPM
- Flutter SDK
- Android Studio (dengan emulator yang sudah dibuat)

---

## 🚀 Langkah 1 — Setup Backend Laravel

Buka **Terminal 1**, masuk ke folder backend:

```bash
cd barberselect
```

Install dependencies:

```bash
composer install
```

Buat file `.env`:

```bash
copy .env.example .env
```

Generate app key:

```bash
php artisan key:generate
```

Jalankan migrasi database (SQLite):

```bash
php artisan migrate
```

Jalankan seeder data:

```bash
php artisan db:seed
```

**Jalankan server Laravel:**

```bash
php artisan serve
```

✅ Server running di `http://localhost:8000`  
✅ Emulator Android mengakses via `http://10.0.2.2:8000`

---

## 🚀 Langkah 2 — Setup Frontend Flutter

Buka **Terminal 2**, masuk ke folder mobile:

```bash
cd Mobile/barberselect_mobile
```

Install dependencies:

```bash
flutter pub get
```

---

## 🚀 Langkah 3 — Start Android Emulator

Buka **Android Studio** → Tools → Device Manager → Klik **▶ Play** pada emulator yang sudah dibuat. Tunggu hingga homescreen muncul.

---

## 🚀 Langkah 4 — Run Flutter App

Di **Terminal 2**, jalankan:

```bash
flutter run
```

Atau pilih device specific:

```bash
flutter devices
flutter run -d <device-id>
```

---

## ✅ Urutan Singkat (Cheat Sheet)

```
Terminal 1:
  cd barberselect
  composer install
  copy .env.example .env
  php artisan key:generate
  php artisan migrate
  php artisan db:seed
  php artisan serve

Terminal 2:
  cd Mobile/barberselect_mobile
  flutter pub get
  flutter run
```

---

## 📌 Catatan Penting

| Item | Detail |
|------|--------|
| **API URL di emulator** | `http://10.0.2.2:8000` |
| **API URL di browser** | `http://localhost:8000` |
| **Database** | SQLite (`DB_CONNECTION=sqlite`) |
| **Port backend** | 8000 |
| **Flutter SDK** | `^3.11.5` |

> Kedua terminal harus berjalan bersamaan agar aplikasi bisa mengambil data dari API.