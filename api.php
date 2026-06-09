<?php
// api.php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'upload':
        handleUpload();
        break;
    case 'convert':
        handleConvert();
        break;
    case 'status':
        handleStatus();
        break;
    case 'cancel':
        handleCancel();
        break;
    case 'download':
        handleDownload();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleUpload() {
    if (!isset($_FILES['audio_file'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
        exit;
    }
    
    $file = $_FILES['audio_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $file['error']]);
        exit;
    }
    
    $taskId = 'task_' . bin2hex(random_bytes(8));
    $taskDir = __DIR__ . '/uploads/' . $taskId;
    
    if (!is_dir($taskDir)) {
        mkdir($taskDir, 0777, true);
    }
    
    $originalName = basename($file['name']);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $savedName = 'input.' . $extension;
    $targetPath = $taskDir . '/' . $savedName;
    
    // Auto-cleanup old tasks only when a new upload comes in
    cleanOldTasks();

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $meta = [
            'original_name' => $originalName,
            'saved_name' => $savedName,
            'size' => $file['size'],
            'uploaded_at' => time()
        ];
        file_put_contents($taskDir . '/meta.json', json_encode($meta));
        
        echo json_encode([
            'success' => true,
            'task_id' => $taskId,
            'filename' => $originalName,
            'size' => $file['size']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
    }
    exit;
}

function handleConvert() {
    $taskId = $_POST['task_id'] ?? '';
    $targetFormat = $_POST['format'] ?? 'mp3';
    $bitrate = $_POST['bitrate'] ?? 'auto';
    $channels = $_POST['channels'] ?? 'auto';
    $sampleRate = $_POST['sample_rate'] ?? 'auto';
    
    if (empty($taskId)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required.']);
        exit;
    }
    
    $taskDir = __DIR__ . '/uploads/' . $taskId;
    $metaFile = $taskDir . '/meta.json';
    
    if (!is_dir($taskDir) || !file_exists($metaFile)) {
        echo json_encode(['success' => false, 'message' => 'Task not found or expired.']);
        exit;
    }
    
    $meta = json_decode(file_get_contents($metaFile), true);
    $inputFile = $taskDir . '/' . $meta['saved_name'];
    
    require_once __DIR__ . '/ffmpeg_helper.php';
    $paths = getFFmpegPaths();
    $ffprobe = $paths['ffprobe'];
    
// --- PENYESUAIAN JALUR FFPROBE & FFMPEG (LINUX VS WINDOWS) ---
    // Jika berjalan di Linux (Railway), panggil langsung perintah globalnya
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $ffprobeExec = '"' . $ffprobe . '"';
        $ffmpegExec = '"' . $paths['ffmpeg'] . '"';
    } else {
        $ffprobeExec = 'ffprobe';
        $ffmpegExec = 'ffmpeg';
    }

    // Get audio duration using ffprobe
    $ffprobeCmd = $ffprobeExec . ' -i ' . escapeshellarg($inputFile) . ' -show_entries format=duration -v quiet -of csv="p=0"';
    $duration = shell_exec($ffprobeCmd);
    $duration = floatval(trim($duration));
    
    if ($duration <= 0) {
        // Fallback: try using ffmpeg info output if ffprobe fails or duration is invalid
        $ffmpegCmd = $ffmpegExec . ' -i ' . escapeshellarg($inputFile) . ' 2>&1';
        $ffmpegOutput = shell_exec($ffmpegCmd);
        if (preg_match('/Duration: (\d+):(\d+):(\d+\.\d+)/', $ffmpegOutput, $matches)) {
            $hours = intval($matches[1]);
            $minutes = intval($matches[2]);
            $seconds = floatval($matches[3]);
            $duration = ($hours * 3600) + ($minutes * 60) + $seconds;
        }
    }
    
    if ($duration <= 0) {
        $duration = 180; // fallback
    }
    
    $meta['duration'] = $duration;
    $meta['target_format'] = $targetFormat;
    file_put_contents($metaFile, json_encode($meta));
    
    // --- PENYESUAIAN BACKGROUND WORKER (LINUX VS WINDOWS) ---
    $phpBinary = PHP_BINARY;
    $workerScript = __DIR__ . '/convert_worker.php';
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Perintah latar belakang khusus WINDOWS (Localhost)
        $cmd = 'cmd /c start /B "" "' . $phpBinary . '" "' . $workerScript . '"'
            . ' ' . escapeshellarg($taskId)
            . ' ' . escapeshellarg($targetFormat)
            . ' ' . escapeshellarg($bitrate)
            . ' ' . escapeshellarg($channels)
            . ' ' . escapeshellarg($sampleRate);
        pclose(popen($cmd, 'r'));
    } else {
        // Perintah latar belakang khusus LINUX (Railway)
        // Menggunakan " > /dev/null 2>&1 &" agar script berjalan di background Linux
        $cmd = '"' . $phpBinary . '" "' . $workerScript . '"'
            . ' ' . escapeshellarg($taskId)
            . ' ' . escapeshellarg($targetFormat)
            . ' ' . escapeshellarg($bitrate)
            . ' ' . escapeshellarg($channels)
            . ' ' . escapeshellarg($sampleRate)
            . ' > /dev/null 2>&1 &';
        shell_exec($cmd);
    }
    
    echo json_encode(['success' => true, 'message' => 'Conversion process started.']);
    exit;
}

function handleStatus() {
    $taskId = $_GET['task_id'] ?? '';
    if (empty($taskId)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required.']);
        exit;
    }
    
    $taskDir = __DIR__ . '/uploads/' . $taskId;
    $metaFile = $taskDir . '/meta.json';
    $statusFile = $taskDir . '/status.json';
    $progressFile = $taskDir . '/progress.txt';
    
    if (!is_dir($taskDir) || !file_exists($metaFile)) {
        echo json_encode(['success' => false, 'message' => 'Task not found or expired.']);
        exit;
    }
    
    $meta = json_decode(file_get_contents($metaFile), true);
    $duration = $meta['duration'] ?? 180;
    
    // Check if finished (status.json exists)
    if (file_exists($statusFile)) {
        $status = json_decode(file_get_contents($statusFile), true);
        if ($status['status'] === 'success') {
            echo json_encode([
                'success' => true,
                'status' => 'success',
                'percent' => 100,
                'converted_size' => $status['converted_size']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'status' => 'error',
                'message' => $status['message'] ?? 'Unknown error occurred.'
            ]);
        }
        exit;
    }
    
    // If not finished, calculate percentage from progress.txt
    $percent = 0;
    $currentTime = 0;
    
    if (file_exists($progressFile)) {
        $progressContent = file_get_contents($progressFile);
        
        // First try out_time_us (microseconds) — most reliable FFmpeg progress field
        if (preg_match_all('/out_time_us=(\d+)/', $progressContent, $usMatches)) {
            $lastUs = intval(end($usMatches[1]));
            if ($lastUs > 0) {
                $currentTime = $lastUs / 1000000.0;
            }
        }
        
        // Fallback: try out_time string HH:MM:SS.xxx
        if ($currentTime <= 0 && preg_match_all('/out_time=([\d]+):([\d]+):([\d.]+)/', $progressContent, $matches)) {
            $lastIndex = count($matches[0]) - 1;
            $hours = intval($matches[1][$lastIndex]);
            $minutes = intval($matches[2][$lastIndex]);
            $seconds = floatval($matches[3][$lastIndex]);
            $currentTime = ($hours * 3600) + ($minutes * 60) + $seconds;
        }
        
        if ($currentTime > 0 && $duration > 0) {
            $percent = round(($currentTime / $duration) * 100);
            if ($percent > 99) {
                $percent = 99; // Cap at 99% until status.json confirms complete
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'status' => 'converting',
        'percent' => $percent,
        'current_time' => formatTime($currentTime),
        'total_time' => formatTime($duration)
    ]);
    exit;
}

function handleCancel() {
    $taskId = $_POST['task_id'] ?? '';
    if (empty($taskId)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required.']);
        exit;
    }
    
    $taskDir = __DIR__ . '/uploads/' . $taskId;
    $pidFile = $taskDir . '/pid.txt';
    
    if (is_dir($taskDir)) {
        if (file_exists($pidFile)) {
            $pid = intval(trim(file_get_contents($pidFile)));
            if ($pid > 0) {
                // Windows command to force kill process tree
                shell_exec("taskkill /F /T /PID " . $pid);
            }
        }
        
        // Clean up directory
        deleteDirectory($taskDir);
        echo json_encode(['success' => true, 'message' => 'Conversion cancelled and files cleaned up.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task directory not found.']);
    }
    exit;
}

function handleDownload() {
    $taskId = $_GET['task_id'] ?? '';
    if (empty($taskId)) {
        header("HTTP/1.1 400 Bad Request");
        echo "Task ID is required.";
        exit;
    }
    
    $taskDir = __DIR__ . '/uploads/' . $taskId;
    $metaFile = $taskDir . '/meta.json';
    
    if (!is_dir($taskDir) || !file_exists($metaFile)) {
        header("HTTP/1.1 404 Not Found");
        echo "Task not found or expired.";
        exit;
    }
    
    $meta = json_decode(file_get_contents($metaFile), true);
    $targetFormat = $meta['target_format'] ?? 'mp3';
    $convertedFile = $taskDir . '/converted.' . $targetFormat;
    
    if (!file_exists($convertedFile)) {
        header("HTTP/1.1 404 Not Found");
        echo "Converted file not found.";
        exit;
    }
    
    $originalName = pathinfo($meta['original_name'], PATHINFO_FILENAME);
    $downloadName = $originalName . '.' . $targetFormat;
    
    // Serve file
    header('Content-Description: File Transfer');
    header('Content-Type: audio/' . ($targetFormat === 'mp3' ? 'mpeg' : $targetFormat));
    header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($convertedFile));
    
    readfile($convertedFile);
    exit;
}

// Helpers
function formatTime($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = floor($seconds % 60);
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

function cleanOldTasks() {
    $uploadParent = __DIR__ . '/uploads';
    if (!is_dir($uploadParent)) {
        return;
    }
    $now = time();
    $expiry = 3600; // 1 hour
    
    foreach (scandir($uploadParent) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        $dir = $uploadParent . DIRECTORY_SEPARATOR . $item;
        if (is_dir($dir)) {
            $mtime = filemtime($dir);
            if (($now - $mtime) > $expiry) {
                deleteDirectory($dir);
            }
        }
    }
}
