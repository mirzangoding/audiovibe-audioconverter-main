<?php
require_once 'ffmpeg_helper.php';
$paths = getFFmpegPaths();
echo 'FFmpeg: ' . $paths['ffmpeg'] . PHP_EOL;
echo 'FFprobe: ' . $paths['ffprobe'] . PHP_EOL;
echo 'PHP Binary: ' . PHP_BINARY . PHP_EOL;
echo 'FFmpeg exists: ' . (file_exists($paths['ffmpeg']) ? 'YES' : 'NO') . PHP_EOL;
echo 'FFprobe exists: ' . (file_exists($paths['ffprobe']) ? 'YES' : 'NO') . PHP_EOL;

// Test the background launch command format
$phpBinary = PHP_BINARY;
$workerScript = __DIR__ . '/convert_worker.php';
$cmd = 'cmd /c start /B "" "' . $phpBinary . '" "' . $workerScript . '" test_id mp3 auto auto auto';
echo 'CMD: ' . $cmd . PHP_EOL;
