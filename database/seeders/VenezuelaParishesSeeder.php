<?php

namespace Database\Seeders;

use App\Models\Municipio;
use App\Models\Parroquia;
use Illuminate\Database\Seeder;

class VenezuelaParishesSeeder extends Seeder
{
    /**
     * Run the database seeds for Venezuelan parishes.
     */
    public function run(): void
    {
        // Obtenemos municipios para asociarles parroquias
        $municipios = Municipio::with('estado.pais')->get()->filter(function($municipio) {
            return $municipio->estado->pais->codigo_iso2 === 'VE';
        });

        if ($municipios->count() === 0) {
            $this->command->error('❌ No se encontraron municipios de Venezuela. Ejecute primero VenezuelaMunicipalitiesSeeder.');
            return;
        }

        // Datos de parroquias por municipio - usando una selección representativa
        $parishesData = [
            // Municipio Libertador (Distrito Capital)
            'Libertador' => [
                'Altagracia', 'Antímano', 'Caricuao', 'Catedral', 'Coche', 'El Junquito', 'El Paraíso', 
                'El Recreo', 'El Valle', 'La Candelaria', 'La Pastora', 'Macarao', 'San Agustín', 
                'San Bernardino', 'San José', 'San Juan', 'Santa Rosalía', 'Santa Teresa', 'Sucre', '23 de Enero'
            ],
            // Municipio Chacao
            'Chacao' => [
                'Chacao'
            ],
            // Municipio El Hatillo
            'El Hatillo' => [
                'El Hatillo'
            ],
            // Municipio Baruta
            'Baruta' => [
                'Baruta', 'El Cafetal', 'Las Minas'
            ],
            // Municipio Sucre (Miranda)
            'Sucre' => [
                'Petare', 'Caucagua', 'Filas de Mariche', 'La Dolorita', 'Leoncio Martínez', 'Cúa', 'Nueva Cúa'
            ],
            // Municipio Maturín (Monagas)
            'Maturín' => [
                'Maturín', 'Alto de los Godos', 'Boquerón', 'Las Cocuítas', 'San Fermín', 'El Corozo', 'El Furrial', 'Jusepín', 'La Pica', 'San Simón'
            ],
            // Municipio Puerto La Cruz (Anzoátegui)
            'Puerto La Cruz' => [
                'Puerto La Cruz', 'Píritu', 'San Miguel', 'Santa Rosa', 'Boca del Pao', 'Pueblo Nuevo'
            ],
            // Municipio Valencia (Carabobo)
            'Valencia' => [
                'Candelaria', 'Catedral', 'El Socorro', 'Miguel Peña', 'Rafael Urdaneta', 'San Blas', 'San José', 'Santa Rosa', 'Negro Primero'
            ],
            // Municipio Maracaibo (Zulia)
            'Maracaibo' => [
                'Altagracia', 'Antonio Borjas Romero', 'Bolívar', 'Cacique Mara', 'Carmen Herrera', 'Ceferino Noriega', 'Cristo de Aranza', 'Coquivacoa', 'Donaldo García', 'Francisco Eugenio Bustamante', 'Idelfonso Vásquez', 'Juana de Avila', 'Luis Hurtado Higuera', 'Manuel Dagnino', 'Olegario Villalobos', 'Raúl Leoni', 'Santa Lucía'
            ],
            // Municipio San Cristóbal (Táchira)
            'San Cristóbal' => [
                'San Cristóbal', 'Boca de Grita', 'Coloncito', 'La Fría', 'Las Delicias', 'San Joaquín de Navay'
            ],
            // Municipio Mérida (Mérida)
            'Mérida' => [
                'Mérida', 'Aldemira', 'Andrés Eloy Blanco', 'Antonio Spinetti Dini', 'Cacute', 'Cajaruro', 'Carrillo', 'Casacoima', 'Chacopata', 'Choroni'
            ],
            // Municipio Barinas (Barinas)
            'Barinas' => [
                'Barinas', 'Alberto Arvelo Torrealba', 'San Silvestre', 'Santa Inés', 'Santa Lucía', 'Torunos', 'El Carmen', 'Rómulo Betancourt', 'Corazón de Jesús', 'Ramón Ignacio Méndez', 'Alto Barinas', 'Manuel Palacios Fajardo', 'Juan Antonio Rodríguez Domínguez'
            ],
            // Municipio Valera (Trujillo)
            'Valera' => [
                'Valera', 'Carache', 'Campo Elías', 'Santa Apolonia', 'El Paradero', 'Santa Cruz'
            ],
            // Municipio Maracay (Aragua)
            'Maracay' => [
                'Maracay', 'Bella Vista', 'Camuriquipa', 'Cecilio Acosta', 'El Limón', 'Las Delicias', 'Los Tacariguas', 'Tacarigua de la Laguna', 'Tacarigua de Mendoza', 'Tiara'
            ],
            // Municipio San Juan de los Morros (Guárico)
            'Bolívar' => [
                'San Juan de los Morros', 'Cazorla', 'San Rafael de Laya'
            ],
            // Municipio Calabozo (Aragua)
            'Girardot' => [
                'Calabozo', 'Espino', 'San Francisco de Cara', 'San Francisco de Guayabal'
            ],
            // Municipio Caroní (Bolívar)
            'Caroní' => [
                'Catedral', 'Zea', 'Orinoco', 'José Antonio Páez', 'Marhuanta', 'Agua Caliente', 'Sierra Maestra', 'Chirica', 'Dalla Costa', 'Santa Bárbara', 'La Sabanita', 'Panapana'
            ],
            // Municipio Cantaura (Anzoátegui)
            'Freites' => [
                'Cantaura', 'Libertad', 'Santa Rosa', 'Urbana Cantaura', 'Urbana Libertad', 'Urbana Santa Rosa'
            ],
            // Municipio Clarines (Anzoátegui)
            'Miranda' => [
                'Clarines', 'Guanape', 'Sabana de Uchire'
            ],
            // Municipio San Francisco (Zulia)
            'San Francisco' => [
                'San Francisco', 'El Bajo', 'Domitila Flores', 'Francisco Ochoa', 'Los Cortijos', 'Marcial Hernández'
            ],
            // Municipio San Antonio del Táchira (Táchira)
            'Colón' => [
                'San Antonio del Táchira', 'San José de Bolívar', 'Pedro María Morantes', 'San Simón', 'La Florida'
            ],
            // Municipio San Joaquín de Navay (Táchira)
            'Córdoba' => [
                'San Joaquín de Navay', 'Capacho Nuevo', 'Capacho Viejo', 'Quinimarí', 'Río Negro'
            ],
            // Municipio San Sebastián (Aragua)
            'San Sebastián' => [
                'San Sebastián'
            ],
            // Municipio Santos Michelena (Aragua)
            'Santos Michelena' => [
                'Santos Michelena'
            ],
            // Municipio Mario Briceño Iragorry (Aragua)
            'Mario Briceño Iragorry' => [
                'El Limón', 'Caña de Azúcar'
            ],
            // Municipio Camatagua (Aragua)
            'Camatagua' => [
                'Camatagua', 'Carmen de Cura'
            ],
            // Municipio Francisco de Miranda (Aragua)
            'Francisco Linares Alcántara' => [
                'Santa Rita', 'Francisco de Miranda', 'Moseñor Feliciano González'
            ],
            // Municipio La Victoria (Aragua)
            'José Félix Ribas' => [
                'La Victoria', 'Castaño Nueva', 'Nueva Cúa'
            ],
            // Municipio Las Delicias (Aragua)
            'Las Delicias' => [
                'Las Delicias'
            ],
            // Municipio Los Guayos (Carabobo)
            'Los Guayos' => [
                'Los Guayos'
            ],
            // Municipio Miranda (Carabobo)
            'Miranda' => [
                'Miranda'
            ],
            // Municipio Montalbán (Carabobo)
            'Montalbán' => [
                'Montalbán'
            ],
            // Municipio Naguanagua (Carabobo)
            'Naguanagua' => [
                'Naguanagua'
            ],
            // Municipio San Diego (Carabobo)
            'San Diego' => [
                'San Diego'
            ],
            // Municipio San Joaquín (Carabobo)
            'San Joaquín' => [
                'San Joaquín'
            ],
            // Municipio Bejuma (Carabobo)
            'Bejuma' => [
                'Bejuma', 'Canoabo', 'Simon Bolivar'
            ],
            // Municipio Güigüe (Carabobo)
            'Carlos Arvelo' => [
                'Güigüe', 'Aguas Calientes', 'Aragua de Barcelona'
            ],
            // Municipio Mariara (Carabobo)
            'Diego Ibarra' => [
                'Mariara', 'Aguas Calientes'
            ],
            // Municipio Guacara (Carabobo)
            'Guacara' => [
                'Guacara', 'Ciudad Alianza', 'Yagua'
            ],
            // Municipio Morón (Carabobo)
            'Juan José Mora' => [
                'Morón', 'Yagua'
            ],
            // Municipio Las Piraguas (Carabobo)
            'Las Piraguas' => [
                'Las Piraguas'
            ],
            // Municipio Puerto Cabello (Carabobo)
            'Puerto Cabello' => [
                'Bartolomé Salóm', 'Democracia', 'Fraternidad', 'Goaigoaza', 'Juan José Flores', 'Patanemo', 'San Pablo', 'Union'
            ],
            // Municipio Anaco (Anzoátegui)
            'Anaco' => [
                'Anaco', 'San Joaquín'
            ],
            // Municipio Aragua de Barcelona (Anzoátegui)
            'Aragua de Barcelona' => [
                'Aragua de Barcelona'
            ],
            // Municipio Boca de Uchire (Anzoátegui)
            'Boca de Uchire' => [
                'Boca de Uchire', 'Boca del Pao', 'Pariaguán'
            ],
            // Municipio Clarines (Anzoátegui)
            'Bruzual' => [
                'Clarines', 'Guanape', 'Sabana de Uchire'
            ],
            // Municipio Oncito (Anzoátegui)
            'Cajigal' => [
                'Onoto', 'San Pablo', 'San Mateo', 'El Carito', 'El Pilar', 'La Romereña'
            ],
            // Municipio Pueblo Nuevo (Anzoátegui)
            'Carvajal' => [
                'Pueblo Nuevo', 'San Miguel', 'Santa Cruz'
            ],
            // Municipio Lechería (Anzoátegui)
            'Diego Bautista Urbaneja' => [
                'Lechería', 'El Morro', 'Puerto Píritu', 'San Francisco'
            ],
            // Municipio Guanta (Anzoátegui)
            'Guanta' => [
                'Guanta', 'Chorrerón'
            ],
            // Municipio San Mateo (Anzoátegui)
            'Independencia' => [
                'San Mateo', 'El Carito', 'Santa Inés'
            ],
            // Municipio San Pablo (Anzoátegui)
            'Libertad' => [
                'San Pablo', 'San José de Guanipa', 'Pueblo Nuevo'
            ],
            // Municipio El Tigre (Anzoátegui)
            'McGregor' => [
                'El Tigre', 'Santa Inés', 'El Chaparro', 'San Francisco'
            ],
            // Municipio San Diego de Cabrutica (Anzoátegui)
            'Peñalver' => [
                'San Diego de Cabrutica', 'Santa Ana', 'Boca del Pao', 'Pueblo Nuevo'
            ],
            // Municipio Píritu (Anzoátegui)
            'Píritu' => [
                'Píritu', 'San Francisco'
            ],
            // Municipio Bergantín (Anzoátegui)
            'Simón Bolívar' => [
                'Bergantín', 'Caigua', 'El Carmen', 'El Pilar', 'Naricual', 'San Crsitóbal', 'Santa Bárbara'
            ],
            // Municipio San José de Aerocuar (Anzoátegui)
            'Simón Rodríguez' => [
                'San José de Aerocuar', 'Tavera Acosta', 'Aguasay', 'El Morro'
            ],
            // Municipio Soledad (Anzoátegui)
            'Soledad' => [
                'Soledad', 'San Francisco'
            ],
            // Municipio San Francisco (Anzoátegui)
            'Sotillo' => [
                'San Francisco', 'San Lorenzo', 'San Rafael', 'Virgen del Valle'
            ],
            // Municipio San Juan de los Morros (Guárico)
            'Acevedo' => [
                'San Juan de los Morros', 'Cazorla', 'San Rafael de Laya'
            ],
            // Municipio Higuerote (Miranda)
            'Brión' => [
                'Higuerote', 'Curiepe', 'Tacarigua de Brión'
            ],
            // Municipio Los Teques (Miranda)
            'Guaicaipuro' => [
                'Los Teques', 'El Cartanal', 'Santa Rosalía', 'San Pedro', 'Mendoza de Quintero', 'Paracotos'
            ],
            // Municipio San Antonio de los Altos (Miranda)
            'Los Salias' => [
                'San Antonio de los Altos'
            ],
            // Municipio Santa Teresa del Tuy (Miranda)
            'Paz Castillo' => [
                'Santa Teresa del Tuy', 'El Cartanal'
            ],
            // Municipio Guarenas (Miranda)
            'Plaza' => [
                'Guarenas'
            ],
            // Municipio San Francisco de Yare (Miranda)
            'Simón Bolívar' => [
                'San Francisco de Yare', 'San Antonio de Yare'
            ],
            // Municipio Cúa (Miranda)
            'Urdaneta' => [
                'Cúa', 'Nueva Cúa'
            ],
            // Municipio Guatire (Miranda)
            'Zamora' => [
                'Guatire', 'Bolívar'
            ],
            // Municipio Santa Lucía (Miranda)
            'Andrés Bello' => [
                'Santa Lucía'
            ],
            // Municipio Charallave (Miranda)
            'Cristóbal Rojas' => [
                'Charallave', 'Las Brisas'
            ],
            // Municipio Ocumare del Tuy (Miranda)
            'Independencia' => [
                'Ocumare del Tuy', 'Santa Barbara', 'San Antonio de Maturin'
            ],
            // Municipio Santa Teresita (Miranda)
            'Lander' => [
                'Santa Teresita', 'El Guapo', 'Tácata', 'Rio Chico'
            ],
            // Municipio Río Grande (Miranda)
            'Páez' => [
                'Río Grande', 'El Jarillo'
            ],
            // Municipio El Café (Miranda)
            'Rivas' => [
                'El Café', 'Marizapa'
            ],
            // Municipio Turmero (Aragua)
            'Santiago Mariño' => [
                'Turmero', 'Arevalo Aponte', 'Chuao', 'Samán de Güere', 'Alfredo Pacheco Miranda'
            ],
            // Municipio Colonia Tovar (Aragua)
            'Tovar' => [
                'Colonia Tovar'
            ],
            // Municipio Villa de Cura (Aragua)
            'Zamora' => [
                'Villa de Cura', 'Magdaleno', 'San Francisco de Asís', 'Valles de Tucutunemo', 'Augusto Mijares'
            ],
            // Municipio San Mateo (Aragua)
            'Bolívar' => [
                'San Mateo', 'Santa Inés', 'Santa Rita', 'Francisco de Miranda', 'Monseñor Feliciano Gómez', 'José Casanova Godoy', 'Madre María de San José', 'Andrés Linares', 'Los Tacariguas'
            ],
            // Municipio Calabozo (Guárico)
            'Girardot' => [
                'Calabozo', 'Espino', 'San Francisco de Cara', 'San Francisco de Guayabal'
            ],
            // Municipio El Consejo (Aragua)
            'José Rafael Revenga' => [
                'El Consejo'
            ],
            // Municipio Las Delicias (Aragua)
            'Libertador' => [
                'Las Delicias', 'Choroni', 'Cuyagua', 'Magdaleno', 'San Francisco de Asís', 'Valles de Tucutunemo', 'Augusto Mijares'
            ],
            // Municipio Ocumare de la Costa (Aragua)
            'Ocumare de la Costa de Oro' => [
                'Ocumare de la Costa'
            ],
            // Municipio San Casimiro (Aragua)
            'San Casimiro' => [
                'San Casimiro', 'Bella Vista', 'San Francisco de Cara', 'Valle Morín'
            ],
            // Municipio Cagua (Aragua)
            'Sucre' => [
                'Cagua', 'Bella Vista'
            ],
            // Municipio Barbacoas (Aragua)
            'Urdaneta' => [
                'Barbacoas', 'Las Peñitas', 'San Francisco de Cara', 'Taguay'
            ]
        ];

        $createdCount = 0;

        foreach ($municipios as $municipio) {
            if (isset($parishesData[$municipio->nombre])) {
                foreach ($parishesData[$municipio->nombre] as $parroquiaNombre) {
                    $parroquia = Parroquia::updateOrCreate(
                        [
                            'nombre' => $parroquiaNombre,
                            'municipio_id' => $municipio->id
                        ],
                        [
                            'nombre' => $parroquiaNombre,
                            'municipio_id' => $municipio->id,
                            'activo' => true
                        ]
                    );
                    
                    $createdCount++;
                }
            }
        }

        $this->command->info('✅ Parroquias de Venezuela procesadas exitosamente');
        $this->command->info("📊 Total de parroquias procesadas: {$createdCount}");
        
        if ($createdCount === 0) {
            $this->command->warn('⚠️  No se encontraron coincidencias de municipios para asociar parroquias. Asegúrese de que los municipios estén correctamente creados.');
        }
    }
}