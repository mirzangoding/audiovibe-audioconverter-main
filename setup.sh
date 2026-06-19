#!/usr/bin/env bash
# =============================================================================
#  AudioVibe — setup.sh
#  Otomatis menginstall semua dependensi yang dibutuhkan (PHP + FFmpeg)
#  Jalankan sekali sebelum pertama kali pakai.
#  Kompatibel: Git Bash / MSYS2 di Windows
# =============================================================================

set -e

# ── Warna terminal ────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# ── Banner ────────────────────────────────────────────────────────────────────
echo ""
echo -e "${CYAN}${BOLD}"
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   🎵  AudioVibe — Setup Script  🎵   ║"
echo "  ╚═══════════════════════════════════════╝"
echo -e "${NC}"

OK()   { echo -e "  ${GREEN}✔${NC}  $1"; }
INFO() { echo -e "  ${CYAN}ℹ${NC}  $1"; }
WARN() { echo -e "  ${YELLOW}⚠${NC}  $1"; }
FAIL() { echo -e "  ${RED}✖${NC}  $1"; }
STEP() { echo -e "\n${BOLD}──────────────────────────────────────────${NC}"; echo -e "  ${CYAN}▶${NC}  ${BOLD}$1${NC}"; }

# ── Deteksi OS ────────────────────────────────────────────────────────────────
STEP "Mendeteksi sistem operasi..."
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" || "$OSTYPE" == "win32" ]]; then
    OS="windows"
    OK "Terdeteksi: Windows (Git Bash / MSYS2)"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="linux"
    OK "Terdeteksi: Linux"
elif [[ "$OSTYPE" == "darwin"* ]]; then
    OS="mac"
    OK "Terdeteksi: macOS"
else
    WARN "OS tidak dikenal ($OSTYPE), mencoba lanjut sebagai Linux..."
    OS="linux"
fi

# ── Helper: cek apakah command ada ───────────────────────────────────────────
command_exists() {
    command -v "$1" &>/dev/null
}

# =============================================================================
#  STEP 1 — CEK & INSTALL PHP
# =============================================================================
STEP "Mengecek PHP..."

if command_exists php; then
    PHP_VER=$(php -r "echo PHP_VERSION;" 2>/dev/null)
    OK "PHP sudah terinstall (versi $PHP_VER) — dilewati."
else
    WARN "PHP belum terinstall. Memulai instalasi..."

    if [[ "$OS" == "windows" ]]; then
        # Coba via winget (Windows Package Manager)
        if command_exists winget; then
            INFO "Menginstall PHP via winget..."
            winget install --id PHP.PHP -e --accept-source-agreements --accept-package-agreements
            # Refresh PATH di sesi ini
            export PATH="$PATH:/c/tools/php:/c/php:/c/xampp/php"
        # Coba via Chocolatey
        elif command_exists choco; then
            INFO "Menginstall PHP via Chocolatey..."
            choco install php -y
            export PATH="$PATH:/c/tools/php:/c/ProgramData/chocolatey/bin"
        else
            FAIL "Winget dan Chocolatey tidak ditemukan."
            echo ""
            echo -e "  ${YELLOW}Silakan install PHP secara manual:${NC}"
            echo "    1. Download dari: https://windows.php.net/download/"
            echo "    2. Ekstrak ke C:\\php"
            echo "    3. Tambahkan C:\\php ke System PATH"
            echo ""
            exit 1
        fi

        # Verifikasi ulang
        if command_exists php; then
            PHP_VER=$(php -r "echo PHP_VERSION;" 2>/dev/null)
            OK "PHP berhasil diinstall (versi $PHP_VER)"
        else
            FAIL "PHP masih tidak terdeteksi setelah instalasi."
            WARN "Coba restart terminal dan jalankan setup.sh lagi."
            exit 1
        fi

    elif [[ "$OS" == "linux" ]]; then
        if command_exists apt-get; then
            sudo apt-get update -qq && sudo apt-get install -y php php-cli php-common
        elif command_exists dnf; then
            sudo dnf install -y php-cli
        elif command_exists pacman; then
            sudo pacman -Sy --noconfirm php
        else
            FAIL "Package manager tidak dikenali. Install PHP secara manual."
            exit 1
        fi
        OK "PHP berhasil diinstall."

    elif [[ "$OS" == "mac" ]]; then
        if command_exists brew; then
            brew install php
            OK "PHP berhasil diinstall via Homebrew."
        else
            FAIL "Homebrew tidak ditemukan. Install dari https://brew.sh lalu ulangi."
            exit 1
        fi
    fi
fi

# =============================================================================
#  STEP 2 — CEK & INSTALL FFMPEG
# =============================================================================
STEP "Mengecek FFmpeg..."

if command_exists ffmpeg; then
    FFMPEG_VER=$(ffmpeg -version 2>/dev/null | head -1 | awk '{print $3}')
    OK "FFmpeg sudah terinstall (versi $FFMPEG_VER) — dilewati."
else
    WARN "FFmpeg belum terinstall. Memulai instalasi..."

    if [[ "$OS" == "windows" ]]; then
        if command_exists winget; then
            INFO "Menginstall FFmpeg via winget (Gyan.FFmpeg)..."
            winget install --id Gyan.FFmpeg -e --accept-source-agreements --accept-package-agreements
            # Coba refresh PATH umum
            FFMPEG_DIRS=(
                "/c/Users/$USERNAME/AppData/Local/Microsoft/WinGet/Packages/Gyan.FFmpeg"*"/bin"
                "/c/ffmpeg/bin"
                "/c/Program Files/ffmpeg/bin"
            )
            for dir in "${FFMPEG_DIRS[@]}"; do
                if [[ -d "$dir" ]]; then
                    export PATH="$PATH:$dir"
                    break
                fi
            done
        elif command_exists choco; then
            INFO "Menginstall FFmpeg via Chocolatey..."
            choco install ffmpeg -y
            export PATH="$PATH:/c/ProgramData/chocolatey/bin"
        else
            FAIL "Winget dan Chocolatey tidak ditemukan."
            echo ""
            echo -e "  ${YELLOW}Silakan install FFmpeg secara manual:${NC}"
            echo "    1. Download dari: https://www.gyan.dev/ffmpeg/builds/"
            echo "    2. Ekstrak ke C:\\ffmpeg"
            echo "    3. Tambahkan C:\\ffmpeg\\bin ke System PATH"
            echo ""
            exit 1
        fi

        if command_exists ffmpeg; then
            FFMPEG_VER=$(ffmpeg -version 2>/dev/null | head -1 | awk '{print $3}')
            OK "FFmpeg berhasil diinstall (versi $FFMPEG_VER)"
        else
            WARN "FFmpeg mungkin perlu restart terminal agar PATH aktif."
            WARN "Coba tutup dan buka kembali terminal, lalu jalankan start.sh"
        fi

    elif [[ "$OS" == "linux" ]]; then
        if command_exists apt-get; then
            sudo apt-get update -qq && sudo apt-get install -y ffmpeg
        elif command_exists dnf; then
            sudo dnf install -y ffmpeg
        elif command_exists pacman; then
            sudo pacman -Sy --noconfirm ffmpeg
        else
            FAIL "Package manager tidak dikenali. Install FFmpeg secara manual."
            exit 1
        fi
        OK "FFmpeg berhasil diinstall."

    elif [[ "$OS" == "mac" ]]; then
        if command_exists brew; then
            brew install ffmpeg
            OK "FFmpeg berhasil diinstall via Homebrew."
        else
            FAIL "Homebrew tidak ditemukan. Install dari https://brew.sh lalu ulangi."
            exit 1
        fi
    fi
fi

# =============================================================================
#  STEP 3 — BUAT FOLDER UPLOADS (jika belum ada)
# =============================================================================
STEP "Menyiapkan folder uploads..."

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
UPLOADS_DIR="$SCRIPT_DIR/uploads"

if [[ -d "$UPLOADS_DIR" ]]; then
    OK "Folder uploads sudah ada — dilewati."
else
    mkdir -p "$UPLOADS_DIR"
    OK "Folder uploads berhasil dibuat."
fi

# =============================================================================
#  SELESAI
# =============================================================================
echo ""
echo -e "${GREEN}${BOLD}"
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   ✅  Setup Selesai! Semua siap.     ║"
echo "  ╚═══════════════════════════════════════╝"
echo -e "${NC}"
echo -e "  Sekarang jalankan: ${CYAN}${BOLD}bash start.sh${NC}"
echo ""
