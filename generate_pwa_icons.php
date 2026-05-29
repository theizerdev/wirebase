<?php

// Script para generar iconos PWA desde el logo existente

$logoPath = 'public/logo/1719430882.png';
$outputDir = 'public/pwa-icons/';

// Crear directorio si no existe
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Verificar que GD está disponible
if (!extension_loaded('gd')) {
    echo "Error: La extensión GD no está instalada\n";
    exit(1);
}

// Cargar imagen original
$source = imagecreatefrompng($logoPath);
if (!$source) {
    echo "Error: No se pudo cargar la imagen\n";
    exit(1);
}

// Dimensiones originales
$origWidth = imagesx($source);
$origHeight = imagesy($source);

echo "Logo original: {$origWidth}x{$origHeight}\n";

// Función para redimensionar a tamaño cuadrado
function createSquareIcon($source, $size, $outputPath) {
    // Crear imagen cuadrada con fondo blanco
    $square = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($square, 255, 255, 255);
    imagefill($square, 0, 0, $white);
    
    // Calcular dimensiones para centrar el logo
    $origWidth = imagesx($source);
    $origHeight = imagesy($source);
    
    // Determinar el tamaño más pequeño para mantener proporción
    $scale = min($size / $origWidth, $size / $origHeight);
    $newWidth = $origWidth * $scale;
    $newHeight = $origHeight * $scale;
    
    // Calcular posición para centrar
    $x = ($size - $newWidth) / 2;
    $y = ($size - $newHeight) / 2;
    
    // Copiar y redimensionar
    imagecopyresampled(
        $square, $source,
        $x, $y, 0, 0,
        $newWidth, $newHeight,
        $origWidth, $origHeight
    );
    
    // Guardar
    imagepng($square, $outputPath);
    imagedestroy($square);
    
    echo "✓ Icono {$size}x{$size} creado: {$outputPath}\n";
}

// Generar iconos PWA estándar
$sizes = [192, 512];

foreach ($sizes as $size) {
    $outputPath = $outputDir . "icon-{$size}x{$size}.png";
    createSquareIcon($source, $size, $outputPath);
}

// Liberar memoria
imagedestroy($source);

echo "\n✅ Iconos PWA generados exitosamente!\n";
echo "📁 Ubicación: {$outputDir}\n";
echo "\nAhora actualiza el manifest.json para usar estos iconos.\n";

?>
