<?php

$files_to_fix = [
    'app/Livewire/Admin/Sucursales/Create.php',
    'app/Livewire/Admin/Sucursales/Edit.php',
    'app/Livewire/Admin/Sucursales/Show.php',
    'app/Livewire/Admin/Users/Create.php',
    'app/Livewire/Admin/Users/Edit.php',
    'app/Livewire/Admin/Users/Show.php',
    'app/Livewire/Admin/Students/Show.php',
    'app/Livewire/Admin/NivelesEducativos/Create.php',
    'app/Livewire/Admin/NivelesEducativos/Edit.php',
    'app/Livewire/Admin/NivelesEducativos/Show.php',
    'app/Livewire/Admin/Turnos/Create.php',
    'app/Livewire/Admin/Turnos/Edit.php',
    'app/Livewire/Admin/Turnos/Show.php',
    'app/Livewire/Admin/SchoolPeriods/Create.php',
    'app/Livewire/Admin/SchoolPeriods/Edit.php',
    'app/Livewire/Admin/SchoolPeriods/Show.php',
    'app/Livewire/Admin/Series/Create.php',
    'app/Livewire/Admin/Series/Edit.php',
    'app/Livewire/Admin/Programas/Create.php',
    'app/Livewire/Admin/Programas/Edit.php',
    'app/Livewire/Admin/Programas/Show.php',
    'app/Livewire/Admin/Permissions/Create.php',
    'app/Livewire/Admin/Permissions/Edit.php',
    'app/Livewire/Admin/Roles/Create.php',
    'app/Livewire/Admin/Roles/Edit.php',
    'app/Livewire/Admin/Roles/Show.php',
    'app/Livewire/Admin/Cajas/Create.php',
    'app/Livewire/Admin/Cajas/Edit.php',
    'app/Livewire/Admin/Cajas/Show.php',
    'app/Livewire/Admin/Pagos/Create.php',
    'app/Livewire/Admin/Pagos/Edit.php',
    'app/Livewire/Admin/Pagos/Show.php',
    'app/Livewire/Admin/Pagos/Comprobantes.php',
    'app/Livewire/Admin/Matriculas/Create.php',
    'app/Livewire/Admin/Matriculas/Edit.php',
    'app/Livewire/Admin/Matriculas/Show.php',
    'app/Livewire/Admin/ConceptosPago/Create.php',
    'app/Livewire/Admin/ConceptosPago/Edit.php',
    'app/Livewire/Admin/ConceptosPago/Index.php',
    'app/Livewire/Admin/Empresas/Show.php',
    'app/Livewire/Admin/Reportes/EstadoCuentas.php',
    'app/Livewire/Admin/Reportes/HistoricoMatriculas.php',
    'app/Livewire/Admin/Reportes/IngresosTotales.php',
    'app/Livewire/Admin/Reportes/Morosidad.php',
    'app/Livewire/Admin/Reportes/ResumenPagos.php',
    'app/Livewire/Admin/Reuniones/Index.php',
    'app/Livewire/Admin/Notifications/Index.php',
    'app/Livewire/Admin/Notificaciones/PagosPendientes.php',
    'app/Livewire/Admin/Pagination.php',
    'app/Livewire/Admin/Students/Historico.php',
    'app/Livewire/Admin/Users/Profile/AvatarUpload.php',
    'app/Livewire/Admin/Users/Profile/ChangePassword.php',
    'app/Livewire/Admin/Users/Profile/HistoryUser.php',
    'app/Livewire/Admin/Users/Profile/ProfileList.php',
    'app/Livewire/Admin/Users/Profile/TwoFactorAuth.php',
];

$fixed = [];
$errors = [];

echo "🔧 Arreglando componentes con problemas de duplicación...\n\n";

foreach ($files_to_fix as $file_path) {
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);

        // Verificar si tiene el problema de duplicación
        if (strpos($content, 'use App\Traits\HasDynamicLayout;') !== false &&
            strpos($content, '    use HasDynamicLayout;') !== false) {

            echo "📁 Arreglando: $file_path\n";

            // Arreglar la duplicación
            $fixedContent = str_replace(
                "use App\Traits\HasDynamicLayout;\n    use HasDynamicLayout;",
                "use App\Traits\HasDynamicLayout;",
                $content
            );

            // Asegurar que la clase use el trait
            if (strpos($fixedContent, 'use HasDynamicLayout;') === false) {
                // Buscar el lugar correcto para insertar el trait
                $fixedContent = str_replace(
                    'class ',
                    "class ",
                    $fixedContent
                );

                // Insertar el trait después de la declaración de clase
                $fixedContent = preg_replace(
                    '/(class\s+\w+\s+extends\s+Component\s*\{)/',
                    '$1' . "\n    use HasDynamicLayout;",
                    $fixedContent
                );
            }

            // Guardar los cambios
            if (file_put_contents($file_path, $fixedContent)) {
                echo "   ✅ Arreglado\n";
                $fixed[] = $file_path;
            } else {
                echo "   ❌ Error al guardar\n";
                $errors[] = $file_path;
            }
        }
    }
}

echo "\n📊 Resumen:\n";
echo "- Componentes arreglados: " . count($fixed) . "\n";
echo "- Errores: " . count($errors) . "\n\n";

if (count($fixed) > 0) {
    echo "✅ Componentes arreglados:\n";
    foreach ($fixed as $component) {
        echo "   - $component\n";
    }
}
