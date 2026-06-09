<?php
// ffmpeg_helper.php (Sudah diperbaiki untuk Windows + Linux Railway)

function getFFmpegPaths() {
    // Jika berjalan di server Linux (Railway/Render)
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        return [
            'ffmpeg' => 'ffmpeg',
            'ffprobe' => 'ffprobe'
        ];
    }

    // --- DI BAWAH INI ADALAH LOGIKA ASLI KHUSUS UNTUK LAPTOP KAMU (WINDOWS) ---
    $ffmpeg = 'ffmpeg';
    $ffprobe = 'ffprobe';

    // 1. Try standard command line detection (Windows Style)
    $ffmpeg_where = shell_exec('where ffmpeg 2>nul');
    if ($ffmpeg_where) {
        $ffmpeg = trim(explode("\n", $ffmpeg_where)[0]);
    }
    
    $ffprobe_where = shell_exec('where ffprobe 2>nul');
    if ($ffprobe_where) {
        $ffprobe = trim(explode("\n", $ffprobe_where)[0]);
    }

    // 2. Check WinGet package path for Gyan.FFmpeg
    if ($ffmpeg === 'ffmpeg' || $ffprobe === 'ffprobe') {
        $wingetDirs = glob('C:/Users/*/AppData/Local/Microsoft/WinGet/Packages/Gyan.FFmpeg_*/ffmpeg-*/bin');
        if (!empty($wingetDirs)) {
            $binDir = $wingetDirs[0];
            if ($ffmpeg === 'ffmpeg' && file_exists("$binDir/ffmpeg.exe")) {
                $ffmpeg = "$binDir/ffmpeg.exe";
            }
            if ($ffprobe === 'ffprobe' && file_exists("$binDir/ffprobe.exe")) {
                $ffprobe = "$binDir/ffprobe.exe";
            }
        }
    }

    return [
        'ffmpeg' => $ffmpeg,
        'ffprobe' => $ffprobe
    ];
}