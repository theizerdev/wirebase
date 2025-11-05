<?php

require_once __DIR__ . '/vendor/autoload.php';

$directory = 'app/Livewire';
$fixed = [];
$errors = [];

echo "🔧 Arreglando duplicación de traits HasDynamicLayout...\n\n";

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);

        // Buscar el patrón de duplicación
        $pattern = '/use App\\Traits\\HasDynamicLayout;\s*\n\s*use HasDynamicLayout;/';

        if (preg_match($pattern, $content)) {
            echo "📁 Arreglando duplicación en: $filePath\n";

            // Arreglar la duplicación
            $fixedContent = preg_replace(
                '/use App\\Traits\\HasDynamicLayout;\s*\n\s*use HasDynamicLayout;/',
                'use App\\Traits\\HasDynamicLayout;',
                $content
            );

            // Asegurar que la clase use el trait correctamente
            if (strpos($fixedContent, 'use HasDynamicLayout;') === false) {
                // Buscar la declaración de la clase
                $fixedContent = preg_replace(
                    '/class\s+\w+\s+extends\s+Component\s*\{([^}]*)\}/s',
                    'class $0 extends Component\n{\1\n    use HasDynamicLayout;',
                    $fixedContent
                );
            }

            // Guardar los cambios
            if (file_put_contents($filePath, $fixedContent)) {
                echo "   ✅ Duplicación arreglada\n";
                $fixed[] = $filePath;
            } else {
                echo "   ❌ Error al guardar cambios\n";
                $errors[] = $filePath;
            }
        }
    }
}

echo "\n📊 Resumen:\n";
echo "- Archivos arreglados: " . count($fixed) . "\n";
echo "- Errores: " . count($errors) . "\n\n";

if (count($fixed) > 0) {
    echo "✅ Archivos arreglados:\n";
    foreach ($fixed as $file) {
        echo "   - $file\n";
    }
}
