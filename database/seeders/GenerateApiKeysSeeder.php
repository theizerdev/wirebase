<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;

class GenerateApiKeysSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::whereNull('api_key')->each(function ($empresa) {
            $empresa->update(['api_key' => Empresa::generateApiKey()]);
        });
    }
}