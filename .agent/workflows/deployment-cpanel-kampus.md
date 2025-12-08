---
description: Panduan Deploy Voica ke cPanel Kampus (WAJIB VOICE AUTH)
---

# 🚀 PANDUAN DEPLOYMENT VOICA KE CPANEL KAMPUS
## Voice Register & Voice Login HARUS JALAN!

---

## 📋 CHECKLIST PERSIAPAN

### ✅ **STEP 0: CEK PYTHON SUPPORT DI CPANEL**

**WAJIB dilakukan PERTAMA sebelum upload!**

1. **Upload file checker ke cPanel:**
   - File: `check_python_support.php`
   - Upload ke: `public_html/check_python_support.php`

2. **Akses via browser:**
   ```
   https://domain-kampus-anda.ac.id/check_python_support.php
   ```

3. **Lihat hasilnya:**
   - ✅ Jika semua hijau → Lanjut ke STEP 1
   - ❌ Jika ada merah → Catat error, hubungi admin kampus

4. **Yang HARUS ADA:**
   - ✅ `shell_exec()` enabled
   - ✅ Python 3.x terinstall
   - ✅ pip terinstall
   - ⚠️ Libraries (numpy, scipy, librosa) - bisa install nanti
   - ⚠️ FFmpeg - bisa install nanti

---

## 🔧 **STEP 1: INSTALL PYTHON LIBRARIES DI CPANEL**

### **Opsi A: Via Terminal SSH (RECOMMENDED)**

1. **Login SSH ke cPanel:**
   ```bash
   ssh username@domain-kampus-anda.ac.id
   ```

2. **Cek Python path:**
   ```bash
   which python3
   # Output: /usr/bin/python3 (catat ini!)
   ```

3. **Install libraries dengan pip:**
   ```bash
   # Install ke user directory (tidak perlu sudo)
   pip3 install --user numpy scipy librosa
   
   # Atau jika pip3 tidak ada, coba:
   python3 -m pip install --user numpy scipy librosa
   ```

4. **Verifikasi instalasi:**
   ```bash
   python3 -c "import numpy; print('numpy:', numpy.__version__)"
   python3 -c "import scipy; print('scipy:', scipy.__version__)"
   python3 -c "import librosa; print('librosa:', librosa.__version__)"
   ```

5. **Cek FFmpeg:**
   ```bash
   which ffmpeg
   # Jika tidak ada, hubungi admin kampus
   ```

### **Opsi B: Via cPanel Python App (jika tersedia)**

1. **cPanel → Setup Python App**
2. **Create Application:**
   - Python version: 3.8+
   - Application root: `/home/username/python-env`
   - Application URL: (kosongkan)

3. **Masuk ke virtual environment:**
   ```bash
   source /home/username/python-env/bin/activate
   pip install numpy scipy librosa
   ```

### **Opsi C: Request ke Admin Kampus**

Jika tidak bisa install sendiri, kirim email ke admin:

```
Subject: Request Install Python Libraries untuk Tugas Akhir

Yth. Admin IT,

Saya mahasiswa yang sedang mengerjakan tugas akhir aplikasi web dengan fitur voice recognition.
Mohon bantuan untuk install Python libraries berikut di server cPanel:

1. numpy
2. scipy  
3. librosa
4. FFmpeg (untuk audio processing)

Command install:
pip3 install --user numpy scipy librosa

Terima kasih atas bantuannya.

Hormat saya,
[Nama Anda]
[NIM]
```

---

## 📦 **STEP 2: PERSIAPAN FILE DI LOCAL**

1. **Pastikan semua file sudah lengkap:**
   ```
   voica/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/
   ├── resources/
   ├── routes/
   ├── storage/
   ├── scripts/
   │   └── voice_processor.py  ← PENTING!
   ├── .env.example
   ├── composer.json
   └── ...
   ```

2. **Buat file .env untuk production:**
   
   Copy `.env.example` ke `.env.production` dan edit:

   ```env
   APP_NAME=Voica
   APP_ENV=production
   APP_KEY=                    # Akan di-generate nanti
   APP_DEBUG=false
   APP_URL=https://domain-kampus-anda.ac.id

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=              # Nama database dari cPanel
   DB_USERNAME=              # Username database dari cPanel
   DB_PASSWORD=              # Password database dari cPanel

   # PENTING: Python path dari hasil check_python_support.php
   PYTHON_PATH=/usr/bin/python3

   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   ```

3. **Optimize di local (opsional):**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

4. **Compress semua file:**
   ```bash
   # Buat zip TANPA folder vendor (akan install di server)
   # Exclude: vendor, node_modules, .git, storage/logs
   ```

---

## 📤 **STEP 3: UPLOAD KE CPANEL**

### **Struktur Folder di cPanel:**

```
/home/username/
├── laravel-app/              ← Upload semua file Laravel di sini
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── scripts/              ← PENTING: voice_processor.py
│   │   └── voice_processor.py
│   ├── .env                  ← Copy dari .env.production
│   ├── composer.json
│   └── ...
│
└── public_html/              ← Document root (hanya isi folder public)
    ├── index.php             ← Dari laravel-app/public/
    ├── .htaccess             ← Dari laravel-app/public/
    ├── css/
    ├── js/
    ├── favicon.ico
    └── ...
```

### **Cara Upload:**

**Via File Manager cPanel:**

1. **Upload zip file:**
   - cPanel → File Manager
   - Navigate ke `/home/username/`
   - Upload `voica.zip`
   - Extract → Rename folder jadi `laravel-app`

2. **Upload public files:**
   - Navigate ke `public_html/`
   - Upload semua isi dari `laravel-app/public/`
   - JANGAN upload folder `public/` nya, tapi ISI nya saja!

**Via FTP (FileZilla):**

1. Connect ke FTP kampus
2. Upload `laravel-app/` ke `/home/username/`
3. Upload isi `public/` ke `/public_html/`

---

## ⚙️ **STEP 4: KONFIGURASI DI CPANEL**

### **A. Setup Database**

1. **cPanel → MySQL Databases**

2. **Create Database:**
   - Database name: `voica_db` (atau sesuai aturan kampus)
   - Klik "Create Database"

3. **Create User:**
   - Username: `voica_user`
   - Password: (generate strong password)
   - Klik "Create User"

4. **Add User to Database:**
   - User: `voica_user`
   - Database: `voica_db`
   - Privileges: **ALL PRIVILEGES**
   - Klik "Make Changes"

5. **Catat kredensial:**
   ```
   DB_HOST=localhost
   DB_DATABASE=username_voica_db  (biasanya ada prefix username)
   DB_USERNAME=username_voica_user
   DB_PASSWORD=your_password
   ```

### **B. Edit .env di Server**

1. **cPanel → File Manager**
2. **Navigate ke:** `/home/username/laravel-app/`
3. **Edit file `.env`:**

   ```env
   APP_NAME=Voica
   APP_ENV=production
   APP_KEY=                    # Kosongkan dulu, akan di-generate
   APP_DEBUG=false
   APP_URL=https://domain-kampus-anda.ac.id

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=username_voica_db
   DB_USERNAME=username_voica_user
   DB_PASSWORD=your_password_here

   # Python path (dari check_python_support.php)
   PYTHON_PATH=/usr/bin/python3
   ```

### **C. Edit public_html/index.php**

1. **cPanel → File Manager**
2. **Navigate ke:** `/home/username/public_html/`
3. **Edit `index.php`:**

   **GANTI baris ini:**
   ```php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```

   **JADI:**
   ```php
   require __DIR__.'/../laravel-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
   ```

4. **Save file**

---

## 🔨 **STEP 5: INSTALL COMPOSER & SETUP LARAVEL**

### **Via Terminal SSH:**

```bash
# 1. Masuk ke folder Laravel
cd ~/laravel-app

# 2. Install Composer dependencies
composer install --optimize-autoloader --no-dev

# 3. Generate APP_KEY
php artisan key:generate

# 4. Set permissions
chmod -R 755 ~/laravel-app
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache

# 5. Create storage symlink
php artisan storage:link

# 6. Run migrations
php artisan migrate --force

# 7. Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Via cPanel Terminal (jika tersedia):**

Sama seperti di atas, jalankan command satu per satu.

### **Jika TIDAK ada akses Terminal:**

1. **Install Composer via cPanel:**
   - Beberapa cPanel punya "PHP Composer" tool
   - Atau download `composer.phar` manual

2. **Run Artisan via browser:**
   
   Buat file `setup.php` di `public_html/`:

   ```php
   <?php
   // HAPUS FILE INI SETELAH SETUP SELESAI!
   
   chdir(__DIR__ . '/../laravel-app');
   require __DIR__ . '/../laravel-app/vendor/autoload.php';
   
   $app = require_once __DIR__ . '/../laravel-app/bootstrap/app.php';
   $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
   
   echo "<h1>Laravel Setup</h1>";
   
   // Generate key
   echo "<h2>1. Generate APP_KEY</h2>";
   $status = $kernel->call('key:generate', ['--force' => true]);
   echo $status === 0 ? "✅ Success<br>" : "❌ Failed<br>";
   
   // Migrate
   echo "<h2>2. Run Migrations</h2>";
   $status = $kernel->call('migrate', ['--force' => true]);
   echo $status === 0 ? "✅ Success<br>" : "❌ Failed<br>";
   
   // Storage link
   echo "<h2>3. Create Storage Link</h2>";
   $status = $kernel->call('storage:link');
   echo $status === 0 ? "✅ Success<br>" : "❌ Failed<br>";
   
   // Cache
   echo "<h2>4. Cache Config</h2>";
   $kernel->call('config:cache');
   $kernel->call('route:cache');
   $kernel->call('view:cache');
   echo "✅ Done<br>";
   
   echo "<hr>";
   echo "<strong>SETUP COMPLETE! HAPUS FILE INI SEKARANG!</strong>";
   ?>
   ```

   Akses: `https://domain-kampus-anda.ac.id/setup.php`

---

## 🎤 **STEP 6: TEST VOICE FEATURES**

### **A. Test Python Script via Terminal**

```bash
# 1. Masuk ke folder scripts
cd ~/laravel-app/scripts

# 2. Test enroll (butuh file audio test)
python3 voice_processor.py enroll /path/to/test.wav

# Expected output:
# {"success": true, "voice_path": "...", "features": [...], "feature_count": 26}
```

### **B. Test via Browser**

1. **Akses aplikasi:**
   ```
   https://domain-kampus-anda.ac.id
   ```

2. **Test Register dengan Voice:**
   - Klik "Daftar"
   - Isi form (nama, telepon, password)
   - Klik tombol rekam suara
   - Ucapkan sesuatu (min 3 detik)
   - Submit

3. **Cek logs jika error:**
   ```bash
   tail -f ~/laravel-app/storage/logs/laravel.log
   ```

### **C. Troubleshooting Voice**

**Error: "Python script tidak memberikan output"**

```bash
# Test manual
cd ~/laravel-app
python3 scripts/voice_processor.py enroll /tmp/test.wav 2>&1

# Cek error detail
```

**Error: "ModuleNotFoundError: No module named 'librosa'"**

```bash
# Install lagi
pip3 install --user librosa numpy scipy

# Cek instalasi
python3 -c "import librosa; print(librosa.__version__)"
```

**Error: "FFmpeg not found"**

```bash
# Cek FFmpeg
which ffmpeg

# Jika tidak ada, hubungi admin kampus
# Atau disable konversi audio (hanya terima WAV)
```

---

## 🔒 **STEP 7: SECURITY & OPTIMIZATION**

### **A. Set Permissions**

```bash
# Via SSH
chmod -R 755 ~/laravel-app
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache
chmod 600 ~/laravel-app/.env

# Pastikan .env tidak bisa diakses public
```

### **B. Protect .env via .htaccess**

Edit `public_html/.htaccess`, tambahkan:

```apache
# Protect .env
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### **C. Enable HTTPS**

1. **cPanel → SSL/TLS**
2. **Install SSL Certificate:**
   - Pilih "Let's Encrypt" (gratis)
   - Atau upload SSL dari kampus

3. **Force HTTPS di .htaccess:**

   Edit `public_html/.htaccess`, tambahkan di atas:

   ```apache
   # Force HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

---

## 📊 **STEP 8: MONITORING & MAINTENANCE**

### **A. Cek Logs**

```bash
# Laravel logs
tail -f ~/laravel-app/storage/logs/laravel.log

# PHP errors
tail -f ~/public_html/error_log
```

### **B. Backup Database**

```bash
# Via SSH
mysqldump -u username_voica_user -p username_voica_db > backup_$(date +%Y%m%d).sql

# Via cPanel → phpMyAdmin → Export
```

### **C. Clear Cache**

```bash
# Via SSH
cd ~/laravel-app
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## ⚠️ **TROUBLESHOOTING UMUM**

### **1. Error 500 - Internal Server Error**

```bash
# Cek logs
tail -50 ~/laravel-app/storage/logs/laravel.log

# Cek permissions
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### **2. Voice Register/Login Tidak Jalan**

```bash
# Test Python script
cd ~/laravel-app
python3 scripts/voice_processor.py enroll /tmp/test.wav 2>&1

# Cek libraries
python3 -c "import numpy, scipy, librosa; print('OK')"

# Cek .env
grep PYTHON_PATH ~/laravel-app/.env
```

### **3. Database Connection Error**

```bash
# Cek kredensial di .env
cat ~/laravel-app/.env | grep DB_

# Test koneksi
php artisan tinker
>>> DB::connection()->getPdo();
```

### **4. Assets (CSS/JS) Tidak Muncul**

```bash
# Cek APP_URL di .env
grep APP_URL ~/laravel-app/.env

# Pastikan sesuai domain
APP_URL=https://domain-kampus-anda.ac.id

# Clear cache
php artisan config:cache
```

---

## 📞 **KONTAK DARURAT**

Jika ada masalah yang tidak bisa diselesaikan:

1. **Catat error message lengkap**
2. **Screenshot hasil `check_python_support.php`**
3. **Kirim logs:**
   ```bash
   tail -100 ~/laravel-app/storage/logs/laravel.log > error_log.txt
   ```
4. **Hubungi admin kampus dengan info lengkap**

---

## ✅ **CHECKLIST FINAL**

Sebelum presentasi/demo, pastikan:

- [ ] Aplikasi bisa diakses via browser
- [ ] Register dengan password bisa
- [ ] **Register dengan voice bisa** ✅
- [ ] Login dengan password bisa
- [ ] **Login dengan voice bisa** ✅
- [ ] Dashboard menampilkan data
- [ ] **Voice transaction bisa** (input transaksi dengan suara)
- [ ] Laporan bisa diakses
- [ ] HTTPS aktif (ikon gembok di browser)
- [ ] Tidak ada error di console browser (F12)

---

**GOOD LUCK! 🚀**

Jika ada error, jangan panik. Cek logs, baca error message, dan troubleshoot step by step!
