<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Zona;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la primera empresa o crear una por defecto
        $empresa = Empresa::first();
        $zonas   = Zona::where('empresa_id', $empresa->id)->delete();
        
        if (!$empresa) {
            $this->command->info('No hay empresas creadas. Ejecute primero el seeder de empresas.');
            return;
        }
        
        // Zonas globales de empresa (aplican a todas las sucursales)
        $zonasGlobales = [
            [
                'nombre' => 'Zona Administrativa',
                'codigo' => 'ZA-001',
                'descripcion' => 'Área administrativa y oficinas principales',
                'distrito' => 'Distrito Central',
                'sucursal_id' => null,
            ],
            [
                'nombre' => 'Zona de Atención al Cliente',
                'codigo' => 'ZAC-001',
                'descripcion' => 'Áreas de recepción y atención directa al cliente',
                'distrito' => 'Distrito Central',
                'sucursal_id' => null,
            ],
        ];

        foreach ($zonasGlobales as $zonaData) {
            Zona::create(array_merge($zonaData, [
                'empresa_id' => $empresa->id,
                'activo' => true,
            ]));
        }
        
        // Zonas por sucursal
        $sucursales = Sucursal::where('empresa_id', $empresa->id)->get();

        foreach ($sucursales as $sucursal) {
            $zonasPorSucursal = [
                [
                    'nombre' => "Zona {$sucursal->nombre} - Planta Baja",
                    'codigo' => "ZPB-{$sucursal->id}",
                    'descripcion' => "Planta baja de la sucursal {$sucursal->nombre}",
                    'distrito' => "Distrito " . $sucursal->nombre,
                    'sucursal_id' => $sucursal->id,
                ],
                [
                    'nombre' => "Zona {$sucursal->nombre} - Primer Piso",
                    'codigo' => "ZPP-{$sucursal->id}",
                    'descripcion' => "Primer piso de la sucursal {$sucursal->nombre}",
                    'distrito' => "Distrito " . $sucursal->nombre,
                    'sucursal_id' => $sucursal->id,
                ],
            ];

            foreach ($zonasPorSucursal as $zonaData) {
                Zona::create(array_merge($zonaData, [
                    'empresa_id' => $empresa->id,
                    'activo' => true,
                ]));
            }
        }
    
        
        $this->command->info('Zonas creadas exitosamente.');
    }
}
