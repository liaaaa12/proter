# Cara Mengatasi Error 419 (CSRF Token Mismatch)

## ❌ Error yang Muncul:
```
POST http://127.0.0.1:8000/api/parse-voice-text 419 (unknown status)
```

## 🔍 Penyebab:
Error **419** terjadi karena **CSRF token tidak valid** atau **session expired**.

---

## ✅ SOLUSI CEPAT:

### **1. Hard Refresh Browser** (PALING PENTING!)
Tekan salah satu kombinasi ini:
- **Windows**: `Ctrl + Shift + R` atau `Ctrl + F5`
- **Mac**: `Cmd + Shift + R`

Ini akan memaksa browser untuk reload halaman dan mengambil file terbaru (termasuk CSRF token yang baru).

### **2. Clear Browser Cache**
Jika hard refresh tidak berhasil:

**Chrome/Edge:**
1. Tekan `F12` untuk buka DevTools
2. Klik kanan pada tombol refresh di browser
3. Pilih **"Empty Cache and Hard Reload"**

**Firefox:**
1. Tekan `Ctrl + Shift + Delete`
2. Pilih "Cached Web Content"
3. Klik "Clear Now"

### **3. Pastikan CSRF Token Ada**
Buka console browser (F12) dan ketik:
```javascript
document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
```

**Hasil yang benar:**
- Akan menampilkan string panjang seperti: `"abc123def456..."`

**Jika hasilnya `null` atau `undefined`:**
- Berarti meta tag CSRF belum ter-load
- Lakukan hard refresh (Ctrl+F5)

---

## 🔧 Jika Masih Error Setelah Hard Refresh:

### **Langkah 1: Clear Laravel Cache**
Jalankan command ini di terminal:
```bash
cd c:\xampp\htdocs\voica
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **Langkah 2: Restart Apache**
- Buka XAMPP Control Panel
- Stop Apache
- Start Apache lagi

### **Langkah 3: Cek File Session**
Pastikan folder `storage/framework/sessions` ada dan writable:
```bash
# Cek apakah folder ada
dir storage\framework\sessions

# Jika tidak ada, buat folder
mkdir storage\framework\sessions
```

### **Langkah 4: Cek .env File**
Buka file `.env` dan pastikan:
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

## 🧪 Test CSRF Token Secara Manual:

Buka console browser (F12) dan jalankan:

```javascript
// Test 1: Cek apakah CSRF token ada
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
console.log('CSRF Token:', token ? 'FOUND ✅' : 'NOT FOUND ❌');

// Test 2: Test API dengan CSRF token
fetch('/api/parse-voice-text', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
    },
    body: JSON.stringify({ text: 'test beli makan 10000' })
})
.then(response => {
    console.log('Status:', response.status);
    if (response.status === 419) {
        console.log('❌ CSRF ERROR - Token tidak valid atau session expired');
    } else if (response.status === 200) {
        console.log('✅ SUCCESS - CSRF token valid!');
    }
    return response.json();
})
.then(data => console.log('Response:', data))
.catch(err => console.error('Error:', err));
```

---

## 📋 Checklist Troubleshooting:

- [ ] Hard refresh browser (Ctrl+F5)
- [ ] Clear browser cache
- [ ] Cek CSRF token di console (harus ada)
- [ ] Clear Laravel cache (artisan cache:clear)
- [ ] Restart Apache di XAMPP
- [ ] Cek folder storage/framework/sessions ada
- [ ] Cek .env SESSION_DRIVER=file
- [ ] Test manual di console browser

---

## 💡 Penjelasan Teknis:

**CSRF (Cross-Site Request Forgery) Token** adalah security token yang:
1. Di-generate oleh Laravel setiap kali halaman di-load
2. Disimpan di session
3. Harus dikirim bersama setiap POST request
4. Divalidasi oleh Laravel untuk memastikan request berasal dari aplikasi yang sah

**Error 419** terjadi ketika:
- Token tidak dikirim
- Token tidak cocok dengan yang ada di session
- Session sudah expired
- Browser menggunakan cached page lama (yang punya token lama)

---

## ✅ Setelah Perbaikan:

Setelah hard refresh, coba lagi:
1. Klik tombol voice 🎤
2. Ucapkan: "pengeluaran 10000 untuk makan"
3. Lihat console, seharusnya muncul:
```
CSRF Token: Found
Sending text to API: pengeluaran 10000 untuk makan
Response status: 200
API Response: {success: true, ...}
```

Jika masih error, screenshot console dan kirimkan untuk bantuan lebih lanjut!
