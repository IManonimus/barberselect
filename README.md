# Barberselect

Aplikasi manajemen barbershop dengan backend Laravel dan frontend mobile Flutter.

## 🚀 Cara Menjalankan

### 1. Jalankan Backend Laravel

Pastikan PHP dan Composer sudah terinstall.

```bash
cd barberselect
cp .env.example .env  # jika .env belum ada
php composer install --no-interaction --prefer-dist
php artisan key:generate

# Jalankan server Laravel
php artisan serve --host=0.0.0.0 --port=8000
```

Backend akan berjalan di **http://localhost:8000**.

### 2. Jalankan Android Emulator

Ada dua cara:

#### Opsi A: Melalui Flutter CLI
```bash
# Lihat daftar emulator yang tersedia
flutter emulators

# Jalankan emulator
flutter emulators --launch <nama_emulator>
# Contoh: flutter emulators --launch flutter_emulator
```

#### Opsi B: Melalui Android Studio
1. Buka **Android Studio**
2. Klik **Device Manager** (ikon ponsel di toolbar kanan)
3. Pilih emulator yang ada, klik tombol **Play** (▶)
4. Tunggu hingga emulator selesai booting

### 3. Jalankan Aplikasi Flutter

```bash
# Pindah ke direktori Flutter
cd Mobile\barberselect_mobile

# Jalankan di emulator (pastikan emulator sudah running)
flutter run -d emulator-5554 --dart-define=API_BASE_URL=http://10.0.2.2:8000
```

> **Catatan Penting:**
> - **10.0.2.2** adalah alamat khusus Android emulator yang mengarah ke `localhost` komputer host.
> - Jika menggunakan **iOS Simulator**, ganti dengan `http://localhost:8000`.
> - Jika menggunakan **perangkat fisik**, ganti dengan IP komputer host (contoh: `http://192.168.1.10:8000`).

### 4. Verifikasi Koneksi

Untuk memastikan backend dan frontend terhubung:

```bash
# Test API (dari terminal lain)
curl http://localhost:8000/api/landing-page

# Cek apakah emulator terdeteksi
flutter devices
```

---

## 📁 Struktur Proyek

```
barberselect-main/
├── barberselect/              # Backend Laravel (REST API)
│   ├── app/                   # Controllers, Models
│   ├── config/                # Konfigurasi (CORS, database, dll)
│   ├── database/              # Migration & Seeder
│   ├── routes/                # API routes (api.php)
│   └── ...
└── Mobile/
    └── barberselect_mobile/   # Frontend Flutter
        ├── lib/
        │   ├── core/
        │   │   ├── config/        # API base URL
        │   │   └── network/       # Dio HTTP client
        │   └── features/          # Auth, Catalog, Profile, dll
        └── android/               # Android-specific config
```

## ⚙️ Konfigurasi API Base URL (Flutter)

Definisi base URL ada di `Mobile/barberselect_mobile/lib/core/config/app_config.dart`:

```dart
static const String apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://10.0.2.2:8000',
);
```

Default untuk emulator Android adalah `http://10.0.2.2:8000`. Bisa di-override saat runtime:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000
```

## 🔧 Troubleshooting

### ❌ Connection Error / Gagal Koneksi ke API

1. **Pastikan backend Laravel sudah running**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Gunakan `--host=0.0.0.0` agar bisa diakses dari emulator.

2. **Pastikan port 8000 tidak terblokir firewall**
   - Windows: Matikan sementara Windows Defender Firewall, atau tambahkan rule inbound untuk port 8000.

3. **Pastikan emulator sudah online**
   ```bash
   flutter devices
   ```
   Jika muncul `Device emulator-5554 is offline`, tunggu beberapa saat hingga booting selesai.

4. **Cek langsung dari browser emulator**
   - Buka browser di Android emulator
   - Buka alamat: `http://10.0.2.2:8000/api/landing-page`
   - Jika muncul response JSON, berarti koneksi berhasil.

5. **CORS issue**
   - Laravel sudah include middleware `HandleCors` secara default.
   - Pastikan `config/cors.php` (jika ada) mengizinkan origin dari mobile app.

6. **Cleartext traffic (HTTP)**
   - Pastikan `android:usesCleartextTraffic="true"` ada di `AndroidManifest.xml` (sudah di-set secara default).

### ❌ Emulator Tidak Terdeteksi

```bash
flutter doctor -v
```
Pastikan Android SDK terinstall dengan benar. Jika perlu, set `ANDROID_HOME`:

```bash
$env:ANDROID_HOME = "$env:LOCALAPPDATA\Android\Sdk"