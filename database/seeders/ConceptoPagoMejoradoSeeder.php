<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConceptoPago;

class ConceptoPagoMejoradoSeeder extends Seeder
{
    public function run(): void
    {
        $conceptos = [
            ['nombre' => 'Cuota Inicial', 'descripcion' => 'Pago de cuota inicial'],
            ['nombre' => 'Mensualidad', 'descripcion' => 'Pago mensual de colegiatura'],
            ['nombre' => 'Mora', 'descripcion' => 'Interés por pago tardío'],
            ['nombre' => 'Otros', 'descripcion' => 'Otros conceptos']
        ];

        foreach ($conceptos as $concepto) {
            ConceptoPago::firstOrCreate(
                ['nombre' => $concepto['nombre']],
                [
                    'descripcion' => $concepto['descripcion'],
                    'activo' => true,
                    'empresa_id' => 1,
                    'sucursal_id' => 1
                ]
            );
        }
    }
}
