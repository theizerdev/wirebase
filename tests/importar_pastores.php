<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Cargar variables de entorno si existe el archivo .env
if (file_exists('.env')) {
    $env = parse_ini_file('.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
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

echo "Iniciando importación de pastores...\n";

// Leer el archivo SQL
$sqlFile = 'pastores.sql';
if (!file_exists($sqlFile)) {
    die("Error: No se encuentra el archivo $sqlFile\n");
}

$content = file_get_contents($sqlFile);
$lines = explode("\n", $content);

// Contadores
$inserted = 0;
$errors = 0;

// Procesar cada línea
foreach ($lines as $line) {
    // Saltar líneas vacías o comentarios
    $trimmedLine = trim($line);
    if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0) {
        continue;
    }
    
    // Buscar sentencias INSERT
    if (stripos($trimmedLine, 'INSERT INTO `pastors`') === 0) {
        // Extraer los valores
        preg_match('/VALUES\s*(.*)/', $trimmedLine, $matches);
        if (!isset($matches[1])) {
            continue;
        }
        
        // Separar los registros múltiples
        $valuesPart = trim($matches[1]);
        // Remover paréntesis inicial y final
        $valuesPart = substr($valuesPart, 1, -1);
        
        // Dividir por '), (' para obtener cada registro
        $records = explode('), (', $valuesPart);
        
        foreach ($records as $record) {
            // Parsear los valores del registro
            $values = parseValues($record);
            
            if (count($values) >= 40) { // Verificar que tenemos suficientes valores
                try {
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
                        'fe_nacimiento' => formatDate($values[12]),
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
                        'municipio' => !empty($values[31]) ? $values[31] : null,
                        'telefono_hab' => !empty($values[32]) ? $values[32] : null,
                        'telefono_tlf' => !empty($values[33]) ? $values[33] : null,
                        'telefono_otro' => !empty($values[34]) ? $values[34] : null,
                        'mencion' => !empty($values[36]) ? $values[36] : null,
                        'cargo_nacional' => !empty($values[37]) ? $values[37] : null,
                        'created_at' => !empty($values[38]) ? $values[38] : date('Y-m-d H:i:s'),
                        'updated_at' => !empty($values[39]) ? $values[39] : date('Y-m-d H:i:s'),
                        'estado' => isset($values[40]) ? $values[40] : null,
                        'parroquia' => isset($values[41]) ? $values[41] : null,
                    ];
                    
                    // Insertar en la base de datos
                    $pastorId = Capsule::table('pastores')->insertGetId($data);
                    $inserted++;
                    echo "Insertado pastor ID $pastorId: {$data['nombres']} {$data['apellidos']}\n";
                } catch (Exception $e) {
                    echo "Error insertando pastor: " . $e->getMessage() . "\n";
                    $errors++;
                }
            }
        }
    }
}

echo "Importación completada.\n";
echo "Registros insertados: $inserted\n";
echo "Errores: $errors\n";

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
    if (empty($date)) return null;
    
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
?>