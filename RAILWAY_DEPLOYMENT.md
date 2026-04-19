# Railway Deployment Guide (Laravel 12)

Panduan ini dibuat khusus untuk project ini agar deploy di Railway lebih stabil.

## 0) Kenapa deploy sering gagal di Railway?
Masalah paling umum biasanya:
1. **Web process tidak bind ke port Railway** (harus pakai `\$PORT`).
2. **`APP_KEY` belum di-set**.
3. **Database belum terkoneksi** (`DB_URL` salah / belum terhubung service MySQL).
4. **Migrations belum jalan**.
5. **Build frontend gagal** karena dependency Node tidak sesuai.

---

## 1) Prasyarat sebelum deploy

Pastikan di lokal:

```bash
php -v
composer -V
node -v
npm -v
```

Target minimal project ini:
- PHP 8.2+
- Composer terbaru
- Node 18+ (disarankan Node 20)

Lalu cek project bisa jalan lokal:

```bash
composer install
npm ci
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Jika lokal belum jalan, Railway hampir pasti gagal juga.

---

## 2) File penting di repository

Pastikan file berikut ada dan sudah di-push:
- `Procfile`
- `.env.example`
- `composer.json`
- `package.json`

`Procfile` dipakai Railway untuk menjalankan process.

---

## 3) Konfigurasi Procfile yang benar

Gunakan format berikut agar Railway bisa routing trafik ke service kamu:

```procfile
web: php artisan migrate --force && php artisan storage:link || true && php -S 0.0.0.0:${PORT} -t public
release: npm ci && npm run build
queue: php artisan queue:work --tries=3 --timeout=90
scheduler: php artisan schedule:work
```

> Kenapa ini penting? Karena Railway memberikan port dinamis lewat env `PORT`. Kalau hardcode `8080`, sering berakhir "Application failed to respond".

---

## 4) Buat project di Railway

1. Login ke <https://railway.app>.
2. Klik **New Project**.
3. Pilih **Deploy from GitHub repo**.
4. Pilih repository project ini.

Railway akan otomatis build setelah repo terhubung.

---

## 5) Tambahkan MySQL service

1. Di project Railway, klik **+ New**.
2. Pilih **Database** → **MySQL**.
3. Setelah service jadi, buka tab variables MySQL, pastikan `DATABASE_URL` tersedia.

---

## 6) Hubungkan app service ke database

Di service aplikasi (bukan service MySQL), set variable ini:

```env
APP_NAME=Ecommerce
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<domain-railway-anda>
APP_KEY=base64:<APP_KEY_ANDA>

DB_CONNECTION=mysql
DB_URL=${{MySQL.DATABASE_URL}}

LOG_CHANNEL=stack
LOG_LEVEL=info

CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
FILESYSTEM_DISK=public
```

Untuk integrasi project ini, tambahkan juga:

```env
RAJAONGKIR_API_KEY=...
RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter

MIDTRANS_CLIENT_KEY=...
MIDTRANS_SERVER_KEY=...
MIDTRANS_IS_PRODUCTION=false
```

> Catatan: format reference `\${{MySQL.DATABASE_URL}}` bisa berbeda nama sesuai nama service DB kamu. Pilih lewat UI **Add Reference** agar tidak typo.

---

## 7) Generate APP_KEY (kalau belum punya)

Di lokal:

```bash
php artisan key:generate --show
```

Copy output `base64:...` ke variable `APP_KEY` di Railway.

---

## 8) Deploy ulang dan cek logs

Setelah variables lengkap:
1. Trigger redeploy (klik **Deploy** / push commit baru).
2. Pantau log dari awal sampai selesai.

Yang harus terlihat sukses:
- composer install selesai
- (opsional) npm build selesai
- migrate sukses
- web process up tanpa crash loop

---


## 8.1) Cegah tampilan berantakan (CSS/JS tidak ter-load)

Jika halaman tampil polos (seperti CSS tidak kebaca), biasanya asset Vite belum dibuild di environment Railway.

Solusi di project ini:
1. Pastikan `Procfile` punya `release: npm ci && npm run build`.
2. Pastikan `railpack.build.sh` menjalankan `npm ci` dan `npm run build`.
3. Redeploy lalu cek tidak ada error di tahap build frontend.

## 9) Checklist debug cepat (kalau masih gagal)

### A. Error: "Application failed to respond"
- Pastikan `web` process memakai `${PORT}` dan document root `public` (`php -S 0.0.0.0:${PORT} -t public`).
- Pastikan process bertipe **web** (bukan worker).

### B. Error: "No application encryption key has been specified"
- Set `APP_KEY` di Railway.

### C. Error database / SQLSTATE
- Cek `DB_CONNECTION=mysql`.
- Cek `DB_URL` reference ke MySQL service yang benar.
- Cek service MySQL statusnya running.

### D. Error tabel belum ada
- Jalankan migration dari deploy command (sudah di Procfile), atau manual:
  ```bash
  php artisan migrate --force
  ```

### E. Build frontend gagal
- Cek lockfile (`package-lock.json`) konsisten.
- Gunakan `npm ci` (bukan `npm install`) untuk build reproducible.

---

## 10) Rekomendasi production

1. Aktifkan auto deploy dari branch utama.
2. Pasang custom domain + HTTPS.
3. Simpan secret hanya di Railway Variables (jangan commit `.env`).
4. Pantau logs + metrics (CPU/RAM/restart count).
5. Pertimbangkan memisahkan queue worker ke service terpisah jika traffic naik.

---

## 11) Template "known good" minimal variables

Jika mau mulai dari minimal dulu (biar cepat hidup), pakai ini:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://<railway-domain>

DB_CONNECTION=mysql
DB_URL=${{MySQL.DATABASE_URL}}

CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=cookie
FILESYSTEM_DISK=public

LOG_CHANNEL=stack
LOG_LEVEL=info
```

Setelah app sudah up, baru isi integrasi pihak ketiga (mail, midtrans, rajaongkir, reverb).
