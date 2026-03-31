@echo off
TITLE Voica AI Microservice

echo =======================================================
echo          VOICA AI FASTAPI MICROSERVICE
echo =======================================================
echo.
echo Mengaktifkan Python Virtual Environment...

if exist ".venv\Scripts\activate.bat" (
    call .venv\Scripts\activate.bat
) else (
    echo [WARNING] Folder .venv tidak ditemukan. Pastikan Python environment Anda sudah diaktifkan secara manual.
)

echo.
echo Menginstall / memastikan FastAPI + Uvicorn tersedia...
python -m pip install --upgrade pip setuptools wheel -q
pip install -r scripts\requirements.txt

echo.
echo Menjalankan AI Server di port 8000...
echo Jangan tutup terminal ini selama aplikasi berjalan!
echo.

uvicorn scripts.api_server:app --host 127.0.0.1 --port 8000 --reload

pause
