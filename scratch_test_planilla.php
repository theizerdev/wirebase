<?php
// Scan vendor/codedge/ for $path variable
function scan_dir($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                if (strpos($content, '$path') !== false) {
                    $results[] = $path;
                }
            }
        } else if ($value != "." && $value != "..") {
            scan_dir($path, $results);
        }
    }
    return $results;
}

$results = [];
$dir = __DIR__ . '/vendor/codedge';
if (is_dir($dir)) {
    scan_dir($dir, $results);
}

echo "Files containing \$path in vendor/codedge:\n";
foreach ($results as $r) {
    echo "- " . str_replace(__DIR__, '', $r) . "\n";
    $lines = file($r);
    foreach ($lines as $i => $line) {
        if (strpos($line, '$path') !== false) {
            echo "  Line " . ($i + 1) . ": " . trim($line) . "\n";
        }
    }
}
