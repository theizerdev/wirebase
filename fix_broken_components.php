<?php

require_once __DIR__ . '/vendor/autoload.php';

$directory = 'app/Livewire';
$fixed = [];
$errors = [];
$checked = 0;

echo "🔧 Verificando componentes Livewire con problemas...\n\n";

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);
        $checked++;

        // Verificar si es un componente Livewire
        if (strpos($content, 'class') !== false && strpos($content, 'extends Component') !== false) {

            // Verificar si tiene el trait importado pero no usado
            $hasImport = strpos($content, 'use App\Traits\HasDynamicLayout;') !== false;
            $hasTraitUsage = strpos($content, 'use HasDynamicLayout;') !== false;
            $hasRenderMethod = strpos($content, 'public function render()') !== false;

            if ($hasImport && !$hasTraitUsage && $hasRenderMethod) {
                echo "📁 Encontrado problema en: $filePath\n";

                // Obtener el contenido actual del método render
                preg_match('/public function render\(\)\s*\{([^}]+)\}/s', $content, $renderMatches);

                if (isset($renderMatches[1])) {
                    $renderContent = trim($renderMatches[1]);
                    echo "   Contenido actual de render(): $renderContent\n";

                    // Verificar si el render está roto (solo retorna string)
                    if (preg_match('/return\s+[\'"]([^\'"]+)[\'"];/', $renderContent, $viewMatches)) {
                        $viewName = $viewMatches[1];
                        echo "   Vista detectada: $viewName\n";

                        // Arreglar el componente
                        $newContent = str_replace(
                            'use App\Traits\HasDynamicLayout;',
                            'use App\Traits\HasDynamicLayout;' . "\n" . '    use HasDynamicLayout;',
                            $content
                        );

                        // Reemplazar el método render
                        $newRenderMethod = <<<PHP
public function render()
    {
        return view('$viewName', [
            'user' => \$this->user ?? null,
            'sessions' => \$sessions ?? null
        ])->layout(\$this->getLayout(), [
            'title' => 'Detalles del Usuario'
        ]);
    }
PHP;

                        // Para componentes que no son Users, ajustar el método render
                        if (strpos($filePath, 'Users') === false) {
                            $className = basename(dirname($filePath));
                            $newRenderMethod = <<<PHP
public function render()
    {
        return view('$viewName')->layout(\$this->getLayout());
    }
PHP;
                        }

                        $newContent = preg_replace(
                            '/public function render\(\)\s*\{[^}]+\}/s',
                            $newRenderMethod,
                            $newContent
                        );

                        // Guardar los cambios
                        if (file_put_contents($filePath, $newContent)) {
                            echo "   ✅ Componente arreglado\n";
                            $fixed[] = $filePath;
                        } else {
                            echo "   ❌ Error al guardar cambios\n";
                            $errors[] = $filePath;
                        }
                    }
                }
                echo "\n";
            }
        }
    }
}

echo "\n📊 Resumen:\n";
echo "- Componentes verificados: $checked\n";
echo "- Componentes arreglados: " . count($fixed) . "\n";
echo "- Errores: " . count($errors) . "\n\n";

if (count($fixed) > 0) {
    echo "✅ Componentes arreglados:\n";
    foreach ($fixed as $component) {
        echo "   - $component\n";
    }
}

if (count($errors) > 0) {
    echo "❌ Componentes con errores:\n";
    foreach ($errors as $component) {
        echo "   - $component\n";
    }
}
