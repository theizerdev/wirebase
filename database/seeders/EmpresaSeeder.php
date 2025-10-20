<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmpresaSeeder extends Seeder
{
    public function run()
    {
        $empresas = [
            [
                'razon_social' => 'Tech Solutions SA',
                'direccion' => 'Av. Principal 123',
                'documento' => '1234567890',
            ],
            [
                'razon_social' => 'Comercializadora XYZ',
                'direccion' => 'Calle Comercial 456',
                'documento' => '0987654321',
            ],
            [
                'razon_social' => 'Servicios Integrales LTDA',
                'direccion' => 'Boulevard Industrial 789',
                'documento' => '5432167890',
            ],
            [
                'razon_social' => 'Distribuidora ABC',
                'direccion' => 'Carrera 10 #20-30',
                'documento' => '6789054321',
            ],
            [
                'razon_social' => 'Consultoría Profesional',
                'direccion' => 'Av. Consultores 567',
                'documento' => '1357924680',
            ],
        ];

        foreach ($empresas as $empresa) {
            Empresa::create($empresa);
        }
    }
}
