<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

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

echo "=== ANÁLISIS DE ESTRUCTURA DE IGLESIAS ===\n\n";

// Leer el archivo SQL
$sqlFile = __DIR__ . '/iglesias.sql';
if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo iglesias.sql\n";
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);

// Extraer estructura de la tabla iglesias del SQL
preg_match("/INSERT INTO `iglesias` \(([^)]+)\)/", $sqlContent, $matches);
if (isset($matches[1])) {
    // Limpiar y dividir las columnas
    $columnsStr = str_replace('`', '', $matches[1]);
    $sqlColumns = array_map('trim', explode(',', $columnsStr));
    echo "COLUMNAS EN EL ARCHIVO SQL (iglesias):\n";
    foreach ($sqlColumns as $i => $column) {
        echo ($i + 1) . ". " . $column . "\n";
    }
} else {
    echo "No se pudo extraer la estructura de iglesias del SQL\n";
    // Intentar con una expresión regular más flexible
    if (preg_match("/INSERT INTO `iglesias`[^(]*\(([^)]+)\)/s", $sqlContent, $matches2)) {
        $columnsStr = str_replace('`', '', $matches2[1]);
        $sqlColumns = array_map('trim', explode(',', $columnsStr));
        echo "COLUMNAS EN EL ARCHIVO SQL (iglesias) - método alternativo:\n";
        foreach ($sqlColumns as $i => $column) {
            echo ($i + 1) . ". " . $column . "\n";
        }
    }
}

echo "\n";

// Obtener estructura actual de la tabla iglesias
try {
    $columns = Capsule::select("SHOW COLUMNS FROM iglesias");
    echo "COLUMNAS EN LA BASE DE DATOS ACTUAL (iglesias):\n";
    foreach ($columns as $i => $column) {
        echo ($i + 1) . ". " . $column->Field . " (" . $column->Type . ")\n";
    }
} catch (Exception $e) {
    echo "Error al obtener estructura de iglesias: " . $e->getMessage() . "\n";
}

echo "\n=== ANÁLISIS DE ESTRUCTURA DE IGLESIA_PASTOR ===\n\n";

// Extraer estructura de la tabla iglesia_pastor del SQL
preg_match("/INSERT INTO `iglesia_pastor` \(([^)]+)\)/", $sqlContent, $matchesPastor);
if (isset($matchesPastor[1])) {
    $columnsStrPastor = str_replace('`', '', $matchesPastor[1]);
    $sqlColumnsPastor = array_map('trim', explode(',', $columnsStrPastor));
    echo "COLUMNAS EN EL ARCHIVO SQL (iglesia_pastor):\n";
    foreach ($sqlColumnsPastor as $i => $column) {
        echo ($i + 1) . ". " . $column . "\n";
    }
} else {
    echo "No se pudo extraer la estructura de iglesia_pastor del SQL\n";
    // Intentar con una expresión regular más flexible
    if (preg_match("/INSERT INTO `iglesia_pastor`[^(]*\(([^)]+)\)/s", $sqlContent, $matches2Pastor)) {
        $columnsStrPastor = str_replace('`', '', $matches2Pastor[1]);
        $sqlColumnsPastor = array_map('trim', explode(',', $columnsStrPastor));
        echo "COLUMNAS EN EL ARCHIVO SQL (iglesia_pastor) - método alternativo:\n";
        foreach ($sqlColumnsPastor as $i => $column) {
            echo ($i + 1) . ". " . $column . "\n";
        }
    }
}

echo "\n";

// Verificar si existe la tabla iglesia_pastor
try {
    $columnsPastor = Capsule::select("SHOW COLUMNS FROM iglesia_pastor");
    echo "COLUMNAS EN LA BASE DE DATOS ACTUAL (iglesia_pastor):\n";
    foreach ($columnsPastor as $i => $column) {
        echo ($i + 1) . ". " . $column->Field . " (" . $column->Type . ")\n";
    }
} catch (Exception $e) {
    echo "La tabla iglesia_pastor no existe o hay un error: " . $e->getMessage() . "\n";
    echo "Necesitas ejecutar la migración para crear esta tabla.\n";
}

echo "\n=== ESTADÍSTICAS DE DATOS ===\n\n";

// Contar registros en el SQL
preg_match_all("/INSERT INTO `iglesias`.*?VALUES\s*\((.*?)\);/s", $sqlContent, $matchesIglesias);
echo "Iglesias en el archivo SQL: " . count($matchesIglesias[1]) . "\n";

preg_match_all("/INSERT INTO `iglesia_pastor`.*?VALUES\s*\((.*?)\);/s", $sqlContent, $matchesIglesiaPastor);
echo "Relaciones iglesia_pastor en el archivo SQL: " . count($matchesIglesiaPastor[1]) . "\n";

// Contar registros en la base de datos
try {
    $countIglesias = Capsule::table('iglesias')->count();
    echo "Iglesias en la base de datos actual: " . $countIglesias . "\n";
} catch (Exception $e) {
    echo "Error al contar iglesias: " . $e->getMessage() . "\n";
}

try {
    $countRelaciones = Capsule::table('iglesia_pastor')->count();
    echo "Relaciones iglesia_pastor en la base de datos actual: " . $countRelaciones . "\n";
} catch (Exception $e) {
    echo "Error al contar relaciones iglesia_pastor: " . $e->getMessage() . "\n";
}

echo "\n=== ANÁLISIS COMPLETADO ===\n";