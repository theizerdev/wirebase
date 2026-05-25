<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurar conexión a la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? 'mmmvnzla',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Configuración de logging
$logFileName = 'importacion_iglesias_log_' . date('Y-m-d_H-i-s') . '.txt';
$logFilePath = __DIR__ . '/' . $logFileName;
$csvFilePath = __DIR__ . '/importacion_iglesias_errores.csv';

function logMessage($message, $type = 'INFO') {
    global $logFilePath;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message" . PHP_EOL;
    echo $logEntry;
    file_put_contents($logFilePath, $logEntry, FILE_APPEND);
}

function logErrorToCsv($id, $nombre, $error, $datos = []) {
    global $csvFilePath;
    $csvData = [
        date('Y-m-d H:i:s'),
        $id,
        $nombre,
        $error,
        json_encode($datos)
    ];

    $fp = fopen($csvFilePath, 'a');
    if ($fp) {
        fputcsv($fp, $csvData, ',', '"', '"');
        fclose($fp);
    }
}

// Inicializar archivos
file_put_contents($logFilePath, "=== INICIO DE IMPORTACIÓN DE IGLESIAS ===\n", FILE_APPEND);
file_put_contents($csvFilePath, "Fecha,ID,Nombre,Error,Datos\n");

logMessage("Iniciando proceso de importación de iglesias...");

// Leer el archivo SQL
$sqlFile = __DIR__ . '/iglesias.sql';
if (!file_exists($sqlFile)) {
    logMessage("Error: No se encontró el archivo iglesias.sql", "ERROR");
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);
logMessage("Archivo SQL cargado: " . strlen($sqlContent) . " caracteres");

// Variables para estadísticas
$totalIglesias = 0;
$iglesiasExitosas = 0;
$iglesiasErrores = 0;
$totalIglesiaPastor = 0;
$iglesiaPastorExitosas = 0;
$iglesiaPastorErrores = 0;

// Función mejorada para parsear valores SQL
function parseSqlValues($valuesStr) {
    $values = [];
    $current = '';
    $inQuotes = false;
    $escapeNext = false;

    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $char = $valuesStr[$i];

        if ($escapeNext) {
            $current .= $char;
            $escapeNext = false;
            continue;
        }

        if ($char === '\\') {
            $escapeNext = true;
            $current .= $char;
            continue;
        }

        if ($char === "'" && !$inQuotes) {
            $inQuotes = true;
            $current .= $char;
        } elseif ($char === "'" && $inQuotes) {
            // Verificar si es una comilla escapada
            if ($i + 1 < strlen($valuesStr) && $valuesStr[$i + 1] === "'") {
                $current .= $char;
                $i++; // Saltar la siguiente comilla
            } else {
                $inQuotes = false;
                $current .= $char;
            }
        } elseif ($char === ',' && !$inQuotes) {
            $values[] = trim($current);
            $current = '';
        } else {
            $current .= $char;
        }
    }

    if ($current !== '') {
        $values[] = trim($current);
    }

    return $values;
}

logMessage("=== PROCESANDO IGLESIAS ===");

// Procesar inserciones de iglesias
// Primero extraer todo el bloque VALUES
preg_match("/INSERT INTO `iglesias`.*?VALUES\s*(.+);/s", $sqlContent, $matchesIglesias);

if (!isset($matchesIglesias[1])) {
    logMessage("No se encontraron datos de iglesias en el SQL", "ERROR");
    exit(1);
}

// Dividir por registros individuales
$valuesBlock = $matchesIglesias[1];
// Dividir por ),\s*\( para separar cada registro
preg_match_all("/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/", $valuesBlock, $individualRecords);

logMessage("Encontradas " . count($individualRecords[1]) . " inserciones de iglesias");

foreach ($individualRecords[1] as $index => $valuesStr) {
    $totalIglesias++;

    try {
        // Parsear los valores
        $values = parseSqlValues($valuesStr);

        if (count($values) < 27) {
            throw new Exception("Número insuficiente de valores: " . count($values));
        }

        // Extraer valores según la estructura del SQL
        $id = trim($values[0], "'");
        $name = trim($values[1], "'");
        $miembros_activos = $values[2] === 'NULL' ? null : trim($values[2], "'");
        $cantidad_campos_blancos = $values[3] === 'NULL' ? null : trim($values[3], "'");
        $tipo_local_id = $values[4] === 'NULL' ? null : trim($values[4], "'");
        $estado_id = $values[5] === 'NULL' ? null : trim($values[5], "'");
        $ciudad_id = $values[6] === 'NULL' ? null : trim($values[6], "'");
        $municipio_id = $values[7] === 'NULL' ? null : trim($values[7], "'");
        $parroquia_id = $values[8] === 'NULL' ? null : trim($values[8], "'");
        $pastor_id = $values[9] === 'NULL' ? null : trim($values[9], "'");
        $status = $values[10] === 'NULL' ? null : trim($values[10], "'");
        $zona = $values[11] === 'NULL' ? null : trim($values[11], "'");
        $distrito = $values[12] === 'NULL' ? null : trim($values[12], "'");
        $miembro_probante = $values[13] === 'NULL' ? null : trim($values[13], "'");
        $logros_obtenidos = $values[14] === 'NULL' ? null : trim($values[14], "'");
        $tiempo_trabajo = $values[15] === 'NULL' ? null : trim($values[15], "'");
        $sector = $values[16] === 'NULL' ? null : trim($values[16], "'");
        $calle = $values[17] === 'NULL' ? null : trim($values[17], "'");
        $avenida = $values[18] === 'NULL' ? null : trim($values[18], "'");
        $iglesias_fundadas = $values[19] === 'NULL' ? null : trim($values[19], "'");
        $pastores_ministerio = $values[20] === 'NULL' ? null : trim($values[20], "'");
        $posee_medio_comunicacion = $values[21] === 'NULL' ? null : trim($values[21], "'");
        $medio_comunicacion = $values[22] === 'NULL' ? null : trim($values[22], "'");
        $nombre_medio_comunicacion = $values[23] === 'NULL' ? null : trim($values[23], "'");
        $donde_medio_comunicacion = $values[24] === 'NULL' ? null : trim($values[24], "'");
        $created_at = trim($values[25], "'");
        $updated_at = trim($values[26], "'");

        // Construir dirección
        $direccion = '';
        if ($sector) $direccion .= $sector . ', ';
        if ($calle) $direccion .= 'Calle: ' . $calle . ', ';
        if ($avenida) $direccion .= 'Av: ' . $avenida;
        $direccion = trim($direccion, ', ');

        // Preparar datos para insertar
        $iglesiaData = [
            'id' => $id,
            'nombre' => $name, // name -> nombre
            'direccion' => $direccion ?: null,
            'pastor_id' => $pastor_id,
            'ciudad_id' => $ciudad_id,
            'estado_id' => $estado_id,
            'municipio_id' => $municipio_id,
            'parroquia_id' => $parroquia_id,
            'tipo_local_id' => $tipo_local_id,
            'zona' => $zona,
            'distrito' => $distrito,
            'activa' => $status == 1, // status -> activa
            'miembros_activos' => $miembros_activos,
            'cantidad_campos_blancos' => $cantidad_campos_blancos,
            'miembro_probante' => $miembro_probante,
            'logros_obtenidos' => $logros_obtenidos,
            'tiempo_trabajo' => $tiempo_trabajo,
            'sector' => $sector,
            'calle' => $calle,
            'avenida' => $avenida,
            'iglesias_fundadas' => $iglesias_fundadas,
            'pastores_ministerio' => $pastores_ministerio,
            'posee_medio_comunicacion' => $posee_medio_comunicacion == 'SI',
            'medio_comunicacion' => $medio_comunicacion,
            'nombre_medio_comunicacion' => $nombre_medio_comunicacion,
            'donde_medio_comunicacion' => $donde_medio_comunicacion,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'usuario_registro_id' => 1, // Usuario por defecto
        ];

        // Limpiar espacios en blanco y arreglar fechas
        foreach ($iglesiaData as $key => $value) {
            if (is_string($value)) {
                $iglesiaData[$key] = trim($value);
                // Arreglar fechas que pueden tener paréntesis extra
                if (in_array($key, ['created_at', 'updated_at']) && $value) {
                    $iglesiaData[$key] = trim($value, "')\"\t\n\r\0\x0B");
                }
            }
        }

        // Verificar si el pastor existe
        if ($pastor_id) {
            $pastorExists = Capsule::table('pastores')->where('id', $pastor_id)->exists();
            if (!$pastorExists) {
                logMessage("Advertencia: Pastor ID $pastor_id no existe para iglesia $name", "WARNING");
                $iglesiaData['pastor_id'] = null;
            }
        }

        // Verificar ubicaciones
        if ($estado_id && !Capsule::table('estados')->where('id', $estado_id)->exists()) {
            logMessage("Advertencia: Estado ID $estado_id no existe para iglesia $name", "WARNING");
            $iglesiaData['estado_id'] = null;
        }

        if ($municipio_id && !Capsule::table('municipios')->where('id', $municipio_id)->exists()) {
            logMessage("Advertencia: Municipio ID $municipio_id no existe para iglesia $name", "WARNING");
            $iglesiaData['municipio_id'] = null;
        }

        if ($parroquia_id && !Capsule::table('parroquias')->where('id', $parroquia_id)->exists()) {
            logMessage("Advertencia: Parroquia ID $parroquia_id no existe para iglesia $name", "WARNING");
            $iglesiaData['parroquia_id'] = null;
        }

        // Insertar iglesia
        try {
            Capsule::table('iglesias')->insert($iglesiaData);
            $iglesiasExitosas++;
            logMessage("Iglesia insertada exitosamente: $name (ID: $id)");
        } catch (Exception $e) {
            $iglesiasErrores++;
            $error = "Error al insertar iglesia: " . $e->getMessage();
            logMessage($error, "ERROR");
            logErrorToCsv($id, $name, $error, $iglesiaData);
        }

    } catch (Exception $e) {
        $iglesiasErrores++;
        $error = "Error procesando iglesia: " . $e->getMessage();
        logMessage($error, "ERROR");
        logErrorToCsv($index + 1, 'Desconocida', $error, ['values' => $valuesStr]);
    }
}

logMessage("=== PROCESANDO RELACIONES IGLESIA-PASTOR ===");

// Procesar inserciones de iglesia_pastor
// Primero extraer todo el bloque VALUES
preg_match("/INSERT INTO `iglesia_pastor`.*?VALUES\s*(.+);/s", $sqlContent, $matchesIglesiaPastor);

if (!isset($matchesIglesiaPastor[1])) {
    logMessage("No se encontraron datos de iglesia_pastor en el SQL", "WARNING");
} else {
    // Dividir por registros individuales
    $valuesBlockPastor = $matchesIglesiaPastor[1];
    preg_match_all("/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/", $valuesBlockPastor, $individualRecordsPastor);
    
    logMessage("Encontradas " . count($individualRecordsPastor[1]) . " inserciones de iglesia_pastor");
    
    foreach ($individualRecordsPastor[1] as $index => $valuesStr) {
    $totalIglesiaPastor++;

    try {
        // Parsear los valores
        $values = parseSqlValues($valuesStr);

        if (count($values) < 4) {
            throw new Exception("Número insuficiente de valores: " . count($values));
        }

        // Extraer valores
        $id = trim($values[0], "'");
        $pastor_id = trim($values[1], "'");
        $iglesia_id = trim($values[2], "'");
        $created_at = isset($values[3]) && $values[3] !== 'NULL' ? trim($values[3], "'") : date('Y-m-d H:i:s');
        $updated_at = isset($values[4]) && $values[4] !== 'NULL' ? trim($values[4], "'") : date('Y-m-d H:i:s');

        // Verificar que existan el pastor y la iglesia
        $pastorExists = Capsule::table('pastores')->where('id', $pastor_id)->exists();
        $iglesiaExists = Capsule::table('iglesias')->where('id', $iglesia_id)->exists();

        if (!$pastorExists) {
            logMessage("Advertencia: Pastor ID $pastor_id no existe para relación iglesia_pastor", "WARNING");
            continue;
        }

        if (!$iglesiaExists) {
            logMessage("Advertencia: Iglesia ID $iglesia_id no existe para relación iglesia_pastor", "WARNING");
            continue;
        }

        // Verificar si ya existe la relación
        $exists = Capsule::table('iglesia_pastor')
            ->where('pastor_id', $pastor_id)
            ->where('iglesia_id', $iglesia_id)
            ->exists();

        if ($exists) {
            logMessage("Relación ya existe: Pastor $pastor_id - Iglesia $iglesia_id", "WARNING");
            continue;
        }

        // Preparar datos para insertar
        $relacionData = [
            'pastor_id' => $pastor_id,
            'iglesia_id' => $iglesia_id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];

        // Insertar relación
        try {
            Capsule::table('iglesia_pastor')->insert($relacionData);
            $iglesiaPastorExitosas++;
            logMessage("Relación iglesia-pastor insertada: Pastor $pastor_id - Iglesia $iglesia_id");
        } catch (Exception $e) {
            $iglesiaPastorErrores++;
            $error = "Error al insertar relación iglesia-pastor: " . $e->getMessage();
            logMessage($error, "ERROR");
            logErrorToCsv($id, "Pastor $pastor_id - Iglesia $iglesia_id", $error, $relacionData);
        }

    } catch (Exception $e) {
        $iglesiaPastorErrores++;
        $error = "Error procesando relación iglesia-pastor: " . $e->getMessage();
        logMessage($error, "ERROR");
        logErrorToCsv($index + 1, 'Relación desconocida', $error, ['values' => $valuesStr]);
    }
}
}

// Mostrar estadísticas finales
logMessage("=== ESTADÍSTICAS FINALES ===");
logMessage("IGLESIAS:");
logMessage("  Total procesadas: $totalIglesias");
logMessage("  Exitosas: $iglesiasExitosas");
logMessage("  Con errores: $iglesiasErrores");
logMessage("");
logMessage("RELACIONES IGLESIA-PASTOR:");
logMessage("  Total procesadas: $totalIglesiaPastor");
logMessage("  Exitosas: $iglesiaPastorExitosas");
logMessage("  Con errores: $iglesiaPastorErrores");
logMessage("");
logMessage("=== IMPORTACIÓN COMPLETADA ===");
logMessage("Log guardado en: $logFilePath");
logMessage("Errores guardados en: $csvFilePath");