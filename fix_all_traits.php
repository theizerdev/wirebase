<?php

function fixComponent($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }

    $content = file_get_contents($file_path);

    // Verificar si tiene el problema de duplicación
    if (strpos($content, 'use App\Traits\HasDynamicLayout;') !== false &&
        strpos($content, '    use HasDynamicLayout;') !== false) {

        // Arreglar la duplicación
        $fixedContent = str_replace(
            "use App\Traits\HasDynamicLayout;\n    use HasDynamicLayout;",
            "use App\Traits\HasDynamicLayout;",
            $content
        );

        // Asegurar que la clase use el trait
        if (strpos($fixedContent, 'use HasDynamicLayout;') === false) {
            // Insertar el trait después de la declaración de clase
            $fixedContent = preg_replace(
                '/(class\s+\w+\s+extends\s+Component\s*\{)/',
                '$1' . "\n    use HasDynamicLayout;",
                $fixedContent
            );
        }

        // Guardar los cambios
        if (file_put_contents($file_path, $fixedContent)) {
            return true;
        }
    }

    return false;
}

function findAndFixAllComponents($directory) {
    $fixed = [];
    $errors = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $file_path = $file->getPathname();

            // Verificar si es un componente Livewire
            $content = file_get_contents($file_path);
            if (strpos($content, 'Livewire\Component') !== false) {
                if (fixComponent($file_path)) {
                    $fixed[] = $file_path;
                }
            }
        }
    }

    return ['fixed' => $fixed, 'errors' => $errors];
}

echo "🔧 Buscando y arreglando todos los componentes con problemas...\n\n";

$result = findAndFixAllComponents('app/Livewire');

echo "📊 Resumen:\n";
echo "- Componentes arreglados: " . count($result['fixed']) . "\n";
echo "- Errores: " . count($result['errors']) . "\n\n";

if (count($result['fixed']) > 0) {
    echo "✅ Componentes arreglados:\n";
    foreach ($result['fixed'] as $component) {
        echo "   - " . str_replace('c:\\laragon\\www\\larawire\\', '', $component) . "\n";
    }
}
