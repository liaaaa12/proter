# 📚 PANDUAN DEPLOYMENT VOICA KE CPANEL

## 🎯 OVERVIEW

Aplikasi Voica menggunakan **Python untuk voice authentication** (voice login & voice register). Untuk deploy ke cPanel kampus, Anda perlu memastikan server support Python dan libraries yang dibutuhkan.

---

## 📁 FILE-FILE DEPLOYMENT

Saya sudah buatkan beberapa file untuk membantu deployment:

### **1. `check_python_support.php`** ⭐ PALING PENTING!
**Fungsi:** Cek apakah cPanel support Python dan dependencies

**Cara pakai:**
1. Upload ke `public_html/check_python_support.php`
2. Akses via browser: `https://domain-anda.ac.id/check_python_support.php`
3. Screenshot hasilnya
4. Jika ada yang merah (❌), hubungi admin kampus

**Yang dicek:**
- ✅ shell_exec() enabled/disabled
- ✅ Python installation & version
- ✅ pip installation
- ✅ Python libraries (numpy, scipy, librosa)
- ✅ FFmpeg installation

---

### **2. `scripts/test_dependencies.py`**
**Fungsi:** Test apakah semua Python libraries terinstall dengan benar

**Cara pakai (via SSH):**
```bash
cd ~/laravel-app/scripts
python3 test_dependencies.py
```

**Output:**
```
✅ numpy       v1.24.0    - Numerical computing
✅ scipy       v1.10.0    - Scientific computing
✅ librosa     v0.10.0    - Audio processing
✅ voice_processor.py found and importable
```

---

### **3. `scripts/install_python_deps.sh`**
**Fungsi:** Otomatis install semua Python libraries yang dibutuhkan

**Cara pakai (via SSH):**
```bash
cd ~/laravel-app/scripts
bash install_python_deps.sh
```

**Atau manual:**
```bash
pip3 install --user numpy scipy librosa
```

---

### **4. `DEPLOYMENT_QUICK_GUIDE.md`** ⚡
**Fungsi:** Panduan singkat deployment (5 menit baca)

**Isi:**
- Langkah cepat deployment
- Troubleshooting umum
- Checklist sebelum demo

**Baca ini PERTAMA sebelum deploy!**

---

### **5. `.agent/workflows/deployment-cpanel-kampus.md`** 📖
**Fungsi:** Panduan lengkap deployment step-by-step

**Isi:**
- Setup Python di cPanel
- Upload & konfigurasi file
- Setup database
- Install Composer & Laravel
- Test voice features
- Troubleshooting detail
- Security & optimization

**Baca ini untuk panduan lengkap!**

---

## 🚀 LANGKAH DEPLOYMENT (RINGKAS)

### **STEP 1: CEK PYTHON SUPPORT** ⭐
```bash
# Upload check_python_support.php ke cPanel
# Akses via browser dan screenshot hasilnya
```

### **STEP 2: INSTALL PYTHON LIBRARIES**
```bash
# Via SSH
pip3 install --user numpy scipy librosa

# Atau pakai script
bash scripts/install_python_deps.sh
```

### **STEP 3: UPLOAD FILE**
```
/home/username/
├── laravel-app/          ← Upload SEMUA file Laravel
│   ├── scripts/          ← WAJIB ada voice_processor.py!
│   └── ...
└── public_html/          ← Upload ISI folder public/
    ├── index.php
    └── ...
```

### **STEP 4: KONFIGURASI**
```bash
# Edit public_html/index.php (ganti path)
# Edit laravel-app/.env (database & PYTHON_PATH)
```

### **STEP 5: SETUP LARAVEL**
```bash
cd ~/laravel-app
composer install --no-dev
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
chmod -R 775 storage bootstrap/cache
```

### **STEP 6: TEST VOICE**
```bash
# Test dependencies
python3 scripts/test_dependencies.py

# Test voice processor
python3 scripts/voice_processor.py enroll /tmp/test.wav
```

### **STEP 7: AKSES APLIKASI**
```
https://domain-kampus-anda.ac.id
```

---

## ⚠️ PERSYARATAN CPANEL

### **WAJIB ADA:**
- ✅ PHP 8.0+
- ✅ MySQL/MariaDB
- ✅ Composer
- ✅ **Python 3.8+** ← PENTING!
- ✅ **pip** ← PENTING!
- ✅ **shell_exec() enabled** ← PENTING!

### **PYTHON LIBRARIES:**
- ✅ numpy
- ✅ scipy
- ✅ librosa

### **OPSIONAL (tapi direkomendasikan):**
- ⚠️ FFmpeg (untuk konversi audio webm → wav)
- ⚠️ SSH access (untuk install libraries)

---

## 🆘 JIKA CPANEL TIDAK SUPPORT PYTHON

### **Opsi 1: Hubungi Admin Kampus**
Minta install:
- Python 3.8+
- pip
- Enable shell_exec()
- Libraries: numpy, scipy, librosa

**Email template ada di:** `deployment-cpanel-kampus.md`

### **Opsi 2: Pakai Microservice (Advanced)**
- Setup VPS kecil untuk Python API
- Laravel di cPanel kirim request ke VPS
- **Panduan ada di:** `deployment-cpanel-dengan-python.md`

### **Opsi 3: Disable Voice Auth (Fallback)**
- Voice login/register dimatikan
- Voice transaction tetap jalan (pakai PHP NLP)
- Paling mudah, tapi fitur berkurang

---

## 📊 FITUR VOICE YANG ADA

| Fitur | Butuh Python? | Status |
|-------|---------------|--------|
| Voice Transaction (input transaksi) | ❌ Tidak | ✅ Pakai PHP NLP |
| Voice Login | ✅ Ya | ⚠️ Butuh Python |
| Voice Register | ✅ Ya | ⚠️ Butuh Python |

**Kesimpulan:**
- Jika Python TIDAK support → Voice transaction tetap jalan
- Jika Python support → Semua fitur voice jalan

---

## 🔧 TROUBLESHOOTING

### **Error: "Python script tidak memberikan output"**
```bash
# Cek Python path
which python3

# Test manual
python3 ~/laravel-app/scripts/voice_processor.py enroll /tmp/test.wav 2>&1

# Update .env
PYTHON_PATH=/usr/bin/python3
```

### **Error: "ModuleNotFoundError: No module named 'librosa'"**
```bash
# Install libraries
pip3 install --user numpy scipy librosa

# Verify
python3 -c "import librosa; print('OK')"
```

### **Error: "shell_exec() disabled"**
**Solusi:** Hubungi admin kampus untuk enable di `php.ini`

### **Error 500**
```bash
# Cek logs
tail -50 ~/laravel-app/storage/logs/laravel.log

# Fix permissions
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache
```

---

## 📞 SUPPORT

### **Dokumentasi:**
1. **Quick Guide:** `DEPLOYMENT_QUICK_GUIDE.md` (baca ini dulu!)
2. **Panduan Lengkap:** `.agent/workflows/deployment-cpanel-kampus.md`
3. **Opsi Alternatif:** `.agent/workflows/deployment-cpanel-dengan-python.md`

### **Tools:**
1. **Checker:** `check_python_support.php`
2. **Test:** `scripts/test_dependencies.py`
3. **Installer:** `scripts/install_python_deps.sh`

### **Logs:**
```bash
# Laravel logs
tail -f ~/laravel-app/storage/logs/laravel.log

# PHP errors
tail -f ~/public_html/error_log
```

---

## ✅ CHECKLIST SEBELUM DEMO

- [ ] Python support OK (check_python_support.php hijau semua)
- [ ] Python libraries terinstall (test_dependencies.py OK)
- [ ] Database migrate berhasil
- [ ] Aplikasi bisa diakses via browser
- [ ] **Register dengan voice BISA** ✅
- [ ] **Login dengan voice BISA** ✅
- [ ] **Voice transaction BISA** ✅
- [ ] HTTPS aktif (gembok hijau di browser)
- [ ] Tidak ada error di console (F12)

---

## 🎓 CATATAN UNTUK MAHASISWA

**PENTING:**
1. **Cek Python support SEBELUM upload** - Jangan buang waktu upload kalau Python tidak support
2. **Screenshot semua hasil** - Untuk dokumentasi TA
3. **Backup database** - Sebelum migrate
4. **Test di local dulu** - Pastikan semua fitur jalan
5. **Siapkan plan B** - Jika Python tidak support, pakai Opsi 3 (disable voice auth)

**TIPS:**
- Hubungi admin kampus H-7 sebelum deadline
- Minta akses SSH untuk install libraries
- Siapkan dokumentasi error untuk troubleshooting
- Test voice features sebelum presentasi

---

## 📝 CHANGELOG

### **2025-12-04**
- ✅ Aktifkan kembali voice authentication di AuthController
- ✅ Buat check_python_support.php
- ✅ Buat test_dependencies.py
- ✅ Buat install_python_deps.sh
- ✅ Buat panduan deployment lengkap
- ✅ Buat quick guide

---

**GOOD LUCK DENGAN DEPLOYMENT! 🚀**

Jika ada pertanyaan, cek panduan lengkap di `.agent/workflows/deployment-cpanel-kampus.md`
