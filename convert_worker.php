<?php
// convert_worker.php
// CLI script, takes arguments: task_id, target_format, bitrate, channels, sample_rate
if (php_sapi_name() !== 'cli') {
    die("Only command line execution is allowed.");
}

$taskId = $argv[1] ?? '';
if (empty($taskId)) {
    die("Task ID required.");
}

$targetFormat = $argv[2] ?? 'mp3';
$bitrate = $argv[3] ?? '';
$channels = $argv[4] ?? '';
$sampleRate = $argv[5] ?? '';

$taskDir = __DIR__ . '/uploads/' . $taskId;
if (!is_dir($taskDir)) {
    die("Task directory not found.");
}

// Write PID immediately for cancellation support
file_put_contents($taskDir . '/pid.txt', getmypid());

require_once __DIR__ . '/ffmpeg_helper.php';
$paths = getFFmpegPaths();
$ffmpeg = $paths['ffmpeg'];

// Load metadata to find input file name
$metaFile = $taskDir . '/meta.json';
if (!file_exists($metaFile)) {
    die("Meta file not found.");
}
$meta = json_decode(file_get_contents($metaFile), true);
$inputFile = $taskDir . '/' . $meta['saved_name'];
$outputFile = $taskDir . '/converted.' . $targetFormat;

// Build FFmpeg command options
$options = [];

// Bitrate options
if (!empty($bitrate) && $bitrate !== 'auto') {
    $options[] = '-ab ' . escapeshellarg($bitrate);
}

// Channels options
if (!empty($channels) && $channels !== 'auto') {
    $options[] = '-ac ' . escapeshellarg($channels === 'stereo' ? '2' : '1');
}

// Sample rate options
if (!empty($sampleRate) && $sampleRate !== 'auto') {
    $options[] = '-ar ' . escapeshellarg($sampleRate);
}

// Format specific codecs
switch ($targetFormat) {
    case 'mp3':
        $options[] = '-codec:a libmp3lame';
        break;
    case 'ogg':
        $options[] = '-codec:a libvorbis';
        break;
    case 'wav':
        $options[] = '-codec:a pcm_s16le';
        break;
    case 'aac':
    case 'm4a':
        $options[] = '-codec:a aac';
        break;
    case 'flac':
        $options[] = '-codec:a flac';
        break;
    case 'wma':
        $options[] = '-codec:a wmav2';
        break;
}

$optionsStr = implode(' ', $options);
$progressLog = $taskDir . '/progress.txt';

// Command execution
// Wrap paths in quotes to support spaces in directories
$cmd = '"' . $ffmpeg . '" -y -i ' . escapeshellarg($inputFile) . ' -progress ' . escapeshellarg($progressLog) . ' ' . $optionsStr . ' ' . escapeshellarg($outputFile);

$logFile = $taskDir . '/worker_log.txt';
file_put_contents($logFile, "Command: " . $cmd . "\n\n", FILE_APPEND);

// Run FFmpeg synchronously inside this background worker process
exec($cmd . " 2>> " . escapeshellarg($logFile), $output, $returnCode);

if ($returnCode === 0 && file_exists($outputFile)) {
    // Save success status
    $status = [
        'status' => 'success',
        'converted_file' => 'converted.' . $targetFormat,
        'converted_size' => filesize($outputFile)
    ];
    file_put_contents($taskDir . '/status.json', json_encode($status));
    
    // Clean up input file to save disk space
    if (file_exists($inputFile)) {
        unlink($inputFile);
    }
} else {
    // Save error status
    $status = [
        'status' => 'error',
        'message' => 'FFmpeg conversion failed. Check logs for details.'
    ];
    file_put_contents($taskDir . '/status.json', json_encode($status));
}
