#!/usr/bin/env bash
# =============================================================================
#  AudioVibe — start.sh
#  Menjalankan PHP built-in web server dan otomatis membuka browser
#  Kompatibel: Git Bash / MSYS2 di Windows
# =============================================================================

set -e

# ── Warna terminal ────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# ── Banner ────────────────────────────────────────────────────────────────────
echo ""
echo -e "${CYAN}${BOLD}"
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   🎵  AudioVibe — Start Server  🎵   ║"
echo "  ╚═══════════════════════════════════════╝"
echo -e "${NC}"

OK()   { echo -e "  ${GREEN}✔${NC}  $1"; }
INFO() { echo -e "  ${CYAN}ℹ${NC}  $1"; }
WARN() { echo -e "  ${YELLOW}⚠${NC}  $1"; }
FAIL() { echo -e "  ${RED}✖${NC}  $1"; }
STEP() { echo -e "\n${BOLD}──────────────────────────────────────────${NC}"; echo -e "  ${CYAN}▶${NC}  ${BOLD}$1${NC}"; }

# ── Konfigurasi ───────────────────────────────────────────────────────────────
HOST="localhost"
PORT=8080
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
URL="http://$HOST:$PORT"

# ── Deteksi OS ────────────────────────────────────────────────────────────────
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" || "$OSTYPE" == "win32" ]]; then
    OS="windows"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="linux"
elif [[ "$OSTYPE" == "darwin"* ]]; then
    OS="mac"
else
    OS="linux"
fi

# ── Helper ────────────────────────────────────────────────────────────────────
command_exists() {
    command -v "$1" &>/dev/null
}

# =============================================================================
#  STEP 1 — VERIFIKASI PHP
# =============================================================================
STEP "Memeriksa PHP..."

if ! command_exists php; then
    FAIL "PHP tidak ditemukan!"
    echo ""
    echo -e "  ${YELLOW}Silakan jalankan setup terlebih dahulu:${NC}"
    echo -e "    ${BOLD}bash setup.sh${NC}"
    echo ""
    exit 1
fi

PHP_VER=$(php -r "echo PHP_VERSION;" 2>/dev/null)
OK "PHP ditemukan (versi $PHP_VER)"

# =============================================================================
#  STEP 2 — VERIFIKASI FFMPEG
# =============================================================================
STEP "Memeriksa FFmpeg..."

if ! command_exists ffmpeg; then
    WARN "FFmpeg tidak ditemukan di PATH."
    WARN "Konversi audio mungkin tidak berjalan."
    echo -e "  ${YELLOW}Jalankan 'bash setup.sh' untuk menginstall FFmpeg.${NC}"
else
    FFMPEG_VER=$(ffmpeg -version 2>/dev/null | head -1 | awk '{print $3}')
    OK "FFmpeg ditemukan (versi $FFMPEG_VER)"
fi

# =============================================================================
#  STEP 3 — CEK PORT KOSONG
# =============================================================================
STEP "Memeriksa ketersediaan port $PORT..."

port_in_use() {
    if command_exists netstat; then
        netstat -an 2>/dev/null | grep -q ":$1 "
    elif command_exists ss; then
        ss -tlnp 2>/dev/null | grep -q ":$1 "
    else
        return 1  # tidak bisa cek, anggap kosong
    fi
}

if port_in_use "$PORT"; then
    WARN "Port $PORT sudah digunakan, mencoba port alternatif..."
    for ALT_PORT in 8081 8082 8083 3000 5000; do
        if ! port_in_use "$ALT_PORT"; then
            PORT=$ALT_PORT
            URL="http://$HOST:$PORT"
            OK "Port $PORT tersedia, menggunakan port ini."
            break
        fi
    done
    if port_in_use "$PORT"; then
        FAIL "Semua port alternatif juga digunakan. Hentikan proses lain dan coba lagi."
        exit 1
    fi
else
    OK "Port $PORT tersedia."
fi

# =============================================================================
#  STEP 4 — BUAT FOLDER UPLOADS (jika belum ada)
# =============================================================================
if [[ ! -d "$SCRIPT_DIR/uploads" ]]; then
    mkdir -p "$SCRIPT_DIR/uploads"
    OK "Folder uploads dibuat."
fi

# =============================================================================
#  STEP 5 — JALANKAN PHP WEB SERVER
# =============================================================================
STEP "Menjalankan PHP web server..."

echo ""
echo -e "  ${GREEN}${BOLD}Server berjalan di: ${URL}${NC}"
echo -e "  ${CYAN}Root direktori   : $SCRIPT_DIR${NC}"
echo -e "  ${YELLOW}Tekan Ctrl+C untuk menghentikan server.${NC}"
echo ""

# Fungsi buka browser berdasarkan OS
open_browser() {
    local target_url="$1"
    INFO "Membuka browser: $target_url"
    sleep 1.5   # tunggu server siap

    if [[ "$OS" == "windows" ]]; then
        # Git Bash di Windows: pakai cmd.exe start
        start "" "$target_url" 2>/dev/null || \
        cmd.exe /c start "" "$target_url" 2>/dev/null || \
        WARN "Tidak bisa membuka browser otomatis. Buka manual: $target_url"
    elif [[ "$OS" == "mac" ]]; then
        open "$target_url" 2>/dev/null || \
        WARN "Tidak bisa membuka browser. Buka manual: $target_url"
    else
        # Linux
        if command_exists xdg-open; then
            xdg-open "$target_url" 2>/dev/null &
        elif command_exists gnome-open; then
            gnome-open "$target_url" 2>/dev/null &
        else
            WARN "Tidak bisa membuka browser otomatis. Buka manual: $target_url"
        fi
    fi
}

# Jalankan buka-browser di background
open_browser "$URL" &

# Jalankan PHP built-in server (foreground — Ctrl+C untuk stop)
echo -e "${BOLD}──────────────────────────────────────────${NC}"
php -S "$HOST:$PORT" -t "$SCRIPT_DIR"
