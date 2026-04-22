<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LARAVEL EMPRESAS TABLE ===\n";
$empresas = \App\Models\Empresa::select('id', 'razon_social', 'whatsapp_api_key', 'whatsapp_active', 'whatsapp_status')->get();

foreach ($empresas as $empresa) {
    echo "ID: {$empresa->id}\n";
    echo "Name: {$empresa->razon_social}\n";
    echo "WhatsApp API Key: " . ($empresa->whatsapp_api_key ?: 'NOT SET') . "\n";
    echo "WhatsApp Active: " . ($empresa->whatsapp_active ? 'YES' : 'NO') . "\n";
    echo "WhatsApp Status: " . ($empresa->whatsapp_status ?: 'N/A') . "\n";
    echo "---\n";
}

echo "\n=== CHECKING NODE.JS COMPANIES TABLE (larawhatsapp DB) ===\n";

// Check Node.js database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=larawhatsapp', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First, let's see what columns exist in the companies table
    $stmt = $pdo->query("DESCRIBE companies");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Companies table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']} ({$column['Type']})\n";
    }
    echo "\n";
    
    $stmt = $pdo->query("SELECT id, name, apiKey, isActive FROM companies");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($companies as $company) {
        echo "ID: {$company['id']}\n";
        echo "Name: {$company['name']}\n";
        echo "API Key: {$company['apiKey']}\n";
        echo "Active: " . ($company['isActive'] ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
    
    // Find matching API keys
    echo "\n=== CHECKING FOR MATCHING API KEYS ===\n";
    foreach ($empresas as $empresa) {
        if ($empresa->whatsapp_api_key) {
            $found = false;
            foreach ($companies as $company) {
                if ($company['apiKey'] === $empresa->whatsapp_api_key) {
                    echo "✅ MATCH FOUND: Empresa '{$empresa->razon_social}' (ID: {$empresa->id}) matches Node.js Company '{$company['name']}' (ID: {$company['id']})\n";
                    echo "API Key: {$empresa->whatsapp_api_key}\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "❌ NO MATCH: Empresa '{$empresa->razon_social}' (ID: {$empresa->id}) has API key '{$empresa->whatsapp_api_key}' but no matching Node.js company found\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "Error connecting to Node.js database: " . $e->getMessage() . "\n";
    echo "Trying SQLite database...\n";
    
    // Try SQLite if MySQL fails
    try {
        $sqlitePath = __DIR__ . '/resources/js/whatsapp/database.sqlite';
        if (file_exists($sqlitePath)) {
            $pdo = new PDO('sqlite:' . $sqlitePath);
            $stmt = $pdo->query("SELECT id, name, apiKey, isActive FROM companies");
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($companies as $company) {
                echo "ID: {$company['id']}\n";
                echo "Name: {$company['name']}\n";
                echo "API Key: {$company['apiKey']}\n";
                echo "Active: " . ($company['isActive'] ? 'YES' : 'NO') . "\n";
                echo "---\n";
            }
        } else {
            echo "SQLite database not found at: $sqlitePath\n";
        }
    } catch (Exception $sqliteError) {
        echo "SQLite Error: " . $sqliteError->getMessage() . "\n";
    }
}