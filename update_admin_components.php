<?php

/**
 * Script para actualizar todos los componentes de Livewire en App\Livewire\Admin
 * para que utilicen el trait HasDynamicLayout
 */

$basePath = __DIR__ . '/app/Livewire/Admin';
$updatedFiles = [];
$skippedFiles = [];
$errors = [];

function updateComponentFile($filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Verificar si ya usa HasDynamicLayout
    if (strpos($content, 'use App\Traits\HasDynamicLayout;') !== false) {
        return 'already_has_trait';
    }

    // Verificar si es un componente de Livewire
    if (strpos($content, 'extends Component') === false) {
        return 'not_a_component';
    }

    // Paso 1: Agregar el use statement
    // Buscar el namespace para insertar después
    if (preg_match('/namespace App\\Livewire\\Admin;/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPos = $matches[0][1] + strlen($matches[0][0]);
        $content = substr_replace($content, "\nuse App\\Traits\\HasDynamicLayout;", $insertPos, 0);
    } else {
        return 'namespace_not_found';
    }

    // Paso 2: Agregar el trait al array de use
    // Buscar la línea con los traits
    if (preg_match('/use [^;]*;/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        // Encontrar todas las líneas de traits
        preg_match_all('/use [^;]*;/', $content, $allMatches, PREG_OFFSET_CAPTURE);
        $lastTraitLine = end($allMatches[0]);

        $traitContent = $lastTraitLine[0];
        if (strpos($traitContent, 'HasDynamicLayout') === false) {
            // Agregar HasDynamicLayout al final
            $newTraitContent = rtrim($traitContent, ';') . ', HasDynamicLayout;';
            $content = str_replace($traitContent, $newTraitContent, $content);
        }
    } else {
        return 'traits_not_found';
    }

    // Paso 3: Actualizar el método render si existe
    if (preg_match('/public function render\(\)[\s\n]*{[\s\n]*return view\([^)]+\)([^;]*);/', $content, $renderMatches)) {
        $oldRender = $renderMatches[0];

        // Verificar si ya usa layout dinámico
        if (strpos($oldRender, '->layout(') !== false) {
            // Reemplazar layout estático por dinámico
            $newRender = preg_replace(
                '/->layout\([^)]+\)/',
                '->layout($this->getLayout())',
                $oldRender
            );
        } else {
            // Agregar layout dinámico
            $newRender = str_replace(
                $renderMatches[1],
                $renderMatches[1] . '->layout($this->getLayout())',
                $oldRender
            );
        }

        $content = str_replace($oldRender, $newRender, $content);
    }

    // Guardar los cambios
    if (file_put_contents($filePath, $content) !== false) {
        return 'updated';
    } else {
        return 'write_error';
    }
}

function processDirectory($dir) {
    global $updatedFiles, $skippedFiles, $errors;

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $fullPath = $dir . '/' . $file;

        if (is_dir($fullPath)) {
            // Recursivamente procesar subdirectorios
            processDirectory($fullPath);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Procesar archivo PHP
            $result = updateComponentFile($fullPath);

            switch ($result) {
                case 'updated':
                    $updatedFiles[] = str_replace(__DIR__, '', $fullPath);
                    break;
                case 'already_has_trait':
                    $skippedFiles[] = str_replace(__DIR__, '', $fullPath) . ' (ya tiene HasDynamicLayout)';
                    break;
                case 'not_a_component':
                    $skippedFiles[] = str_replace(__DIR__, '', $fullPath) . ' (no es componente Livewire)';
                    break;
                case 'namespace_not_found':
                    $errors[] = str_replace(__DIR__, '', $fullPath) . ' (no se encontró namespace)';
                    break;
                case 'traits_not_found':
                    $errors[] = str_replace(__DIR__, '', $fullPath) . ' (no se encontró declaración de traits)';
                    break;
                case 'write_error':
                    $errors[] = str_replace(__DIR__, '', $fullPath) . ' (error al escribir archivo)';
                    break;
            }
        }
    }
}

echo "🔄 Iniciando actualización de componentes de Admin...\n\n";

// Procesar el directorio de Admin
processDirectory($basePath);

echo "✅ Proceso completado!\n\n";

echo "📊 RESUMEN:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Archivos actualizados: " . count($updatedFiles) . "\n";
echo "⏭️  Archivos omitidos: " . count($skippedFiles) . "\n";
echo "❌ Errores: " . count($errors) . "\n\n";

if (!empty($updatedFiles)) {
    echo "📁 ARCHIVOS ACTUALIZADOS:\n";
    foreach ($updatedFiles as $file) {
        echo "   ✅ $file\n";
    }
    echo "\n";
}

if (!empty($skippedFiles)) {
    echo "📁 ARCHIVOS OMITIDOS:\n";
    foreach ($skippedFiles as $file) {
        echo "   ⏭️  $file\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORES:\n";
    foreach ($errors as $error) {
        echo "   ❌ $error\n";
    }
    echo "\n";
}

echo "💡 NOTA: Los componentes ahora usarán layouts dinámicos según la configuración de plantilla.\n";
echo "   El layout se actualizará automáticamente cuando cambie la configuración.\n";
