# Panduan Debugging Fitur Voice

## Error "undefined" yang Muncul

Jika Anda melihat error "❌ undefined", ini berarti ada masalah dengan response dari API. Berikut cara mengeceknya:

## Langkah Debugging:

### 1. Buka Browser Console
- Tekan `F12` atau `Ctrl+Shift+I` di browser
- Pilih tab **Console**

### 2. Test Fitur Voice
- Klik tombol "Tekan Untuk Bersuara"
- Ucapkan sesuatu, contoh: "Beli makan 50 ribu"
- Lihat log di console

### 3. Periksa Log Console
Anda akan melihat log seperti ini:

```
🎤 Mulai berbicara...
Speech recognized: beli makan 50 ribu Confidence: 0.95
Sending text to API: beli makan 50 ribu
Response status: 200
API Response: {success: true, data: {...}, raw_text: "..."}
```

### 4. Kemungkinan Masalah:

#### A. CSRF Token Missing
Jika muncul error **419** atau **CSRF token mismatch**:
- Pastikan ada tag `<meta name="csrf-token">` di layout
- Cek file: `resources/views/layouts/app.blade.php`
- Harus ada baris ini di dalam `<head>`:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### B. Route Tidak Ditemukan (404)
Jika response status **404**:
- Periksa file `routes/web.php`
- Pastikan route ini ada:
```php
Route::post('/api/parse-voice-text', [VoiceTransactionController::class, 'parseVoiceText'])
    ->name('voice.parse.text');
Route::post('/api/voice-transaction', [VoiceTransactionController::class, 'store'])
    ->name('voice.transaction.store');
```

#### C. Server Error (500)
Jika response status **500**:
- Buka file log Laravel: `storage/logs/laravel.log`
- Lihat error terakhir
- Biasanya masalah di database atau validation

#### D. Response Tidak Sesuai Format
Jika `result.success` undefined:
- Periksa response di console log
- Pastikan controller mengembalikan JSON dengan format:
```json
{
  "success": true/false,
  "message": "...",
  "data": {...}
}
```

## Perbaikan yang Sudah Dilakukan:

✅ Menghapus kode HTML yang rusak di dalam JavaScript
✅ Memperbaiki URL API dari `/api/voice-transaction/store` ke `/api/voice-transaction`
✅ Standardisasi error response menggunakan `message` bukan `error`
✅ Menambahkan console.log untuk debugging
✅ Menambahkan filter `user_id` di semua query
✅ Menambahkan fallback message jika response tidak ada message

## Testing Manual:

### Test 1: Parsing Voice Text
Buka browser console dan jalankan:
```javascript
fetch('/api/parse-voice-text', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
    },
    body: JSON.stringify({ text: 'beli makan 50 ribu' })
})
.then(r => r.json())
.then(data => console.log(data));
```

Expected output:
```json
{
  "success": true,
  "data": {
    "jenis": "Pengeluaran",
    "kategori": "Makanan",
    "jumlah": 50000,
    "keterangan": "Beli makan 50 ribu"
  },
  "raw_text": "beli makan 50 ribu"
}
```

### Test 2: Save Transaction
```javascript
fetch('/api/voice-transaction', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        jenis: 'Pengeluaran',
        kategori: 'Makanan',
        jumlah: 50000,
        keterangan: 'Test transaksi',
        budget_id: null,
        goal_id: null
    })
})
.then(r => r.json())
.then(data => console.log(data));
```

Expected output:
```json
{
  "success": true,
  "message": "Transaksi berhasil disimpan",
  "data": {
    "transaction_id": 123,
    "jenis": "Pengeluaran",
    "kategori": "Makanan",
    "jumlah": 50000
  }
}
```

## Jika Masih Error:

1. **Clear cache Laravel:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

2. **Restart server:**
- Jika pakai XAMPP: restart Apache
- Jika pakai `php artisan serve`: stop dan start lagi

3. **Periksa permissions:**
- Folder `storage` harus writable
- Folder `bootstrap/cache` harus writable

4. **Periksa database:**
- Pastikan tabel `transaction`, `budget`, `goals` ada
- Pastikan kolom `user_id` ada di semua tabel

## Kontak untuk Bantuan:
Jika masih ada masalah, kirimkan:
1. Screenshot console log (F12)
2. Screenshot error yang muncul
3. Isi file `storage/logs/laravel.log` (bagian error terakhir)
