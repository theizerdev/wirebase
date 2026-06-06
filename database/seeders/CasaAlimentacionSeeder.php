<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CasaAlimentacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;

class CasaAlimentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing estados, municipios and parroquias for relationships
        $estado = Estado::first();
        $municipio = $estado ? Municipio::where('estado_id', $estado->id)->first() : null;
        $parroquia = $municipio ? Parroquia::where('municipio_id', $municipio->id)->first() : null;

        CasaAlimentacion::create([
            'fecha' => now()->toDateString(),
            'codigo' => 'CDA-001',
            'estado_id' => $estado?->id,
            'municipio_id' => $municipio?->id,
            'parroquia_id' => $parroquia?->id,
            'sector' => 'Sector Centro',
            'calle_avenida' => 'Avenida Principal',
            'numero_vivienda' => '123',
            'punto_referencia' => 'Frente a la plaza Bolívar',
            'longitud' => -66.9036,
            'latitud' => 10.4806,
            'zona_base_misiones' => true,
            'distancia_a_base_misiones' => 2.5,
            'consejo_comunal' => 'Consejo Comunal Simón Bolívar',
            'vocero_alimentacion' => 'Juan Pérez',
            'telefono_principal' => '0414-1234567',
            'telefono_secundario' => '0424-7654321',
            'estado_cda' => 'Operativa',
            'motivo_inoperatividad' => null,
        ]);

        CasaAlimentacion::create([
            'fecha' => now()->toDateString(),
            'codigo' => 'CDA-002',
            'estado_id' => $estado?->id,
            'municipio_id' => $municipio?->id,
            'parroquia_id' => $parroquia?->id,
            'sector' => 'Sector Norte',
            'calle_avenida' => 'Calle Libertador',
            'numero_vivienda' => '456',
            'punto_referencia' => 'Cerca del mercado municipal',
            'longitud' => -66.9136,
            'latitud' => 10.4906,
            'zona_base_misiones' => false,
            'distancia_a_base_misiones' => 5.0,
            'consejo_comunal' => 'Consejo Comunal Libertador',
            'vocero_alimentacion' => 'María González',
            'telefono_principal' => '0416-9876543',
            'telefono_secundario' => '0426-1234567',
            'estado_cda' => 'Operativa',
            'motivo_inoperatividad' => null,
        ]);

        CasaAlimentacion::create([
            'fecha' => now()->toDateString(),
            'codigo' => 'CDA-003',
            'estado_id' => $estado?->id,
            'municipio_id' => $municipio?->id,
            'parroquia_id' => $parroquia?->id,
            'sector' => 'Sector Sur',
            'calle_avenida' => 'Avenida Universidad',
            'numero_vivienda' => '789',
            'punto_referencia' => 'Detrás de la universidad',
            'longitud' => -66.8936,
            'latitud' => 10.4706,
            'zona_base_misiones' => true,
            'distancia_a_base_misiones' => 1.2,
            'consejo_comunal' => 'Consejo Comunal Universitario',
            'vocero_alimentacion' => 'Carlos Rodríguez',
            'telefono_principal' => '0412-5555555',
            'telefono_secundario' => '0422-6666666',
            'estado_cda' => 'Inoperativa',
            'motivo_inoperatividad' => 'En reparación por daños estructurales',
        ]);
    }
}
