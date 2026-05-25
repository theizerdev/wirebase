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

echo "=== REORGANIZANDO IDs DE IGLESIAS ===\n\n";

try {
    // Desactivar verificación de claves foráneas temporalmente
    Capsule::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Obtener todas las iglesias ordenadas por ID actual
    $iglesias = Capsule::table('iglesias')->orderBy('id')->get();
    
    echo "Iglesias encontradas: " . count($iglesias) . "\n";
    
    // Crear tabla temporal
    Capsule::statement('CREATE TEMPORARY TABLE temp_iglesias_mapping (old_id INT, new_id INT)');
    
    $newId = 1;
    foreach ($iglesias as $iglesia) {
        // Insertar mapeo en tabla temporal
        Capsule::table('temp_iglesias_mapping')->insert([
            'old_id' => $iglesia->id,
            'new_id' => $newId
        ]);
        $newId++;
    }
    
    // Actualizar referencias en iglesia_pastor
    Capsule::statement('
        UPDATE iglesia_pastor ip 
        JOIN temp_iglesias_mapping tim ON ip.iglesia_id = tim.old_id 
        SET ip.iglesia_id = tim.new_id
    ');
    
    // Actualizar IDs en iglesias
    Capsule::statement('
        UPDATE iglesias i 
        JOIN temp_iglesias_mapping tim ON i.id = tim.old_id 
        SET i.id = tim.new_id
    ');
    
    // Resetear AUTO_INCREMENT
    Capsule::statement('ALTER TABLE iglesias AUTO_INCREMENT = ' . $newId);
    
    // Reactivar verificación de claves foráneas
    Capsule::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "✅ IDs reorganizados exitosamente\n";
    echo "Nuevo rango de IDs: 1 - " . ($newId - 1) . "\n";
    echo "Próximo AUTO_INCREMENT: " . $newId . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    // Reactivar verificación de claves foráneas en caso de error
    Capsule::statement('SET FOREIGN_KEY_CHECKS=1');
}