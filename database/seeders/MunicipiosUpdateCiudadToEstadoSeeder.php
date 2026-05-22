<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosUpdateCiudadToEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds to update municipios from ciudad_id to estado_id
     */
    public function run(): void
    {
        // Antes de ejecutar esta migración, debemos asegurarnos de que la migración 
        // que cambia ciudad_id a estado_id ya se haya ejecutado
        
        $this->command->info('🔄 Actualizando relación de municipios de ciudad a estado...');
        
        // Actualizamos los municipios existentes para que tengan estado_id en lugar de ciudad_id
        // Solo para casos donde no hay migración automática
        $municipios = DB::table('municipios')
            ->join('ciudades', 'municipios.estado_id', '=', 'ciudades.id') // estado_id temporalmente es ciudad_id
            ->select('municipios.id', 'ciudades.estado_id as ciudad_estado_id')
            ->get();

        $updatedCount = 0;
        foreach ($municipios as $municipio) {
            DB::table('municipios')
                ->where('id', $municipio->id)
                ->update(['estado_id' => $municipio->ciudad_estado_id]);
            $updatedCount++;
        }

        $this->command->info("✅ {$updatedCount} municipios actualizados con éxito");
        
        $this->command->info('📌 Nota: Esta migración asume que los municipios ya han sido migrados');
        $this->command->info('   desde ciudad_id a estado_id mediante la migración correspondiente.');
    }
}