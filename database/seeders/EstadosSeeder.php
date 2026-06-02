<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Amazonas', 'iso_3166_2' => 'VE-X', 'latitud' => 3.2167, 'longitud' => -65.2167],
            ['nombre' => 'Anzoátegui', 'iso_3166_2' => 'VE-B', 'latitud' => 9.5167, 'longitud' => -64.6167],
            ['nombre' => 'Apure', 'iso_3166_2' => 'VE-C', 'latitud' => 7.0833, 'longitud' => -67.7500],
            ['nombre' => 'Aragua', 'iso_3166_2' => 'VE-D', 'latitud' => 10.2446, 'longitud' => -67.5939],
            ['nombre' => 'Barinas', 'iso_3166_2' => 'VE-E', 'latitud' => 8.6333, 'longitud' => -70.2000],
            ['nombre' => 'Bolívar', 'iso_3166_2' => 'VE-F', 'latitud' => 8.1333, 'longitud' => -63.5500],
            ['nombre' => 'Carabobo', 'iso_3166_2' => 'VE-G', 'latitud' => 10.1567, 'longitud' => -68.0475],
            ['nombre' => 'Cojedes', 'iso_3166_2' => 'VE-H', 'latitud' => 9.3833, 'longitud' => -68.3333],
            ['nombre' => 'Delta Amacuro', 'iso_3166_2' => 'VE-Y', 'latitud' => 8.8833, 'longitud' => -61.3500],
            ['nombre' => 'Distrito Capital', 'iso_3166_2' => 'VE-A', 'latitud' => 10.5000, 'longitud' => -66.9167],
            ['nombre' => 'Falcón', 'iso_3166_2' => 'VE-I', 'latitud' => 11.4000, 'longitud' => -69.6667],
            ['nombre' => 'Guárico', 'iso_3166_2' => 'VE-J', 'latitud' => 8.7500, 'longitud' => -66.2333],
            ['nombre' => 'Lara', 'iso_3166_2' => 'VE-K', 'latitud' => 10.0636, 'longitud' => -69.3347],
            ['nombre' => 'Mérida', 'iso_3166_2' => 'VE-L', 'latitud' => 8.6000, 'longitud' => -71.1500],
            ['nombre' => 'Miranda', 'iso_3166_2' => 'VE-M', 'latitud' => 10.2500, 'longitud' => -66.4167],
            ['nombre' => 'Monagas', 'iso_3166_2' => 'VE-N', 'latitud' => 9.7500, 'longitud' => -63.1667],
            ['nombre' => 'Nueva Esparta', 'iso_3166_2' => 'VE-O', 'latitud' => 11.0000, 'longitud' => -63.8333],
            ['nombre' => 'Portuguesa', 'iso_3166_2' => 'VE-P', 'latitud' => 9.7500, 'longitud' => -69.3333],
            ['nombre' => 'Sucre', 'iso_3166_2' => 'VE-R', 'latitud' => 10.4167, 'longitud' => -63.2500],
            ['nombre' => 'Táchira', 'iso_3166_2' => 'VE-S', 'latitud' => 7.9167, 'longitud' => -72.1667],
            ['nombre' => 'Trujillo', 'iso_3166_2' => 'VE-T', 'latitud' => 9.3667, 'longitud' => -70.4333],
            ['nombre' => 'Vargas', 'iso_3166_2' => 'VE-X', 'latitud' => 10.6000, 'longitud' => -66.9333],
            ['nombre' => 'Yaracuy', 'iso_3166_2' => 'VE-U', 'latitud' => 10.0833, 'longitud' => -68.7500],
            ['nombre' => 'Zulia', 'iso_3166_2' => 'VE-V', 'latitud' => 9.7500, 'longitud' => -72.2500],
        ];

        foreach ($estados as $estado) {
            DB::table('estados')->updateOrInsert(
                ['iso_3166_2' => $estado['iso_3166_2']],
                [
                    'nombre' => $estado['nombre'],
                    'latitud' => $estado['latitud'],
                    'longitud' => $estado['longitud']
                ]
            );
        }
    }
}