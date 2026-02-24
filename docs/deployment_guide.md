# Panduan Deployment cPanel (Laravel + Python)

Dokumen ini menjelaskan langkah-langkah untuk mendeploy aplikasi "Proter" ke cPanel.

## 1. Persiapan Repository (GitHub)

Sebelum melakukan push, jalankan perintah ini di lokal untuk membersihkan file GSD dari index Git (file tetap aman di komputer Anda):

```bash
# Menghapus file GSD dari index agar tidak masuk GitHub
git rm -r --cached .planning/

# Tambahkan model AI ke tracking (karena sebelumnya diabaikan atau belum masuk)
git add pretrained_models/

# Commit dan Push
git commit -m "chore: exclude GSD and include AI models for update"
git push
```

## 2. Struktur Folder di cPanel

Disarankan untuk meletakkan file Laravel di luar folder `public_html` demi keamanan:

```text
/home/username/
├── proter_app/         <-- (Isi folder project diletakkan di sini)
│   ├── app/
│   ├── scripts/
│   ├── pretrained_models/
│   └── ...
└── public_html/        <-- (Hanya isi dari folder 'public' project)
    ├── index.php
    └── assets/
```

## 3. Setup Python di cPanel

1. Masuk ke cPanel > **Setup Python App**.
2. Klik **Create Application**.
3. Pilih **Python Version** (disarankan 3.10+).
4. **Application root**: `proter_app` (atau folder tempat script berada).
5. **Application URL**: (Bisa diisi domain/subdomain).
6. Setelah aplikasi dibuat, masuk ke terminal cPanel dan jalankan perintah yang muncul di panel Python App (biasanya `source .../bin/activate`).
7. Update pip: `pip install --upgrade pip`
8. Install dependencies: `pip install -r requirements.txt`

## 4. Konfigurasi .env (Laravel)

Pastikan path script Python di `.env` sudah benar:

```env
PYTHON_EXEC=/home/username/nodevenv/proter_app/.../bin/python
VOICE_PROCESSOR_SCRIPT=scripts/voice_processor_ecapa.py
VOICE_MODELS_PATH=pretrained_models
```

## 5. Symlink Public

Jangan lupa menghubungkan folder `public` ke `public_html`:

```bash
ln -s /home/username/proter_app/public /home/username/public_html
```

_(Atau arahkan Document Root subdomain ke folder public)_
