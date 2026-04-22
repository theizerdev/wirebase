<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check current companies and their API keys
        $companies = \App\Models\Empresa::select('id', 'razon_social', 'whatsapp_api_key', 'whatsapp_active')->get();
        
        echo "Current companies in database:\n";
        foreach ($companies as $company) {
            echo "ID: {$company->id}\n";
            echo "Name: {$company->razon_social}\n";
            echo "WhatsApp API Key: " . ($company->whatsapp_api_key ?: 'NOT SET') . "\n";
            echo "WhatsApp Active: " . ($company->whatsapp_active ? 'YES' : 'NO') . "\n";
            echo "---\n";
        }
        
        // If no companies have API keys, create a test one
        if ($companies->isEmpty() || $companies->whereNotNull('whatsapp_api_key')->isEmpty()) {
            echo "\nNo companies with WhatsApp API keys found. Creating test company...\n";
            
            $testCompany = \App\Models\Empresa::create([
                'razon_social' => 'Test Company WhatsApp',
                'documento' => '12345678',
                'direccion' => 'Test Address',
                'representante_legal' => 'Test Representative',
                'status' => true,
                'telefono' => '1234567890',
                'email' => 'test@example.com',
                'pais_id' => 1,
                'whatsapp_api_key' => 'wa_1_test_api_key_12345',
                'whatsapp_rate_limit' => 100,
                'whatsapp_active' => true
            ]);
            
            echo "Created test company with ID: {$testCompany->id}\n";
            echo "WhatsApp API Key: {$testCompany->whatsapp_api_key}\n";
        }
    }

    public function down(): void
    {
        // No rollback needed for this diagnostic migration
    }
};