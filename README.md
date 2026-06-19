# 🎵 AudioVibe — Advanced Audio Converter

Aplikasi konverter audio berbasis web yang powerful dengan antarmuka modern dan performa tinggi. Mendukung berbagai format audio populer langsung dari browser — tanpa perlu install aplikasi apapun.

## ✨ Fitur

- **Multiple Format** — Konversi antara MP3, WAV, FLAC, AAC, M4A, OGG, WMA, dan lainnya
- **Batch Processing** — Konversi file secara massal dengan antrian
- **Metadata Tagging** — Otomatis mengisi metadata (judul, artis, album, cover art)
- **Modern UI/UX** — Antarmuka responsif dan real-time progress bar berbasis AJAX
- **Cross-platform** — Berjalan di Windows, Linux, dan macOS

---

## 🚀 Cara Menjalankan di Komputer Sendiri

### Prasyarat

Sebelum mulai, pastikan sudah terinstall:
- **Git** — untuk clone repo
- **PHP 8.0+** — untuk menjalankan web server
- **FFmpeg** — untuk proses konversi audio

> Jika belum punya PHP atau FFmpeg, tenang — script `setup.sh` akan menginstall semuanya otomatis!

---

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/USERNAME/audiovibe-audioconverter.git
cd audiovibe-audioconverter
```

> Ganti `USERNAME` dengan username GitHub pemilik repo.

---

### Langkah 2 — Jalankan Setup (Install Dependensi)

```bash
bash setup.sh
```

Script ini akan otomatis:
- ✅ Mengecek apakah PHP sudah terinstall (skip jika sudah ada)
- ✅ Mengecek apakah FFmpeg sudah terinstall (skip jika sudah ada)
- ✅ Menginstall yang belum ada via `winget` (Windows) atau `apt/brew` (Linux/Mac)
- ✅ Membuat folder `uploads/` yang diperlukan

> **Windows:** Jalankan menggunakan **Git Bash** (bukan CMD atau PowerShell).
> **Linux/Mac:** Jalankan langsung di terminal.

---

### Langkah 3 — Jalankan Aplikasi

```bash
bash start.sh
```

Script ini akan:
- 🔍 Memverifikasi PHP dan FFmpeg tersedia
- 🌐 Menjalankan PHP built-in web server di `http://localhost:8080`
- 🖥️ Membuka browser secara otomatis
- 🔄 Otomatis ganti port jika 8080 sudah dipakai

**Tekan `Ctrl+C` untuk menghentikan server.**

---

## 🪟 Panduan Khusus Per Sistem Operasi

### Windows (via Git Bash)

```bash
# Install Git Bash terlebih dahulu dari: https://git-scm.com
# Lalu buka Git Bash dan jalankan:
bash setup.sh
bash start.sh
```

### Linux (Ubuntu/Debian)

```bash
chmod +x setup.sh start.sh
bash setup.sh
bash start.sh
```

### macOS

```bash
# Pastikan Homebrew sudah terinstall: https://brew.sh
chmod +x setup.sh start.sh
bash setup.sh
bash start.sh
```

---

## 📁 Struktur Proyek

```
audiovibe-audioconverter/
├── index.php           # Halaman utama (UI)
├── api.php             # Backend API handler
├── convert_worker.php  # Proses konversi background
├── ffmpeg_helper.php   # Deteksi path FFmpeg otomatis
├── setup.sh            # Script instalasi dependensi
├── start.sh            # Script menjalankan server
└── uploads/            # Folder temporary file (auto-dibuat)
```

---

## ❓ Troubleshooting

| Masalah | Solusi |
|---|---|
| `php: command not found` | Jalankan `bash setup.sh` terlebih dahulu |
| `ffmpeg: command not found` | Jalankan `bash setup.sh` terlebih dahulu |
| Port 8080 sudah dipakai | `start.sh` otomatis cari port lain (8081, 8082, dst.) |
| Browser tidak terbuka otomatis | Buka manual di `http://localhost:8080` |
| `Permission denied` saat setup | Di Linux/Mac: jalankan dengan `sudo bash setup.sh` |
| Konversi gagal / stuck | Pastikan FFmpeg terdeteksi: jalankan `ffmpeg -version` di terminal |

---

## 📄 Lisensi

MIT License — bebas digunakan dan dimodifikasi.
