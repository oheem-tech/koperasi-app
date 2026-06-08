#!/usr/bin/env bash
# =====================================================================
#  Entrypoint Aplikasi Koperasi — auto config, migrate, & seed
# =====================================================================
set -e

APP_DIR=/var/www/html
ENV_FILE="$APP_DIR/.env"

# --- Default value (bisa di-override lewat environment compose) ---
: "${CI_ENVIRONMENT:=production}"
: "${APP_BASE_URL:=http://localhost:8088/}"
: "${DB_HOST:=db}"
: "${DB_PORT:=3306}"
: "${DB_NAME:=koperasi_db}"
: "${DB_USER:=koperasi}"
: "${DB_PASS:=koperasi}"
: "${SEED_DEMO:=1}"

echo "==> [1/4] Membuat file .env ..."
cat > "$ENV_FILE" <<EOF
#--- Dibuat otomatis oleh Docker entrypoint ---
CI_ENVIRONMENT = ${CI_ENVIRONMENT}

app.baseURL = '${APP_BASE_URL}'
app.indexPage = ''
app.forceGlobalSecureRequests = false

database.default.hostname = ${DB_HOST}
database.default.database = ${DB_NAME}
database.default.username = ${DB_USER}
database.default.password = '${DB_PASS}'
database.default.DBDriver = MySQLi
database.default.port     = ${DB_PORT}
EOF
chown www-data:www-data "$ENV_FILE" 2>/dev/null || true

echo "==> [2/4] Menunggu database ${DB_HOST}:${DB_PORT} siap ..."
tries=0
until mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" --skip-ssl --silent >/dev/null 2>&1; do
    tries=$((tries+1))
    if [ "$tries" -gt 60 ]; then
        echo "!! Database tidak merespon setelah 60 percobaan. Berhenti." >&2
        exit 1
    fi
    sleep 2
done
echo "    Database siap."

echo "==> [3/4] Menjalankan migrasi database ..."
php spark migrate --all || php spark migrate

echo "==> [4/4] Memeriksa data awal (seeder) ..."
USER_COUNT=$(php -r '
    $h=getenv("DB_HOST");$p=getenv("DB_PORT");$d=getenv("DB_NAME");$u=getenv("DB_USER");$pw=getenv("DB_PASS");
    $m=@mysqli_connect($h,$u,$pw,$d,(int)$p);
    if(!$m){echo "0";exit;}
    $r=@mysqli_query($m,"SELECT COUNT(*) c FROM users");
    if(!$r){echo "0";exit;}
    echo mysqli_fetch_assoc($r)["c"];
' 2>/dev/null || echo "0")

if [ "$USER_COUNT" = "0" ]; then
    echo "    Database kosong → menjalankan AdminSeeder ..."
    php spark db:seed AdminSeeder
    if [ "$SEED_DEMO" = "1" ]; then
        echo "    Menjalankan DemoSeeder (data demo) ..."
        php spark db:seed DemoSeeder || true
    fi
else
    echo "    Data sudah ada (${USER_COUNT} user) → seeder dilewati."
fi

# Pastikan hak akses writable tetap benar
chown -R www-data:www-data "$APP_DIR/writable" 2>/dev/null || true

echo "==> Aplikasi siap. Menjalankan Apache ..."
exec "$@"
