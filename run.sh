#!/usr/bin/env bash
# =====================================================================
#  Aplikasi Koperasi (CodeIgniter 4 + MariaDB) — One-Click Runner
#  Penggunaan: ./run.sh [up|down|restart|status|logs|reset|doctor|help]
#  Tanpa argumen = setup penuh + start.
# =====================================================================
set -euo pipefail
cd "$(dirname "$0")"

# ---------- Warna (hanya jika TTY) ----------
if [ -t 1 ]; then
  R=$'\e[31m'; G=$'\e[32m'; Y=$'\e[33m'; B=$'\e[34m'; C=$'\e[36m'; W=$'\e[1m'; N=$'\e[0m'
else
  R=""; G=""; Y=""; B=""; C=""; W=""; N=""
fi
info()  { echo "${C}➜${N} $*"; }
ok()    { echo "${G}✔${N} $*"; }
warn()  { echo "${Y}⚠${N} $*"; }
err()   { echo "${R}✖${N} $*" >&2; }
title() { echo; echo "${W}${B}=== $* ===${N}"; }

APP_PORT="${APP_PORT:-8088}"
APP_URL="http://localhost:${APP_PORT}/"

# ---------- Deteksi docker compose ----------
detect_compose() {
  if docker compose version >/dev/null 2>&1; then COMPOSE="docker compose";
  elif command -v docker-compose >/dev/null 2>&1; then COMPOSE="docker-compose";
  else err "Docker Compose tidak ditemukan."; exit 1; fi
}

# ---------- Cek prasyarat ----------
check_prereq() {
  if ! command -v docker >/dev/null 2>&1; then
    err "Docker belum terpasang. Install Docker Desktop dulu: https://www.docker.com/products/docker-desktop/"
    exit 1
  fi
  if ! docker info >/dev/null 2>&1; then
    err "Docker daemon tidak berjalan. Jalankan Docker Desktop, lalu coba lagi."
    exit 1
  fi
  detect_compose
}

# ---------- Tunggu HTTP siap ----------
wait_http() {
  info "Menunggu aplikasi siap di ${APP_URL} ..."
  local tries=0
  until curl -fsS -o /dev/null "${APP_URL}" 2>/dev/null; do
    tries=$((tries+1))
    if [ "$tries" -gt 60 ]; then
      warn "Aplikasi belum merespon setelah 120 detik. Cek log: ./run.sh logs"
      return 1
    fi
    sleep 2
  done
  ok "Aplikasi merespon."
}

summary() {
  title "RINGKASAN"
  echo "  ${W}URL Aplikasi :${N} ${G}${APP_URL}${N}"
  echo "  ${W}Database     :${N} MariaDB (host: localhost:${DB_EXPOSE_PORT:-33061} dari host)"
  echo
  echo "  ${W}Akun Demo (Quick Login tersedia di halaman login):${N}"
  echo "    ${C}Admin  ${N} → username: ${W}admin${N}     password: ${W}admin123${N}"
  echo "    ${C}Anggota${N} → username: ${W}budi123${N}   password: ${W}budi123${N}"
  echo "    ${C}Anggota${N} → username: ${W}citra456${N}  password: ${W}citra456${N}"
  echo "    ${C}Anggota${N} → username: ${W}dewi789${N}   password: ${W}dewi789${N}"
  echo
  echo "  ${W}Perintah:${N} ./run.sh [status|logs|restart|down|reset|doctor]"
}

cmd_up() {
  check_prereq
  title "BUILD & START"
  info "Membangun image & menjalankan container (mode detach) ..."
  $COMPOSE up -d --build
  ok "Container berjalan."
  wait_http || true
  summary
  info "Membuka di browser ... (jika tidak terbuka, akses manual ${APP_URL})"
  ( command -v xdg-open >/dev/null && xdg-open "${APP_URL}" ) >/dev/null 2>&1 || \
  ( command -v open >/dev/null && open "${APP_URL}" ) >/dev/null 2>&1 || true
}

cmd_down()    { check_prereq; title "STOP"; $COMPOSE down; ok "Semua container dihentikan."; }
cmd_restart() { check_prereq; title "RESTART"; $COMPOSE restart; ok "Restart selesai."; wait_http || true; summary; }
cmd_status()  { check_prereq; title "STATUS"; $COMPOSE ps; }
cmd_logs()    { check_prereq; title "LOGS (Ctrl+C untuk keluar)"; $COMPOSE logs -f --tail=120; }

cmd_reset() {
  check_prereq
  title "RESET (HAPUS DATA)"
  warn "Ini akan MENGHAPUS database & volume (data tidak bisa dikembalikan)."
  read -r -p "Ketik 'YA' untuk lanjut: " ans
  if [ "$ans" = "YA" ]; then
    $COMPOSE down -v
    ok "Volume dihapus. Menjalankan ulang dari awal ..."
    cmd_up
  else
    info "Dibatalkan."
  fi
}

cmd_doctor() {
  title "DOCTOR"
  command -v docker >/dev/null 2>&1 && ok "docker terpasang ($(docker --version))" || err "docker tidak ada"
  docker info >/dev/null 2>&1 && ok "docker daemon berjalan" || err "docker daemon mati"
  (docker compose version >/dev/null 2>&1 && ok "docker compose tersedia") || \
    (command -v docker-compose >/dev/null 2>&1 && ok "docker-compose tersedia") || err "compose tidak ada"
  if command -v lsof >/dev/null 2>&1 && lsof -i :"${APP_PORT}" >/dev/null 2>&1; then
    warn "Port ${APP_PORT} sedang dipakai proses lain. Ubah APP_PORT bila perlu (APP_PORT=8090 ./run.sh)."
  else
    ok "Port ${APP_PORT} bebas."
  fi
}

cmd_help() {
  cat <<EOF
${W}Aplikasi Koperasi — One-Click Runner${N}

  ./run.sh            Setup penuh: build + start + migrasi + seed + buka browser
  ./run.sh up         Sama seperti default
  ./run.sh down       Hentikan semua container
  ./run.sh restart    Restart container
  ./run.sh status     Lihat status container
  ./run.sh logs       Lihat log realtime
  ./run.sh reset      Hapus total data (volume) lalu setup ulang
  ./run.sh doctor     Cek prasyarat & port
  ./run.sh help       Tampilkan bantuan ini

  Variabel opsional: APP_PORT (default 8088), SEED_DEMO=0 untuk tanpa data demo.
EOF
}

case "${1:-up}" in
  up|"")    cmd_up ;;
  down)     cmd_down ;;
  restart)  cmd_restart ;;
  status)   cmd_status ;;
  logs)     cmd_logs ;;
  reset|hard-reset) cmd_reset ;;
  doctor)   cmd_doctor ;;
  help|-h|--help) cmd_help ;;
  *) err "Perintah tidak dikenal: $1"; cmd_help; exit 1 ;;
esac
