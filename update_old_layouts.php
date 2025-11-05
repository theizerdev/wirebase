<?php

// Script para encontrar y actualizar componentes que usan el método antiguo

require_once __DIR__ . '/vendor/autoload.php';

$directory = __DIR__ . '/app/Livewire';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$updated = [];
$skipped = [];
$errors = [];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);

        // Verificar si el archivo usa HasDynamicLayout
        if (strpos($content, 'use HasDynamicLayout;') === false &&
            strpos($content, 'HasDynamicLayout') === false) {
            $skipped[] = $filePath . " (No usa HasDynamicLayout)";
            continue;
        }

        // Buscar el patrón antiguo: ->layout($this->getLayout())
        if (strpos($content, '->layout($this->getLayout())') !== false ||
            strpos($content, '->layout($this->getLayout(),') !== false) {

            try {
                // Buscar el patrón completo del método render
                if (preg_match('/return view\(([^,]+),\s*(.*?)\)\s*->layout\(\$this->getLayout\(\)(.*?)\);/s', $content, $matches)) {

                    $viewName = trim($matches[1], "'\"");
                    $viewData = trim($matches[2]);
                    $layoutData = trim($matches[3]);

                    // Extraer datos del layout
                    $title = '';
                    $breadcrumb = '';
                    if (preg_match("/'title'\s*=>\s*'([^']+)'/", $layoutData, $titleMatch)) {
                        $title = $titleMatch[1];
                    }
                    if (preg_match("/'breadcrumb'\s*=>\s*(\[[^\]]+\])", $layoutData, $breadcrumbMatch)) {
                        $breadcrumb = $breadcrumbMatch[1];
                    }

                    // Construir el nuevo código
                    $newRender = "return \$this->renderWithLayout('{$viewName}', {$viewData}, [\n";
                    if ($title) {
                        $newRender .= "            'title' => '{$title}',\n";
                    }
                    $newRender .= "            'description' => 'Gestión de {$title}',\n";
                    if ($breadcrumb) {
                        $newRender .= "            'breadcrumb' => {$breadcrumb},\n";
                    }
                    $newRender .= "        ]);";

                    // Reemplazar en el contenido
                    $oldPattern = '/return view\(([^,]+),\s*(.*?)\)\s*->layout\(\$this->getLayout\(\)(.*?)\);/s';
                    $newContent = preg_replace($oldPattern, $newRender, $content);

                    if ($newContent !== $content) {
                        file_put_contents($filePath, $newContent);
                        $updated[] = $filePath;
                    } else {
                        $errors[] = $filePath . " (No se pudo actualizar el patrón)";
                    }
                } else {
                    $errors[] = $filePath . " (Patrón no reconocido)";
                }
            } catch (Exception $e) {
                $errors[] = $filePath . " (Error: " . $e->getMessage() . ")";
            }
        } else {
            $skipped[] = $filePath . " (Ya usa renderWithLayout o no necesita actualización)";
        }
    }
}

// Mostrar resultados
echo "🔄 Actualización de Componentes HasDynamicLayout\n";
echo "================================================\n\n";

echo "✅ COMPONENTES ACTUALIZADOS (" . count($updated) . "):\n";
foreach ($updated as $file) {
    echo "   - " . str_replace(__DIR__, '', $file) . "\n";
}

echo "\n⏭️  COMPONENTES OMITIDOS (" . count($skipped) . "):\n";
foreach ($skipped as $file) {
    echo "   - " . str_replace(__DIR__, '', $file) . "\n";
}

echo "\n❌ ERRORES (" . count($errors) . "):\n";
foreach ($errors as $error) {
    echo "   - " . str_replace(__DIR__, '', $error) . "\n";
}

echo "\n📊 RESUMEN:\n";
echo "   - Actualizados: " . count($updated) . "\n";
echo "   - Omitidos: " . count($skipped) . "\n";
echo "   - Errores: " . count($errors) . "\n";

if (count($updated) > 0) {
    echo "\n✨ ¡Componentes actualizados exitosamente!\n";
} else {
    echo "\n✨ No se encontraron componentes para actualizar.\n";
}
