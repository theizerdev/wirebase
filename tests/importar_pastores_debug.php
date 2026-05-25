<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Cargar variables de entorno si existe el archivo .env
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Saltar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Separar clave y valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remover comillas si las tiene
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

echo "Iniciando importación de pastores con debugging detallado...\n\n";

// Verificar si existen las tablas necesarias
try {
    Capsule::schema()->hasTable('pastores');
    Capsule::schema()->hasTable('municipios');
    Capsule::schema()->hasTable('estados');
    Capsule::schema()->hasTable('parroquias');
} catch (Exception $e) {
    die("Error: No se puede conectar a la base de datos o faltan tablas requeridas.\n" . $e->getMessage() . "\n");
}

// Cachés para evitar consultas repetidas
$municipiosCache = [];
$estadosCache = [];
$parroquiasCache = [];

// Leer el archivo SQL
$sqlFile = 'pastores30122025.sql';
if (!file_exists($sqlFile)) {
    die("Error: No se encuentra el archivo $sqlFile\n");
}

$content = file_get_contents($sqlFile);

// Contadores
$inserted = 0;
$errors = 0;
$debugInfo = [];
$errorDetails = [];
$processedRecords = 0;
$pastoresTemporal = []; // Para manejar relaciones de cónyuges después

// Extraer todas las sentencias INSERT
preg_match_all('/INSERT INTO `pastors` .*?VALUES\s*(.*?);/s', $content, $matches);

if (!isset($matches[1]) || empty($matches[1])) {
    die("No se encontraron sentencias INSERT en el archivo.\n");
}

echo "Encontrados " . count($matches[1]) . " bloques INSERT\n\n";

foreach ($matches[1] as $blockIndex => $insertBlock) {
    // Limpiar el bloque de valores
    $valuesPart = trim($insertBlock);
    // Remover paréntesis inicial y final si existen
    if (substr($valuesPart, 0, 1) === '(' && substr($valuesPart, -1) === ')') {
        $valuesPart = substr($valuesPart, 1, -1);
    }

    // Dividir por '), (' para obtener cada registro
    $records = preg_split('/\),\s*\(/', $valuesPart);

    echo "Procesando bloque " . ($blockIndex + 1) . " con " . count($records) . " registros\n";

    foreach ($records as $recordIndex => $record) {
        // Limpiar paréntesis restantes
        $record = trim($record, '()');
        $processedRecords++;

        // Parsear los valores del registro
        $values = parseValues($record);

        // Debug: Mostrar información del registro actual
        echo "\n--- Procesando registro $processedRecords ---\n";
        echo "Código: " . (!empty($values[1]) ? $values[1] : 'VACÍO') . "\n";
        echo "Nombre: " . (!empty($values[2]) ? trim($values[2]) : 'VACÍO') . "\n";
        echo "Apellidos: " . (!empty($values[3]) ? trim($values[3]) : 'VACÍO') . "\n";
        echo "Total valores encontrados: " . count($values) . "\n";

        if (count($values) >= 40) { // Verificar que tenemos suficientes valores
            try {
                // Obtener IDs para las relaciones foráneas
                $municipioId = null;
                $estadoId = null;
                $parroquiaId = null;

                // Debug: Validar campos clave antes de procesar
                echo "Validando campos clave...\n";

                if (!empty($values[31])) { // municipio
                    echo "  Municipio: {$values[31]}\n";
                    $municipioId = getOrCreateMunicipio($values[31], $municipiosCache);
                    echo "  Municipio ID resultante: $municipioId\n";
                } else {
                    echo "  Municipio: VACÍO\n";
                }

                if (!empty($values[40])) { // estado
                    echo "  Estado: {$values[40]}\n";
                    $estadoId = getOrCreateEstado($values[40], $estadosCache);
                    echo "  Estado ID resultante: $estadoId\n";
                } else {
                    echo "  Estado: VACÍO\n";
                }

                if (!empty($values[41])) { // parroquia
                    echo "  Parroquia: {$values[41]}\n";
                    $parroquiaId = getOrCreateParroquia($values[41], $parroquiasCache);
                    echo "  Parroquia ID resultante: $parroquiaId\n";
                } else {
                    echo "  Parroquia: VACÍO\n";
                }

                // Debug: Validar fecha de nacimiento
                $fechaNac = formatDate($values[12]);
                echo "  Fecha nacimiento original: " . (!empty($values[12]) ? $values[12] : 'VACÍO') . "\n";
                echo "  Fecha nacimiento formateada: " . ($fechaNac ?: 'NULL') . "\n";

                // Crear el array de datos mapeando los campos del SQL antiguo al nuevo
                $data = [
                    'codigo' => !empty($values[1]) ? $values[1] : null,
                    'nombres' => !empty($values[2]) ? trim($values[2]) : '',
                    'apellidos' => !empty($values[3]) ? trim($values[3]) : '',
                    'documento' => !empty($values[4]) ? $values[4] : null,
                    'nivel_ministerial' => !empty($values[5]) ? $values[5] : null,
                    'zona' => !empty($values[6]) ? $values[6] : null,
                    'distrito' => !empty($values[7]) ? $values[7] : null,
                    'genero' => !empty($values[8]) ? $values[8] : null,
                    'edad' => !empty($values[9]) ? intval($values[9]) : null,
                    'ano_promocion' => !empty($values[10]) ? $values[10] : null,
                    'tiempo_colaborando' => !empty($values[11]) ? $values[11] : null,
                    'fe_nacimiento' => $fechaNac,
                    'foto' => !empty($values[13]) ? $values[13] : null,
                    'nota' => !empty($values[14]) ? $values[14] : null,
                    'status' => !empty($values[15]) ? boolval($values[15]) : true,
                    'estado_civil' => !empty($values[16]) ? $values[16] : null,
                    'batizado_espiritu_santo' => convertToBoolean($values[17]),
                    'grado_instruccion' => !empty($values[18]) ? $values[18] : null,
                    'titulo_obtenido' => !empty($values[19]) ? $values[19] : null,
                    'estudio_teologico' => convertToBoolean($values[20]),
                    'titulo_teologico' => !empty($values[21]) ? $values[21] : null,
                    'tiempo_de_estudio_teologico' => !empty($values[22]) ? $values[22] : null,
                    'instituto_teologico' => !empty($values[23]) ? $values[23] : null,
                    'pertenece_ministerio' => convertToBoolean($values[24]),
                    'nombre_conyuge' => !empty($values[25]) ? $values[25] : null,
                    'edificio_casa_quinta' => !empty($values[26]) ? $values[26] : null,
                    'piso' => !empty($values[27]) ? $values[27] : null,
                    'apartamento' => !empty($values[28]) ? $values[28] : null,
                    'calle_avenida' => !empty($values[29]) ? $values[29] : null,
                    'urbanizacion' => !empty($values[30]) ? $values[30] : null,
                    'municipio_id' => $municipioId,
                    'telefono_hab' => !empty($values[32]) ? $values[32] : null,
                    'telefono_tlf' => !empty($values[33]) ? $values[33] : null,
                    'telefono_otro' => !empty($values[34]) ? $values[34] : null,
                    'mencion' => !empty($values[36]) ? $values[36] : null,
                    'cargo_nacional' => !empty($values[37]) ? $values[37] : null,
                    'created_at' => !empty($values[38]) ? $values[38] : date('Y-m-d H:i:s'),
                    'updated_at' => !empty($values[39]) ? $values[39] : date('Y-m-d H:i:s'),
                    'estado_id' => $estadoId,
                    'parroquia_id' => $parroquiaId,
                ];

                // Guardar temporalmente para procesar cónyuges después
                $pastorCodigo = $data['codigo'];
                $data['temp_nombre_conyuge'] = $data['nombre_conyuge'];
                unset($data['nombre_conyuge']); // Lo manejaremos después

                $pastoresTemporal[$pastorCodigo] = $data;

                $inserted++;
                echo "✓ Procesado exitosamente: {$data['nombres']} {$data['apellidos']} (Código: $pastorCodigo)\n";

                // Guardar info de debug
                $debugInfo[] = [
                    'registro' => $processedRecords,
                    'codigo' => $pastorCodigo,
                    'nombre' => $data['nombres'] . ' ' . $data['apellidos'],
                    'estado' => 'éxito',
                    'municipio_id' => $municipioId,
                    'estado_id' => $estadoId,
                    'parroquia_id' => $parroquiaId
                ];

            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                echo "✗ ERROR: $errorMsg\n";
                $errors++;

                // Guardar detalles del error
                $errorDetails[] = [
                    'registro' => $processedRecords,
                    'codigo' => !empty($values[1]) ? $values[1] : 'SIN_CODIGO',
                    'nombre' => (!empty($values[2]) ? trim($values[2]) : '') . ' ' . (!empty($values[3]) ? trim($values[3]) : ''),
                    'error' => $errorMsg,
                    'valores' => array_slice($values, 0, 10) // Primeros 10 valores para debug
                ];
            }
        } else {
            $errorMsg = "Registro con datos incompletos (solo " . count($values) . " valores, se requieren 40)";
            echo "⚠ ADVERTENCIA: $errorMsg\n";
            $errors++;

            $errorDetails[] = [
                'registro' => $processedRecords,
                'codigo' => 'DATOS_INCOMPLETOS',
                'nombre' => 'REGISTRO_INCOMPLETO',
                'error' => $errorMsg,
                'valores' => array_slice($values, 0, 5) // Primeros 5 valores para debug
            ];
        }

        // Limitar output para no saturar la consola
        if ($processedRecords % 50 == 0) {
            echo "\n=== PROCESADOS $processedRecords REGISTROS ===\n";
        }
    }
}

// Insertar todos los pastores en la base de datos
echo "\n--- INSERTANDO REGISTROS EN BASE DE DATOS ---\n";
$pastorIds = [];
foreach ($pastoresTemporal as $codigo => $data) {
    try {
        $tempNombreConyuge = $data['temp_nombre_conyuge'];
        unset($data['temp_nombre_conyuge']);

        $pastorId = Capsule::table('pastores')->insertGetId($data);
        $pastorIds[$codigo] = $pastorId;
        echo "✓ Insertado pastor ID $pastorId: {$data['nombres']} {$data['apellidos']}\n";
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
        echo "✗ ERROR insertando pastor con código $codigo: $errorMsg\n";
        $errors++;

        $errorDetails[] = [
            'registro' => 'INSERT_DB',
            'codigo' => $codigo,
            'nombre' => $data['nombres'] . ' ' . $data['apellidos'],
            'error' => "ERROR_DB: $errorMsg",
            'valores' => array_keys($data) // Nombres de campos
        ];
    }
}

// Actualizar relaciones de cónyuges
echo "\n--- PROCESANDO RELACIONES DE CÓNYUGES ---\n";
foreach ($pastoresTemporal as $codigo => $data) {
    if (!empty($data['temp_nombre_conyuge']) && isset($pastorIds[$codigo])) {
        $pastorId = $pastorIds[$codigo];
        $nombreConyuge = $data['temp_nombre_conyuge'];
        echo "Nota: El pastor con código $codigo tiene un cónyuge ('$nombreConyuge') que debe ser vinculado manualmente.\n";
    }
}

// Generar reporte detallado
echo "\n\n=== REPORTE FINAL DE IMPORTACIÓN ===\n";
echo "Registros procesados: $processedRecords\n";
echo "Registros exitosos: $inserted\n";
echo "Errores totales: $errors\n";
echo "Porcentaje de éxito: " . round(($inserted / $processedRecords) * 100, 2) . "%\n";

// Guardar archivo de log con errores detallados
$logFile = 'importacion_pastores_log_' . date('Y-m-d_H-i-s') . '.txt';
$logContent = "REPORTE DE IMPORTACIÓN DE PASTORES\n";
$logContent .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
$logContent .= "=====================================\n\n";
$logContent .= "RESUMEN:\n";
$logContent .= "- Total registros procesados: $processedRecords\n";
$logContent .= "- Registros exitosos: $inserted\n";
$logContent .= "- Errores totales: $errors\n";
$logContent .= "- Porcentaje de éxito: " . round(($inserted / $processedRecords) * 100, 2) . "%\n\n";

if (!empty($errorDetails)) {
    $logContent .= "\nDETALLES DE ERRORES:\n";
    $logContent .= "=====================================\n";
    foreach ($errorDetails as $error) {
        $logContent .= "\nRegistro #" . $error['registro'] . "\n";
        $logContent .= "Código: " . $error['codigo'] . "\n";
        $logContent .= "Nombre: " . $error['nombre'] . "\n";
        $logContent .= "Error: " . $error['error'] . "\n";
        if (!empty($error['valores'])) {
            $logContent .= "Valores (debug): " . json_encode($error['valores']) . "\n";
        }
        $logContent .= "---\n";
    }
}

file_put_contents($logFile, $logContent);
echo "\n✓ Archivo de log generado: $logFile\n";

// Guardar archivo CSV con errores para análisis en Excel
$csvFile = 'errores_importacion_pastores_' . date('Y-m-d_H-i-s') . '.csv';
$csvContent = "Registro,Código,Nombre,Error,Valores_Debug\n";
foreach ($errorDetails as $error) {
    $csvContent .= $error['registro'] . ",";
    $csvContent .= '"' . str_replace('"', '""', $error['codigo']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $error['nombre']) . '",';
    $csvContent .= '"' . str_replace('"', '""', $error['error']) . '",';
    $csvContent .= '"' . str_replace('"', '""', json_encode($error['valores'])) . '"' . "\n";
}
file_put_contents($csvFile, $csvContent);
echo "✓ Archivo CSV con errores generado: $csvFile\n";

echo "\n=== IMPORTACIÓN FINALIZADA ===\n";

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
 * Función para convertir valores a booleano
 */
function convertToBoolean($value) {
    if ($value === null) return false;
    if (is_bool($value)) return $value;
    if (is_numeric($value)) return boolval($value);

    $value = strtoupper(trim($value));
    return in_array($value, ['SI', 'YES', 'TRUE', '1']);
}

/**
 * Función para formatear fechas
 */
function formatDate($date) {
    if (empty($date) || trim($date) === 'NULL') return null;

    // Limpiar espacios y caracteres extraños
    $date = trim($date);

    // Intentar diferentes formatos de fecha
    $formats = ['d-m-Y', 'Y-m-d'];

    foreach ($formats as $format) {
        $dateTime = DateTime::createFromFormat($format, $date);
        if ($dateTime !== false) {
            return $dateTime->format('Y-m-d');
        }
    }

    // Si no coincide con ninguno de los formatos, intentar parsear directamente
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

/**
 * Función para obtener o crear un municipio
 */
function getOrCreateMunicipio($nombre, &$cache) {
    $nombre = trim($nombre);
    if (empty($nombre)) return null;

    // Verificar en caché
    if (isset($cache[$nombre])) {
        return $cache[$nombre];
    }

    // Buscar en la base de datos
    $municipio = Capsule::table('municipios')->where('nombre', $nombre)->first();

    if ($municipio) {
        $cache[$nombre] = $municipio->id;
        return $municipio->id;
    }

    // Crear nuevo municipio si no existe
    $id = Capsule::table('municipios')->insertGetId([
        'nombre' => $nombre,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $cache[$nombre] = $id;
    return $id;
}

/**
 * Función para obtener o crear un estado
 */
function getOrCreateEstado($nombre, &$cache) {
    $nombre = trim($nombre);
    if (empty($nombre)) return null;

    // Verificar en caché
    if (isset($cache[$nombre])) {
        return $cache[$nombre];
    }

    // Buscar en la base de datos
    $estado = Capsule::table('estados')->where('nombre', $nombre)->first();

    if ($estado) {
        $cache[$nombre] = $estado->id;
        return $estado->id;
    }

    // Crear nuevo estado si no existe
    $id = Capsule::table('estados')->insertGetId([
        'nombre' => $nombre,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $cache[$nombre] = $id;
    return $id;
}

/**
 * Función para obtener o crear una parroquia
 */
function getOrCreateParroquia($nombre, &$cache) {
    $nombre = trim($nombre);
    if (empty($nombre)) return null;

    // Verificar en caché
    if (isset($cache[$nombre])) {
        return $cache[$nombre];
    }

    // Buscar en la base de datos
    $parroquia = Capsule::table('parroquias')->where('nombre', $nombre)->first();

    if ($parroquia) {
        $cache[$nombre] = $parroquia->id;
        return $parroquia->id;
    }

    // Crear nueva parroquia si no existe
    $id = Capsule::table('parroquias')->insertGetId([
        'nombre' => $nombre,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $cache[$nombre] = $id;
    return $id;
}
?>
