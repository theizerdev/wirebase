<?php

namespace Database\Seeders;

use App\Models\Moto;
use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotosSeeder extends Seeder
{
    public function run()
    {
        // Obtener la primera empresa o crear una por defecto
        $empresa = Empresa::first();
        
        if (!$empresa) {
            $this->command->error('No se encontró ninguna empresa. Por favor, ejecute EmpresaSeeder primero.');
            return;
        }

        $motos = [
            // Motos Honda
            [
                'marca' => 'Honda',
                'modelo' => 'CB 190R',
                'anio' => 2024,
                'color_principal' => 'Rojo',
                'cilindrada' => '190cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Moto deportiva Honda CB 190R, ideal para ciudad y carretera',
                'precio_venta_base' => 3500.00,
                'costo_referencial' => 2800.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Honda',
                'modelo' => 'XR 190L',
                'anio' => 2024,
                'color_principal' => 'Azul',
                'cilindrada' => '190cc',
                'tipo' => 'Trabajo',
                'descripcion' => 'Moto dual sport Honda XR 190L, perfecta para todo terreno',
                'precio_venta_base' => 4200.00,
                'costo_referencial' => 3400.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Honda',
                'modelo' => 'PCX 150',
                'anio' => 2024,
                'color_principal' => 'Blanco',
                'cilindrada' => '150cc',
                'tipo' => 'Paseo',
                'descripcion' => 'Scooter Honda PCX 150, máxima comodidad y estilo',
                'precio_venta_base' => 2800.00,
                'costo_referencial' => 2200.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos Yamaha
            [
                'marca' => 'Yamaha',
                'modelo' => 'FZ 25',
                'anio' => 2024,
                'color_principal' => 'Negro',
                'cilindrada' => '250cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Yamaha FZ 25, potencia y rendimiento excepcional',
                'precio_venta_base' => 4500.00,
                'costo_referencial' => 3600.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Yamaha',
                'modelo' => 'XTZ 150',
                'anio' => 2024,
                'color_principal' => 'Verde',
                'cilindrada' => '150cc',
                'tipo' => 'Trabajo',
                'descripcion' => 'Yamaha XTZ 150, confiable para todo tipo de terreno',
                'precio_venta_base' => 3200.00,
                'costo_referencial' => 2600.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Yamaha',
                'modelo' => 'NMAX 155',
                'anio' => 2024,
                'color_principal' => 'Gris',
                'cilindrada' => '155cc',
                'tipo' => 'Paseo',
                'descripcion' => 'Scooter Yamaha NMAX 155, tecnología de última generación',
                'precio_venta_base' => 3800.00,
                'costo_referencial' => 3000.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos Suzuki
            [
                'marca' => 'Suzuki',
                'modelo' => 'GSX R150',
                'anio' => 2024,
                'color_principal' => 'Azul/Blanco',
                'cilindrada' => '150cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Suzuki GSX R150, deportividad pura en 150cc',
                'precio_venta_base' => 3800.00,
                'costo_referencial' => 3000.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Suzuki',
                'modelo' => 'V-Strom 150',
                'anio' => 2024,
                'color_principal' => 'Rojo',
                'cilindrada' => '150cc',
                'tipo' => 'Trabajo',
                'descripcion' => 'Suzuki V-Strom 150, versatilidad y confiabilidad',
                'precio_venta_base' => 3600.00,
                'costo_referencial' => 2900.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Suzuki',
                'modelo' => 'Address 125',
                'anio' => 2024,
                'color_principal' => 'Plateado',
                'cilindrada' => '125cc',
                'tipo' => 'Paseo',
                'descripcion' => 'Suzuki Address 125, economía y practicidad',
                'precio_venta_base' => 2200.00,
                'costo_referencial' => 1800.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos Bajaj
            [
                'marca' => 'Bajaj',
                'modelo' => 'Dominar 400',
                'anio' => 2024,
                'color_principal' => 'Negro/Rojo',
                'cilindrada' => '400cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Bajaj Dominar 400, potencia y dominio en la carretera',
                'precio_venta_base' => 5500.00,
                'costo_referencial' => 4400.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Bajaj',
                'modelo' => 'Pulsar NS 200',
                'anio' => 2024,
                'color_principal' => 'Azul',
                'cilindrada' => '200cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Bajaj Pulsar NS 200, estilo naked y performance',
                'precio_venta_base' => 3200.00,
                'costo_referencial' => 2600.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'Bajaj',
                'modelo' => 'CT 100',
                'anio' => 2024,
                'color_principal' => 'Rojo',
                'cilindrada' => '100cc',
                'tipo' => 'Trabajo',
                'descripcion' => 'Bajaj CT 100, máxima economía y durabilidad',
                'precio_venta_base' => 1800.00,
                'costo_referencial' => 1400.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos KTM
            [
                'marca' => 'KTM',
                'modelo' => 'Duke 200',
                'anio' => 2024,
                'color_principal' => 'Naranja',
                'cilindrada' => '200cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'KTM Duke 200, pura adrenalina en ciudad',
                'precio_venta_base' => 4200.00,
                'costo_referencial' => 3400.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'KTM',
                'modelo' => 'RC 200',
                'anio' => 2024,
                'color_principal' => 'Naranja/Blanco',
                'cilindrada' => '200cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'KTM RC 200, deportividad extrema en 200cc',
                'precio_venta_base' => 4800.00,
                'costo_referencial' => 3800.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos Kawasaki
            [
                'marca' => 'Kawasaki',
                'modelo' => 'Ninja 300',
                'anio' => 2024,
                'color_principal' => 'Verde/Negro',
                'cilindrada' => '300cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Kawasaki Ninja 300, legendaria deportividad',
                'precio_venta_base' => 6200.00,
                'costo_referencial' => 5000.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motas TVS
            [
                'marca' => 'TVS',
                'modelo' => 'Apache RTR 200',
                'anio' => 2024,
                'color_principal' => 'Rojo/Negro',
                'cilindrada' => '200cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'TVS Apache RTR 200, tecnología racing',
                'precio_venta_base' => 3500.00,
                'costo_referencial' => 2800.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            [
                'marca' => 'TVS',
                'modelo' => 'Rockz 125',
                'anio' => 2024,
                'color_principal' => 'Azul',
                'cilindrada' => '125cc',
                'tipo' => 'Paseo',
                'descripcion' => 'TVS Rockz 125, estilo y comodidad urbana',
                'precio_venta_base' => 2100.00,
                'costo_referencial' => 1700.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motos Zongshen
            [
                'marca' => 'Zongshen',
                'modelo' => 'ZS 200GY',
                'anio' => 2024,
                'color_principal' => 'Azul/Blanco',
                'cilindrada' => '200cc',
                'tipo' => 'Trabajo',
                'descripcion' => 'Zongshen ZS 200GY, versatilidad todo terreno',
                'precio_venta_base' => 2800.00,
                'costo_referencial' => 2200.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
            // Motas Benelli
            [
                'marca' => 'Benelli',
                'modelo' => 'TNT 300',
                'anio' => 2024,
                'color_principal' => 'Rojo',
                'cilindrada' => '300cc',
                'tipo' => 'Deportiva',
                'descripcion' => 'Benelli TNT 300, italiana con carácter',
                'precio_venta_base' => 5800.00,
                'costo_referencial' => 4600.00,
                'activo' => true,
                'empresa_id' => $empresa->id,
            ],
        ];

        $this->command->info('Creando ' . count($motos) . ' modelos de motos...');

        foreach ($motos as $moto) {
            try {
                Moto::create($moto);
            } catch (\Exception $e) {
                $this->command->error('Error al crear moto: ' . $e->getMessage());
            }
        }

        $this->command->info('✓ Motos creadas exitosamente');
    }
}