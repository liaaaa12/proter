<?php
/**
 * Script untuk cek apakah cPanel support Python
 * Upload file ini ke public_html dan akses via browser
 */

echo "<h1>🔍 Pengecekan Python Support di cPanel</h1>";
echo "<hr>";

// 1. Cek apakah shell_exec enabled
echo "<h2>1. Cek shell_exec()</h2>";
if (function_exists('shell_exec')) {
    echo "✅ <strong>shell_exec() ENABLED</strong><br>";
} else {
    echo "❌ <strong>shell_exec() DISABLED</strong> - Voice auth TIDAK BISA jalan!<br>";
    echo "Hubungi admin kampus untuk enable shell_exec()<br>";
}

echo "<hr>";

// 2. Cek Python installation
echo "<h2>2. Cek Python Installation</h2>";

$pythonCommands = ['python3', 'python', 'python3.9', 'python3.8'];
$pythonFound = false;
$pythonPath = null;

foreach ($pythonCommands as $cmd) {
    $output = shell_exec("which $cmd 2>&1");
    if ($output && trim($output) != '') {
        echo "✅ <strong>$cmd ditemukan:</strong> " . trim($output) . "<br>";
        if (!$pythonFound) {
            $pythonPath = trim($output);
            $pythonFound = true;
        }
    }
}

if (!$pythonFound) {
    echo "❌ <strong>Python TIDAK ditemukan!</strong><br>";
    echo "Voice auth TIDAK BISA jalan tanpa Python.<br>";
}

echo "<hr>";

// 3. Cek Python version
if ($pythonFound) {
    echo "<h2>3. Cek Python Version</h2>";
    $version = shell_exec("$pythonPath --version 2>&1");
    echo "📌 Version: <strong>" . trim($version) . "</strong><br>";
    
    echo "<hr>";
    
    // 4. Cek pip
    echo "<h2>4. Cek pip (Package Manager)</h2>";
    $pipCommands = ['pip3', 'pip'];
    $pipFound = false;
    
    foreach ($pipCommands as $pip) {
        $output = shell_exec("which $pip 2>&1");
        if ($output && trim($output) != '') {
            echo "✅ <strong>$pip ditemukan:</strong> " . trim($output) . "<br>";
            $pipFound = true;
            break;
        }
    }
    
    if (!$pipFound) {
        echo "❌ <strong>pip TIDAK ditemukan!</strong><br>";
    }
    
    echo "<hr>";
    
    // 5. Cek Python libraries yang dibutuhkan
    echo "<h2>5. Cek Python Libraries</h2>";
    $libraries = ['numpy', 'scipy', 'librosa'];
    
    foreach ($libraries as $lib) {
        $check = shell_exec("$pythonPath -c 'import $lib; print($lib.__version__)' 2>&1");
        if (strpos($check, 'ModuleNotFoundError') !== false || strpos($check, 'ImportError') !== false) {
            echo "❌ <strong>$lib:</strong> TIDAK terinstall<br>";
        } else {
            echo "✅ <strong>$lib:</strong> " . trim($check) . "<br>";
        }
    }
    
    echo "<hr>";
    
    // 6. Cek FFmpeg
    echo "<h2>6. Cek FFmpeg (untuk konversi audio)</h2>";
    $ffmpeg = shell_exec("which ffmpeg 2>&1");
    if ($ffmpeg && trim($ffmpeg) != '') {
        echo "✅ <strong>FFmpeg ditemukan:</strong> " . trim($ffmpeg) . "<br>";
        $ffmpegVersion = shell_exec("ffmpeg -version 2>&1 | head -n 1");
        echo "📌 Version: " . trim($ffmpegVersion) . "<br>";
    } else {
        echo "❌ <strong>FFmpeg TIDAK ditemukan!</strong><br>";
        echo "Diperlukan untuk konversi audio webm → wav<br>";
    }
}

echo "<hr>";

// 7. Kesimpulan
echo "<h2>📊 KESIMPULAN</h2>";

if (!function_exists('shell_exec')) {
    echo "❌ <strong>TIDAK BISA</strong> - shell_exec() disabled<br>";
    echo "<strong>Solusi:</strong> Hubungi admin kampus untuk enable shell_exec() di php.ini<br>";
} elseif (!$pythonFound) {
    echo "❌ <strong>TIDAK BISA</strong> - Python tidak terinstall<br>";
    echo "<strong>Solusi:</strong> Hubungi admin kampus untuk install Python 3<br>";
} else {
    echo "✅ <strong>MUNGKIN BISA!</strong><br>";
    echo "<strong>Langkah selanjutnya:</strong><br>";
    echo "1. Install Python libraries (numpy, scipy, librosa)<br>";
    echo "2. Install FFmpeg jika belum ada<br>";
    echo "3. Test voice_processor.py script<br>";
    echo "<br>";
    echo "<strong>Python Path untuk .env:</strong><br>";
    echo "<code>PYTHON_PATH=$pythonPath</code><br>";
}

echo "<hr>";
echo "<p><em>Simpan hasil ini dan kirim ke developer untuk analisis lebih lanjut.</em></p>";
?>
