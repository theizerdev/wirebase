<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Database\Seeder;

class VenezuelaMunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds for Venezuelan municipalities.
     */
    public function run(): void
    {
        // Estados de Venezuela
        $estados = Estado::whereHas('pais', function($query) {
            $query->where('codigo_iso2', 'VE');
        })->get();

        if ($estados->count() === 0) {
            $this->command->error('❌ No se encontraron estados de Venezuela. Ejecute primero VenezuelaStatesSeeder.');
            return;
        }

        $municipalitiesData = [
            'Amazonas' => [
                'Alto Orinoco', 'Atabapo', 'Atures', 'Autana', 'Manapiare', 'Maroa', 'Río Negro'
            ],
            'Anzoátegui' => [
                'Anaco', 'Aragua de Barcelona', 'Bolívar', 'Bruzual', 'Cajigal', 'Carvajal', 'Diego Bautista Urbaneja', 
                'Freites', 'Guanipa', 'Guanta', 'Independencia', 'Libertad', 'McGregor', 'Miranda', 'Monagas', 
                'Peñalver', 'Píritu', 'San José de Guanipa', 'San Juan de Capistrano', 'Santa Ana', 'Simón Bolívar', 
                'Simón Rodríguez', 'Soledad', 'Valle de Guanape', 'Santa Rita'
            ],
            'Apure' => [
                'Achaguas', 'Biruaca', 'Muñoz', 'Páez', 'Pedro Camejo', 'Rómulo Gallegos', 'San Fernando'
            ],
            'Aragua' => [
                'Bolívar', 'Camatagua', 'Francisco Linares Alcántara', 'Girardot', 'José Ángel Lamas', 
                'José Félix Ribas', 'José Rafael Revenga', 'Libertador', 'Mario Briceño Iragorry', 'Ocumare de la Costa de Oro', 
                'San Casimiro', 'San Sebastián', 'Santiago Mariño', 'Santos Michelena', 'Sucre', 'Tovar', 'Urdaneta', 'Zamora'
            ],
            'Barinas' => [
                'Alberto Arvelo Torrealba', 'Andrés Eloy Blanco', 'Antonio José de Sucre', 'Arismendi', 'Barinas', 
                'Bolívar', 'Cruz Paredes', 'Ezequiel Zamora', 'Obispos', 'Pedraza', 'Rojas', 'Sosa'
            ],
            'Bolívar' => [
                'Caroní', 'Cedeño', 'El Callao', 'Gran Sabana', 'Heres', 'Piar', 'Raúl Leoni', 'Roscio', 'Sifontes', 'Sucre'
            ],
            'Carabobo' => [
                'Bejuma', 'Carlos Arvelo', 'Diego Ibarra', 'Guacara', 'Juan José Mora', 'Las Piraguas', 
                'Los Guayos', 'Miranda', 'Montalbán', 'Naguanagua', 'Puerto Cabello', 'San Diego', 
                'San Joaquín', 'Valencia'
            ],
            'Cojedes' => [
                'Anzoátegui', 'Falcón', 'Girardot', 'Lima Blanco', 'Páez', 'Rómulo Gallegos', 'San Carlos', 'Tinaquillo'
            ],
            'Delta Amacuro' => [
                'Antonio Díaz', 'Casacoima', 'Pedernales', 'Tucupita'
            ],
            'Distrito Capital' => [
                'Libertador'
            ],
            'Falcón' => [
                'Acosta', 'Bolívar', 'Buchivacoa', 'Cacique Manaure', 'Carirubana', 'Colina', 'Dabajuro', 
                'Democracia', 'Falcón', 'Federación', 'Jacura', 'José Laurencio Silva', 'Los Taques', 
                'Mauroa', 'Miranda', 'Petit', 'Píritu', 'San Francisco', 'Silva', 'Sucre', 'Tocopero', 
                'Unión', 'Urumaco', 'Zamora'
            ],
            'Guárico' => [
                'Camaguan', 'Chaguaramas', 'El Socorro', 'Infante', 'Las Mercedes', 'Mellado', 'Miranda', 
                'Monagas', 'Ortiz', 'Ribas', 'Roscio', 'San Gerónimo de Guayabal', 'San José de Guaribe', 'Santa María de Ipire', 'Sebastián Francisco de Miranda'
            ],
            'Lara' => [
                'Andrés Eloy Blanco', 'Crespo', 'Iribarren', 'Jiménez', 'Morán', 'Palavecino', 'Sanare', 'Simón Planas', 'Torres', 'Urdaneta'
            ],
            'Mérida' => [
                'Alberto Adriani', 'Andrés Bello', 'Antonio Pinto Salinas', 'Aricagua', 'Arzobispo Chacón', 
                'Campo Elías', 'Caracciolo Parra Olmedo', 'Cardenal Quintero', 'Guaraque', 'Julio César Salas', 
                'Justo Briceño', 'Kotepa', 'Mantecal', 'Mariano Picón Salas', 'Matanzas', 'Mijares', 
                'Montaña', 'Pueblo Llano', 'Rangel', 'Rivas Dávila', 'Sabaneta', 'Salas', 'San Cristóbal de Torondoy', 'Santo Domingo', 'Sucre', 'Tovar', 'Tulio Febres Mendoza', 'Zea'
            ],
            'Miranda' => [
                'Acevedo', 'Andrés Bello', 'Baruta', 'Brión', 'Buroz', 'Carrizal', 'Chacao', 'Cristóbal Rojas', 
                'El Hatillo', 'Guaicaipuro', 'Independencia', 'Lander', 'Los Salias', 'Páez', 'Paz Castillo', 
                'Plaza', 'Rivas', 'Sánchez', 'Sucre', 'Tovar', 'Urdaneta', 'Zamora'
            ],
            'Monagas' => [
                'Acosta', 'Aguasay', 'Bolívar', 'Caripe', 'Cedeño', 'Ezequiel Zamora', 'Libertador', 
                'Maturín', 'Piar', 'Punceres', 'Santa Bárbara', 'Sotillo', 'Uracoa'
            ],
            'Nueva Esparta' => [
                'Antolín del Campo', 'Arismendi', 'Díaz', 'García', 'Gómez', 'Maneiro', 'Marcano', 'Mariño', 
                'Península de Macanao', 'Tubores', 'Villalba'
            ],
            'Portuguesa' => [
                'Agua Blanca', 'Araure', 'Esteller', 'Guanare', 'Guanarito', 'José Vicente Campos Elías', 
                'Ospino', 'Páez', 'Papelón', 'San Genaro de Boconoíto', 'San Rafael de Onoto', 'Santa Rosalía', 
                'Sucre', 'Turén'
            ],
            'Sucre' => [
                'Andrés Eloy Blanco', 'Andrés Mata', 'Arismendi', 'Benítez', 'Bermúdez', 'Bolívar', 'Cajigal', 
                'Cruz Salmerón Acosta', 'El Hatillo', 'Florida', 'Guanoco', 'Guanta', 'Independencia', 'Libertador', 
                'Mariño', 'Mejía', 'Montes', 'Muey', 'Píritu', 'San Andrés', 'San Antonio', 'San Diego de Cabrutica', 
                'Santa Catalina', 'Santa Teresa', 'Sucre', 'Valdez'
            ],
            'Táchira' => [
                'Andrés Bello', 'Antonio Rómulo Costa', 'Ayacucho', 'Bolívar', 'Cárdenas', 'Córdoba', 
                'Fernández Feo', 'Francisco de Miranda', 'García de Hevia', 'Guásimos', 'Independencia', 
                'Jaureguí', 'José María Vargas', 'Junín', 'Libertad', 'Lobatera', 'Michelena', 'Panamericano', 
                'Pedro María Ureña', 'Rafael Urdaneta', 'Samuel Darío Maldonado', 'San Cristóbal', 
                'Seboruco', 'Simón Rodríguez', 'Sucre', 'Torbes', 'Uribante'
            ],
            'Trujillo' => [
                'Andrés Bello', 'Boconó', 'Bolívar', 'Candelaria', 'Carache', 'Escuque', 'José Felipe Márquez Cañizalez', 
                'Juan Vicente Campos Elías', 'La Ceiba', 'Miranda', 'Monte Carmelo', 'Motatán', 'Pampán', 
                'Pampanito', 'Rafael Rangel', 'San Rafael de Carvajal', 'Sucre', 'Trujillo', 'Urdaneta', 'Valera'
            ],
            'Vargas' => [
                'Vargas'
            ],
            'Yaracuy' => [
                'Arístides Bastidas', 'Bolívar', 'Bruzual', 'Cocorote', 'Independencia', 'José Antonio Páez', 
                'La Trinidad', 'Manuel Monge', 'Nirgua', 'Peña', 'San Felipe', 'Sucre', 'Urachiche', 'José Joaquín Veroes'
            ],
            'Zulia' => [
                'Almirante Padilla', 'Baralt', 'Cabimas', 'Catatumbo', 'Colón', 'Francisco Javier Pulgar', 
                'Páez', 'Jesús Enrique Losada', 'Jesús María Semprún', 'La Cañada de Urdaneta', 'Lagunillas', 
                'Machiques de Perijá', 'Mara', 'Maracaibo', 'Miranda', 'Rosario de Perijá', 'San Francisco', 
                'Santa Rita', 'Simón Bolívar', 'Sucre', 'Valmore Rodríguez'
            ]
        ];

        $createdCount = 0;

        foreach ($estados as $estado) {
            if (isset($municipalitiesData[$estado->nombre])) {
                foreach ($municipalitiesData[$estado->nombre] as $municipioNombre) {
                    $municipio = Municipio::updateOrCreate(
                        [
                            'nombre' => $municipioNombre,
                            'estado_id' => $estado->id
                        ],
                        [
                            'nombre' => $municipioNombre,
                            'estado_id' => $estado->id,
                            'activo' => true
                        ]
                    );
                    
                    $createdCount++;
                }
            }
        }

        $this->command->info('✅ Municipios de Venezuela procesados exitosamente');
        $this->command->info("📊 Total de municipios procesados: {$createdCount}");
    }
}