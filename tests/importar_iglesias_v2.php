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
$logFileName = 'importacion_iglesias_v2_log_' . date('Y-m-d_H-i-s') . '.txt';
$logFilePath = __DIR__ . '/' . $logFileName;
$csvFilePath = __DIR__ . '/importacion_iglesias_v2_errores.csv';

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
file_put_contents($logFilePath, "=== INICIO DE IMPORTACIÓN DE IGLESIAS V2 ===\n", FILE_APPEND);
file_put_contents($csvFilePath, "Fecha,Hora,ID,Nombre,Error,Datos\n");

logMessage("Iniciando proceso de importación de iglesias V2...");

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

// Función mejorada para parsear líneas SQL
function parseSqlLine($line) {
    $line = trim($line);
    if (empty($line) || $line === ')' || substr($line, 0, 6) === 'DELETE') {
        return null;
    }

    // Eliminar la coma final si existe
    if (substr($line, -1) === ',') {
        $line = substr($line, 0, -1);
    }

    // Parsear valores entre paréntesis
    if (preg_match('/^\s*\((.*)\)\s*$/s', $line, $matches)) {
        return $matches[1];
    }

    return null;
}

// Función para parsear valores SQL mejorada
function parseSqlValues($valuesStr) {
    $values = [];
    $current = '';
    $inQuotes = false;
    $escapeNext = false;
    $parenthesesLevel = 0;

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
        } elseif ($char === '(' && !$inQuotes) {
            $parenthesesLevel++;
            $current .= $char;
        } elseif ($char === ')' && !$inQuotes) {
            $parenthesesLevel--;
            $current .= $char;
        } elseif ($char === ',' && !$inQuotes && $parenthesesLevel === 0) {
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

// Función para limpiar valores
function cleanValue($value) {
    if ($value === 'NULL' || $value === null) {
        return null;
    }

    $value = trim($value);

    // Eliminar comillas
    if ((substr($value, 0, 1) === "'" && substr($value, -1) === "'") ||
        (substr($value, 0, 1) === '"' && substr($value, -1) === '"')) {
        $value = substr($value, 1, -1);
    }

    // Decodificar caracteres escapados
    $value = str_replace("''", "'", $value);
    $value = str_replace("\\'", "'", $value);
    $value = str_replace('\\"', '"', $value);
    $value = str_replace("\\n", "\n", $value);
    $value = str_replace("\\r", "\r", $value);
    $value = str_replace("\\t", "\t", $value);

    return $value === '' ? null : $value;
}

// Función para validar y formatear fecha
function formatDate($dateStr) {
    if (!$dateStr || $dateStr === 'NULL') {
        return null;
    }

    $dateStr = cleanValue($dateStr);

    if (empty($dateStr)) {
        return null;
    }

    // Si ya está en formato MySQL, devolverlo
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateStr)) {
        return $dateStr;
    }

    // Intentar parsear la fecha
    try {
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return date('Y-m-d H:i:s', $timestamp);
        }
    } catch (Exception $e) {
        // Ignorar error
    }

    return null;
}

// Procesar líneas del archivo SQL
$lines = explode("\n", $sqlContent);
$currentLine = '';
$inInsert = false;

foreach ($lines as $lineNumber => $line) {
    $trimmedLine = trim($line);

    // Saltar líneas vacías o comentarios
    if (empty($trimmedLine) || substr($trimmedLine, 0, 2) === '--') {
        continue;
    }

    // Detectar inicio de INSERT
    if (preg_match('/^INSERT INTO `iglesias`/', $trimmedLine)) {
        $inInsert = true;
        continue;
    }

    // Detectar fin de INSERT
    if ($inInsert && substr($trimmedLine, -1) === ';') {
        $inInsert = false;
        continue;
    }

    // Procesar líneas de valores
    if ($inInsert) {
        $valuesStr = parseSqlLine($trimmedLine);
        if ($valuesStr) {
            $totalIglesias++;

            try {
                // Parsear los valores
                $values = parseSqlValues($valuesStr);

                if (count($values) < 27) {
                    throw new Exception("Número insuficiente de valores: " . count($values) . " (esperados: 27)");
                }

                // Extraer y limpiar valores
                $id = cleanValue($values[0]);
                $name = cleanValue($values[1]);
                $miembros_activos = cleanValue($values[2]);
                $cantidad_campos_blancos = cleanValue($values[3]);
                $tipo_local_id = cleanValue($values[4]);
                $estado_id = cleanValue($values[5]);
                $ciudad_id = cleanValue($values[6]);
                $municipio_id = cleanValue($values[7]);
                $parroquia_id = cleanValue($values[8]);
                $pastor_id = cleanValue($values[9]);
                $status = cleanValue($values[10]);
                $zona = cleanValue($values[11]);
                $distrito = cleanValue($values[12]);
                $miembro_probante = cleanValue($values[13]);
                $logros_obtenidos = cleanValue($values[14]);
                $tiempo_trabajo = cleanValue($values[15]);
                $sector = cleanValue($values[16]);
                $calle = cleanValue($values[17]);
                $avenida = cleanValue($values[18]);
                $iglesias_fundadas = cleanValue($values[19]);
                $pastores_ministerio = cleanValue($values[20]);
                $posee_medio_comunicacion = cleanValue($values[21]);
                $medio_comunicacion = cleanValue($values[22]);
                $nombre_medio_comunicacion = cleanValue($values[23]);
                $donde_medio_comunicacion = cleanValue($values[24]);
                $created_at = formatDate($values[25]);
                $updated_at = formatDate($values[26]);

                // Validar ID
                if (!is_numeric($id)) {
                    throw new Exception("ID inválido: $id");
                }

                // Construir dirección
                $direccion = '';
                if ($sector) $direccion .= $sector . ', ';
                if ($calle) $direccion .= 'Calle: ' . $calle . ', ';
                if ($avenida) $direccion .= 'Av: ' . $avenida;
                $direccion = trim($direccion, ', ');

                // Preparar datos para insertar
                $iglesiaData = [
                    'id' => $id,
                    'nombre' => $name,
                    'direccion' => $direccion ?: null,
                    'pastor_id' => $pastor_id,
                    'ciudad_id' => $ciudad_id,
                    'estado_id' => $estado_id,
                    'municipio_id' => $municipio_id,
                    'parroquia_id' => $parroquia_id,
                    'zona' => $zona,
                    'distrito' => $distrito,
                    'activa' => $status == 1,
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

                // Limpiar espacios en blanco
                foreach ($iglesiaData as $key => $value) {
                    if (is_string($value)) {
                        $iglesiaData[$key] = trim($value);
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

                if ($ciudad_id && !Capsule::table('ciudades')->where('id', $ciudad_id)->exists()) {
                    logMessage("Advertencia: Ciudad ID $ciudad_id no existe para iglesia $name", "WARNING");
                    $iglesiaData['ciudad_id'] = null;
                }

                // Insertar iglesia
                Capsule::table('iglesias')->insert($iglesiaData);
                $iglesiasExitosas++;

                logMessage("Iglesia importada: $name (ID: $id)");

            } catch (Exception $e) {
                $iglesiasErrores++;
                $errorMsg = $e->getMessage();
                logMessage("Error al importar iglesia línea $lineNumber: $errorMsg", "ERROR");
                logErrorToCsv($lineNumber, "Iglesia línea $lineNumber", $errorMsg, ['values' => $values ?? []]);
            }
        }
    }
}

// Ahora procesar iglesia_pastor
$inInsert = false;
$currentLine = '';

foreach ($lines as $lineNumber => $line) {
    $trimmedLine = trim($line);

    // Saltar líneas vacías o comentarios
    if (empty($trimmedLine) || substr($trimmedLine, 0, 2) === '--') {
        continue;
    }

    // Detectar inicio de INSERT para iglesia_pastor
    if (preg_match('/^INSERT INTO `iglesia_pastor`/', $trimmedLine)) {
        $inInsert = true;
        continue;
    }

    // Detectar fin de INSERT
    if ($inInsert && substr($trimmedLine, -1) === ';') {
        $inInsert = false;
        continue;
    }

    // Procesar líneas de valores
    if ($inInsert) {
        $valuesStr = parseSqlLine($trimmedLine);
        if ($valuesStr) {
            $totalIglesiaPastor++;

            try {
                // Parsear los valores
                $values = parseSqlValues($valuesStr);

                if (count($values) < 5) {
                    throw new Exception("Número insuficiente de valores: " . count($values) . " (esperados: 5)");
                }

                // Extraer y limpiar valores
                $id = cleanValue($values[0]);
                $pastor_id = cleanValue($values[1]);
                $iglesia_id = cleanValue($values[2]);
                $created_at = formatDate($values[3]);
                $updated_at = formatDate($values[4]);

                // Validar IDs
                if (!is_numeric($id) || !is_numeric($pastor_id) || !is_numeric($iglesia_id)) {
                    throw new Exception("IDs inválidos: id=$id, pastor_id=$pastor_id, iglesia_id=$iglesia_id");
                }

                // Verificar que ambos existan
                $pastorExists = Capsule::table('pastores')->where('id', $pastor_id)->exists();
                $iglesiaExists = Capsule::table('iglesias')->where('id', $iglesia_id)->exists();

                if (!$pastorExists) {
                    logMessage("Advertencia: Pastor ID $pastor_id no existe para relación iglesia_pastor", "WARNING");
                    $iglesiaPastorErrores++;
                    continue;
                }

                if (!$iglesiaExists) {
                    logMessage("Advertencia: Iglesia ID $iglesia_id no existe para relación iglesia_pastor", "WARNING");
                    $iglesiaPastorErrores++;
                    continue;
                }

                // Verificar si ya existe la relación
                $exists = Capsule::table('iglesia_pastor')
                    ->where('pastor_id', $pastor_id)
                    ->where('iglesia_id', $iglesia_id)
                    ->exists();

                if ($exists) {
                    logMessage("Relación iglesia_pastor ya existe: Pastor $pastor_id - Iglesia $iglesia_id", "INFO");
                    $iglesiaPastorExitosas++;
                    continue;
                }

                // Insertar relación
                Capsule::table('iglesia_pastor')->insert([
                    'id' => $id,
                    'pastor_id' => $pastor_id,
                    'iglesia_id' => $iglesia_id,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                ]);

                $iglesiaPastorExitosas++;
                logMessage("Relación iglesia_pastor creada: Pastor $pastor_id - Iglesia $iglesia_id");

            } catch (Exception $e) {
                $iglesiaPastorErrores++;
                $errorMsg = $e->getMessage();
                logMessage("Error al importar iglesia_pastor línea $lineNumber: $errorMsg", "ERROR");
            }
        }
    }
}

// Resumen final
logMessage("\n=== RESUMEN DE IMPORTACIÓN V2 ===");
logMessage("Total de iglesias procesadas: $totalIglesias");
logMessage("Iglesias importadas exitosamente: $iglesiasExitosas");
logMessage("Iglesias con errores: $iglesiasErrores");
if ($totalIglesias > 0) {
    logMessage("Porcentaje de éxito (iglesias): " . round(($iglesiasExitosas / $totalIglesias) * 100, 2) . "%");
}
logMessage("");
logMessage("Total de relaciones iglesia_pastor procesadas: $totalIglesiaPastor");
logMessage("Relaciones iglesia_pastor creadas exitosamente: $iglesiaPastorExitosas");
logMessage("Relaciones iglesia_pastor con errores: $iglesiaPastorErrores");
if ($totalIglesiaPastor > 0) {
    logMessage("Porcentaje de éxito (iglesia_pastor): " . round(($iglesiaPastorExitosas / $totalIglesiaPastor) * 100, 2) . "%");
}

logMessage("\nArchivo de log: $logFileName");
logMessage("Archivo de errores CSV: importacion_iglesias_v2_errores.csv");
logMessage("\n=== PROCESO FINALIZADO ===");
