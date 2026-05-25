<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Cargar variables de entorno si existe el archivo .env
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            putenv("$key=$value");
        }
    }
}

// Configurar la conexión a la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST') ?: 'localhost',
    'database'  => getenv('DB_DATABASE') ?: 'mmmvnzla',
    'username'  => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== ANÁLISIS DE ERRORES DE IMPORTACIÓN DE PASTORES ===\n\n";

// Leer el archivo SQL para analizar los datos originales
$sqlFile = 'pastores30122025.sql';
if (!file_exists($sqlFile)) {
    die("Error: No se encuentra el archivo $sqlFile\n");
}

$content = file_get_contents($sqlFile);

// Array para almacenar información de todos los registros
$allRecords = [];
$problematicRecords = [];

// Extraer todas las sentencias INSERT
preg_match_all('/INSERT INTO `pastors` .*?VALUES\s*(.*?);/s', $content, $matches);

if (!isset($matches[1]) || empty($matches[1])) {
    die("No se encontraron sentencias INSERT en el archivo.\n");
}

$recordNumber = 0;
foreach ($matches[1] as $insertBlock) {
    $valuesPart = trim($insertBlock);
    if (substr($valuesPart, 0, 1) === '(' && substr($valuesPart, -1) === ')') {
        $valuesPart = substr($valuesPart, 1, -1);
    }

    $records = preg_split('/\),\s*\(/', $valuesPart);

    foreach ($records as $record) {
        $recordNumber++;
        $record = trim($record, '()');
        $values = parseValues($record);

        // Analizar cada registro
        $analysis = analyzeRecord($values, $recordNumber);
        $allRecords[] = $analysis;

        if ($analysis['has_problems']) {
            $problematicRecords[] = $analysis;
        }
    }
}

// Generar reporte detallado
echo "Total de registros analizados: " . count($allRecords) . "\n";
echo "Registros con problemas: " . count($problematicRecords) . "\n\n";

// Categorizar problemas
$problemCategories = [
    'datos_incompletos' => [],
    'fechas_invalidas' => [],
    'codigos_duplicados' => [],
    'campos_vacios_criticos' => [],
    'ubicaciones_invalidas' => [],
    'datos_mal_formateados' => []
];

foreach ($problematicRecords as $record) {
    if ($record['problems']['incomplete_data']) {
        $problemCategories['datos_incompletos'][] = $record;
    }
    if ($record['problems']['invalid_dates']) {
        $problemCategories['fechas_invalidas'][] = $record;
    }
    if ($record['problems']['empty_critical_fields']) {
        $problemCategories['campos_vacios_criticos'][] = $record;
    }
    if ($record['problems']['invalid_locations']) {
        $problemCategories['ubicaciones_invalidas'][] = $record;
    }
    if ($record['problems']['malformed_data']) {
        $problemCategories['datos_mal_formateados'][] = $record;
    }
}

// Mostrar resumen por categorías
echo "=== RESUMEN DE PROBLEMAS POR CATEGORÍA ===\n\n";

foreach ($problemCategories as $category => $records) {
    if (count($records) > 0) {
        echo strtoupper(str_replace('_', ' ', $category)) . ": " . count($records) . " registros\n";

        switch ($category) {
            case 'datos_incompletos':
                echo "  → Registros con menos de 40 campos requeridos\n";
                break;
            case 'fechas_invalidas':
                echo "  → Fechas de nacimiento con formato inválido o nulas\n";
                break;
            case 'campos_vacios_criticos':
                echo "  → Código, nombres o apellidos vacíos\n";
                break;
            case 'ubicaciones_invalidas':
                echo "  → Municipios, estados o parroquias con datos inválidos\n";
                break;
            case 'datos_mal_formateados':
                echo "  → Campos con formato incorrecto (edad, teléfonos, etc.)\n";
                break;
        }
        echo "\n";
    }
}

// Generar reporte detallado CSV
$csvFile = 'analisis_errores_pastores_' . date('Y-m-d_H-i-s') . '.csv';
$csvContent = "Registro,Código,Nombre,Apellidos,Problemas,Fecha_Nacimiento,Edad,Municipio,Estado,Parroquia,Teléfonos,Observaciones\n";

foreach ($problematicRecords as $record) {
    $csvContent .= $record['record_number'] . ",";
    $csvContent .= '"' . str_replace('"', '""', $record['codigo']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['nombres']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['apellidos']) . '",';
    $csvContent .= '"' . str_replace('"', '""', implode('; ', $record['problem_descriptions'])) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['fecha_nacimiento']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['edad']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['municipio']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['estado']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['parroquia']) . '",';
    $csvContent .= '"' . str_replace('"', '""', implode('; ', $record['telefonos'])) . '",';
    $csvContent .= '"' . str_replace('"', '""', $record['observaciones']) . '"' . "\n";
}

file_put_contents($csvFile, $csvContent);
echo "✓ Reporte detallado generado: $csvFile\n\n";

// Mostrar ejemplos de errores comunes
echo "=== EJEMPLOS DE ERRORES COMUNES ===\n\n";

if (!empty($problemCategories['datos_incompletos'])) {
    echo "1. REGISTROS CON DATOS INCOMPLETOS (Primeros 3 ejemplos):\n";
    for ($i = 0; $i < min(3, count($problemCategories['datos_incompletos'])); $i++) {
        $record = $problemCategories['datos_incompletos'][$i];
        echo "   Registro #{$record['record_number']} - Código: {$record['codigo']}\n";
        echo "   Nombre: {$record['nombres']} {$record['apellidos']}\n";
        echo "   Campos encontrados: {$record['field_count']}/40\n\n";
    }
}

if (!empty($problemCategories['fechas_invalidas'])) {
    echo "2. FECHAS DE NACIMIENTO INVÁLIDAS (Primeros 3 ejemplos):\n";
    for ($i = 0; $i < min(3, count($problemCategories['fechas_invalidas'])); $i++) {
        $record = $problemCategories['fechas_invalidas'][$i];
        echo "   Registro #{$record['record_number']} - Código: {$record['codigo']}\n";
        echo "   Fecha original: '{$record['fecha_nacimiento_original']}'\n";
        echo "   Fecha formateada: " . ($record['fecha_nacimiento'] ?: 'NULL') . "\n\n";
    }
}

if (!empty($problemCategories['campos_vacios_criticos'])) {
    echo "3. CAMPOS CRÍTICOS VACÍOS (Primeros 3 ejemplos):\n";
    for ($i = 0; $i < min(3, count($problemCategories['campos_vacios_criticos'])); $i++) {
        $record = $problemCategories['campos_vacios_criticos'][$i];
        echo "   Registro #{$record['record_number']} - Código: {$record['codigo']}\n";
        echo "   Nombre: {$record['nombres']} {$record['apellidos']}\n";
        echo "   Campos vacíos: " . implode(', ', $record['empty_critical_fields']) . "\n\n";
    }
}

// Recomendaciones
echo "=== RECOMENDACIONES PARA CORREGIR ERRORES ===\n\n";

if (count($problemCategories['datos_incompletos']) > 0) {
    echo "• DATOS INCOMPLETOS:\n";
    echo "  - Verificar que el archivo SQL esté bien formado\n";
    echo "  - Revisar que no haya registros truncados\n";
    echo "  - Considerar usar un parser SQL más robusto\n\n";
}

if (count($problemCategories['fechas_invalidas']) > 0) {
    echo "• FECHAS INVÁLIDAS:\n";
    echo "  - Establecer un formato de fecha estándar (YYYY-MM-DD)\n";
    echo "  - Validar fechas antes de la importación\n";
    echo "  - Considerar fechas nulas como opción válida\n\n";
}

if (count($problemCategories['campos_vacios_criticos']) > 0) {
    echo "• CAMPOS CRÍTICOS VACÍOS:\n";
    echo "  - Establecer valores por defecto para campos obligatorios\n";
    echo "  - Generar códigos automáticos si están vacíos\n";
    echo "  - Validar nombres y apellidos como requeridos\n\n";
}

echo "=== ANÁLISIS COMPLETADO ===\n";

/**
 * Función para analizar un registro individual
 */
function analyzeRecord($values, $recordNumber) {
    $analysis = [
        'record_number' => $recordNumber,
        'codigo' => !empty($values[1]) ? $values[1] : 'VACÍO',
        'nombres' => !empty($values[2]) ? trim($values[2]) : 'VACÍO',
        'apellidos' => !empty($values[3]) ? trim($values[3]) : 'VACÍO',
        'field_count' => count($values),
        'has_problems' => false,
        'problems' => [
            'incomplete_data' => false,
            'invalid_dates' => false,
            'empty_critical_fields' => false,
            'invalid_locations' => false,
            'malformed_data' => false
        ],
        'problem_descriptions' => [],
        'fecha_nacimiento_original' => !empty($values[12]) ? $values[12] : 'VACÍO',
        'fecha_nacimiento' => null,
        'edad' => !empty($values[9]) ? $values[9] : 'VACÍO',
        'municipio' => !empty($values[31]) ? $values[31] : 'VACÍO',
        'estado' => !empty($values[40]) ? $values[40] : 'VACÍO',
        'parroquia' => !empty($values[41]) ? $values[41] : 'VACÍO',
        'telefonos' => [
            !empty($values[32]) ? $values[32] : 'VACÍO', // telefono_hab
            !empty($values[33]) ? $values[33] : 'VACÍO', // telefono_tlf
            !empty($values[34]) ? $values[34] : 'VACÍO'  // telefono_otro
        ],
        'empty_critical_fields' => [],
        'observaciones' => ''
    ];

    // Verificar datos incompletos
    if (count($values) < 40) {
        $analysis['has_problems'] = true;
        $analysis['problems']['incomplete_data'] = true;
        $analysis['problem_descriptions'][] = 'Datos incompletos (' . count($values) . '/40 campos)';
    }

    // Verificar campos críticos vacíos
    $criticalFields = [
        1 => 'código',
        2 => 'nombres',
        3 => 'apellidos'
    ];

    foreach ($criticalFields as $index => $field) {
        if (empty($values[$index]) || trim($values[$index]) === '' || trim($values[$index]) === 'NULL') {
            $analysis['has_problems'] = true;
            $analysis['problems']['empty_critical_fields'] = true;
            $analysis['empty_critical_fields'][] = $field;
        }
    }

    if (!empty($analysis['empty_critical_fields'])) {
        $analysis['problem_descriptions'][] = 'Campos críticos vacíos: ' . implode(', ', $analysis['empty_critical_fields']);
    }

    // Verificar fecha de nacimiento
    if (!empty($values[12]) && trim($values[12]) !== 'NULL' && trim($values[12]) !== '') {
        $fechaFormateada = formatDate($values[12]);
        $analysis['fecha_nacimiento'] = $fechaFormateada;
        if ($fechaFormateada === null) {
            $analysis['has_problems'] = true;
            $analysis['problems']['invalid_dates'] = true;
            $analysis['problem_descriptions'][] = 'Fecha de nacimiento inválida';
        }
    } else {
        $analysis['fecha_nacimiento'] = null;
        $analysis['has_problems'] = true;
        $analysis['problems']['invalid_dates'] = true;
        $analysis['problem_descriptions'][] = 'Fecha de nacimiento vacía';
    }

    // Verificar ubicaciones
    $locationFields = [
        31 => 'municipio',
        40 => 'estado',
        41 => 'parroquia'
    ];

    foreach ($locationFields as $index => $field) {
        if (!empty($values[$index]) && trim($values[$index]) !== 'NULL' && trim($values[$index]) !== '') {
            // Verificar si el nombre parece válido (más de 2 caracteres, no solo números)
            if (strlen(trim($values[$index])) < 2 || is_numeric(trim($values[$index]))) {
                $analysis['has_problems'] = true;
                $analysis['problems']['invalid_locations'] = true;
                $analysis['problem_descriptions'][] = "Ubicación inválida: $field";
            }
        }
    }

    // Verificar edad
    if (!empty($values[9]) && trim($values[9]) !== 'NULL' && trim($values[9]) !== '') {
        $edad = intval($values[9]);
        if ($edad < 18 || $edad > 100) {
            $analysis['has_problems'] = true;
            $analysis['problems']['malformed_data'] = true;
            $analysis['problem_descriptions'][] = "Edad fuera de rango ($edad años)";
        }
    }

    // Verificar teléfonos
    $phoneFields = [32, 33, 34];
    foreach ($phoneFields as $index) {
        if (!empty($values[$index]) && trim($values[$index]) !== 'NULL' && trim($values[$index]) !== '') {
            $telefono = trim($values[$index]);
            // Verificar formato básico de teléfono (debe contener números)
            if (!preg_match('/[0-9]/', $telefono)) {
                $analysis['has_problems'] = true;
                $analysis['problems']['malformed_data'] = true;
                $analysis['problem_descriptions'][] = "Teléfono inválido: $telefono";
            }
        }
    }

    return $analysis;
}

/**
 * Función para parsear los valores de una línea SQL
 */
function parseValues($record) {
    $values = [];
    $current = '';
    $inString = false;
    $escaped = false;

    for ($i = 0; $i < strlen($record); $i++) {
        $char = $record[$i];

        if ($escaped) {
            $current .= $char;
            $escaped = false;
            continue;
        }

        if ($char === '\\') {
            $escaped = true;
            continue;
        }

        if ($char === "'" && !$inString) {
            $inString = true;
            continue;
        }

        if ($char === "'" && $inString) {
            $inString = false;
            continue;
        }

        if ($char === ',' && !$inString) {
            $value = trim($current);
            $values[] = ($value === 'NULL' || $value === '') ? null : trim($current, "'");
            $current = '';
            continue;
        }

        $current .= $char;
    }

    // Agregar el último valor
    $value = trim($current);
    $values[] = ($value === 'NULL' || $value === '') ? null : trim($current, "'");

    return $values;
}

/**
 * Función para formatear fechas
 */
function formatDate($date) {
    if (empty($date) || trim($date) === 'NULL') return null;

    $date = trim($date);
    $formats = ['d-m-Y', 'Y-m-d'];

    foreach ($formats as $format) {
        $dateTime = DateTime::createFromFormat($format, $date);
        if ($dateTime !== false) {
            return $dateTime->format('Y-m-d');
        }
    }

    try {
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
    } catch (Exception $e) {
        // Silenciar excepciones
    }

    return null;
}

?>
