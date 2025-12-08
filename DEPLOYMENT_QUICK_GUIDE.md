# 🚀 VOICA - QUICK DEPLOYMENT GUIDE (cPanel Kampus)

## ⚡ LANGKAH CEPAT (5 MENIT BACA INI DULU!)

### 📋 YANG HARUS ANDA LAKUKAN:

#### **SEBELUM UPLOAD:**

1. **CEK PYTHON SUPPORT** ⭐ PALING PENTING!
   - Upload `check_python_support.php` ke cPanel
   - Akses via browser: `https://domain-anda.ac.id/check_python_support.php`
   - **HARUS ADA:**
     - ✅ shell_exec() enabled
     - ✅ Python 3.x installed
     - ✅ pip installed
   - **Screenshot hasilnya!**

2. **INSTALL PYTHON LIBRARIES** (via SSH)
   ```bash
   pip3 install --user numpy scipy librosa
   ```
   
   **ATAU upload `install_python_deps.sh` dan jalankan:**
   ```bash
   bash ~/laravel-app/scripts/install_python_deps.sh
   ```

#### **SAAT UPLOAD:**

3. **STRUKTUR FOLDER:**
   ```
   /home/username/
   ├── laravel-app/          ← Upload SEMUA file Laravel
   │   ├── scripts/          ← WAJIB ada voice_processor.py!
   │   └── ...
   └── public_html/          ← Upload ISI folder public/ saja
       ├── index.php
       ├── .htaccess
       └── ...
   ```

4. **EDIT 2 FILE PENTING:**
   
   **A. `public_html/index.php`** - Ganti path:
   ```php
   require __DIR__.'/../laravel-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
   ```
   
   **B. `laravel-app/.env`** - Set database & Python:
   ```env
   DB_DATABASE=username_voica_db
   DB_USERNAME=username_voica_user
   DB_PASSWORD=your_password
   
   PYTHON_PATH=/usr/bin/python3  ← Dari check_python_support.php
   ```

#### **SETELAH UPLOAD:**

5. **JALANKAN COMMAND** (via SSH):
   ```bash
   cd ~/laravel-app
   composer install --no-dev
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   chmod -R 775 storage bootstrap/cache
   ```

6. **TEST VOICE:**
   ```bash
   # Test Python script
   cd ~/laravel-app/scripts
   python3 test_dependencies.py
   
   # Test voice processor
   python3 voice_processor.py enroll /tmp/test.wav
   ```

7. **AKSES APLIKASI:**
   ```
   https://domain-kampus-anda.ac.id
   ```

---

## 🆘 TROUBLESHOOTING CEPAT

### ❌ "Python script tidak memberikan output"

```bash
# Cek Python path
which python3

# Test manual
python3 ~/laravel-app/scripts/voice_processor.py enroll /tmp/test.wav 2>&1

# Update PYTHON_PATH di .env
```

### ❌ "ModuleNotFoundError: No module named 'librosa'"

```bash
# Install lagi
pip3 install --user numpy scipy librosa

# Verify
python3 -c "import librosa; print('OK')"
```

### ❌ "shell_exec() disabled"

**SOLUSI:** Hubungi admin kampus, minta enable `shell_exec()` di `php.ini`

Email template ada di file `deployment-cpanel-kampus.md`

### ❌ Error 500

```bash
# Cek logs
tail -50 ~/laravel-app/storage/logs/laravel.log

# Fix permissions
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache
```

---

## 📞 JIKA STUCK

1. **Baca panduan lengkap:** `.agent/workflows/deployment-cpanel-kampus.md`
2. **Cek logs:** `storage/logs/laravel.log`
3. **Screenshot error** dan catat pesan lengkap
4. **Hubungi admin kampus** dengan info:
   - Screenshot `check_python_support.php`
   - Error message dari logs
   - Apa yang sudah dicoba

---

## ✅ CHECKLIST SEBELUM DEMO

- [ ] `check_python_support.php` semua hijau
- [ ] Python libraries terinstall (numpy, scipy, librosa)
- [ ] Database migrate berhasil
- [ ] Register dengan voice BISA ✅
- [ ] Login dengan voice BISA ✅
- [ ] Voice transaction BISA ✅
- [ ] HTTPS aktif (gembok hijau)

---

## 📁 FILE PENTING

| File | Fungsi | Lokasi Upload |
|------|--------|---------------|
| `check_python_support.php` | Cek Python support | `public_html/` |
| `voice_processor.py` | Voice authentication | `laravel-app/scripts/` |
| `test_dependencies.py` | Test libraries | `laravel-app/scripts/` |
| `install_python_deps.sh` | Auto install libs | `laravel-app/scripts/` |

---

**INGAT:** Voice authentication BUTUH Python! Pastikan Python support OK sebelum upload!

**GOOD LUCK! 🚀**
