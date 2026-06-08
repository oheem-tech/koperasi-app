@echo off
REM =====================================================================
REM  Aplikasi Koperasi (CodeIgniter 4 + MariaDB) - One-Click Runner (Windows)
REM  Penggunaan: run.bat [up^|down^|restart^|status^|logs^|reset^|doctor^|help]
REM  Tanpa argumen = setup penuh + start.
REM =====================================================================
setlocal enabledelayedexpansion
cd /d "%~dp0"

if "%APP_PORT%"=="" set "APP_PORT=8088"
set "APP_URL=http://localhost:%APP_PORT%/"

set "CMD=%~1"
if "%CMD%"=="" set "CMD=up"

REM ---------- Cek prasyarat ----------
where docker >nul 2>&1
if errorlevel 1 (
  echo [ERROR] Docker belum terpasang. Install Docker Desktop dulu:
  echo         https://www.docker.com/products/docker-desktop/
  goto :end
)
docker info >nul 2>&1
if errorlevel 1 (
  echo [ERROR] Docker daemon tidak berjalan. Jalankan Docker Desktop, lalu coba lagi.
  goto :end
)

REM ---------- Deteksi compose ----------
set "COMPOSE=docker compose"
docker compose version >nul 2>&1
if errorlevel 1 (
  where docker-compose >nul 2>&1
  if errorlevel 1 (
    echo [ERROR] Docker Compose tidak ditemukan.
    goto :end
  )
  set "COMPOSE=docker-compose"
)

if /i "%CMD%"=="up"       goto :up
if /i "%CMD%"==""         goto :up
if /i "%CMD%"=="down"     goto :down
if /i "%CMD%"=="restart"  goto :restart
if /i "%CMD%"=="status"   goto :status
if /i "%CMD%"=="logs"     goto :logs
if /i "%CMD%"=="reset"    goto :reset
if /i "%CMD%"=="doctor"   goto :doctor
if /i "%CMD%"=="help"     goto :help
echo [ERROR] Perintah tidak dikenal: %CMD%
goto :help

:up
echo.
echo === BUILD ^& START ===
echo Membangun image ^& menjalankan container ...
%COMPOSE% up -d --build
if errorlevel 1 ( echo [ERROR] Gagal menjalankan container. Cek log: run.bat logs & goto :end )
echo Menunggu aplikasi siap di %APP_URL% ...
set /a tries=0
:waitloop
curl -fsS -o NUL "%APP_URL%" >nul 2>&1
if not errorlevel 1 goto :ready
set /a tries+=1
if %tries% GTR 60 ( echo [WARN] Aplikasi belum merespon. Cek log: run.bat logs & goto :summary )
timeout /t 2 /nobreak >nul
goto :waitloop
:ready
echo [OK] Aplikasi merespon.
:summary
echo.
echo === RINGKASAN ===
echo   URL Aplikasi : %APP_URL%
echo   Database     : MariaDB (host: localhost dari Docker)
echo.
echo   Akun Demo (Quick Login tersedia di halaman login):
echo     Admin   -^> username: admin     password: admin123
echo     Anggota -^> username: budi123   password: budi123
echo     Anggota -^> username: citra456  password: citra456
echo     Anggota -^> username: dewi789   password: dewi789
echo.
echo   Perintah: run.bat [status^|logs^|restart^|down^|reset^|doctor]
start "" "%APP_URL%"
goto :end

:down
echo === STOP ===
%COMPOSE% down
goto :end

:restart
echo === RESTART ===
%COMPOSE% restart
goto :end

:status
echo === STATUS ===
%COMPOSE% ps
goto :end

:logs
echo === LOGS (Ctrl+C untuk keluar) ===
%COMPOSE% logs -f --tail=120
goto :end

:reset
echo === RESET (HAPUS DATA) ===
echo [WARN] Ini akan MENGHAPUS database ^& volume (tidak bisa dikembalikan).
set /p ans="Ketik YA untuk lanjut: "
if /i "!ans!"=="YA" (
  %COMPOSE% down -v
  goto :up
) else (
  echo Dibatalkan.
)
goto :end

:doctor
echo === DOCTOR ===
docker --version
docker info >nul 2>&1 && echo [OK] docker daemon berjalan || echo [ERROR] docker daemon mati
%COMPOSE% version
echo Port aplikasi: %APP_PORT%
goto :end

:help
echo.
echo Aplikasi Koperasi - One-Click Runner (Windows)
echo.
echo   run.bat            Setup penuh: build + start + migrasi + seed + buka browser
echo   run.bat up         Sama seperti default
echo   run.bat down       Hentikan semua container
echo   run.bat restart    Restart container
echo   run.bat status     Lihat status container
echo   run.bat logs       Lihat log realtime
echo   run.bat reset      Hapus total data (volume) lalu setup ulang
echo   run.bat doctor     Cek prasyarat ^& port
echo   run.bat help       Tampilkan bantuan ini
echo.
echo   Variabel opsional: APP_PORT (default 8088), SEED_DEMO=0 untuk tanpa data demo.
goto :end

:end
echo.
pause
endlocal
