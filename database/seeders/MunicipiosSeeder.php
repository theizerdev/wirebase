<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los estados
        $estados = DB::table('estados')->pluck('id', 'iso_3166_2')->toArray();
        
        // Municipios por estado (ejemplo básico, se pueden agregar más)
        $municipios = [
            // Amazonas (VE-X)
            'VE-X' => [
                'Alto Orinoco', 'Atabapo', 'Atures', 'Autana', 'Manapiare', 'Maroa', 'Río Negro'
            ],
            
            // Anzoátegui (VE-B)
            'VE-B' => [
                'Anaco', 'Aragua', 'Bolívar', 'Bruzual', 'Cajigal', 'Carvajal', 'Diego Bautista Urbaneja',
                'Freites', 'Guanipa', 'Guanta', 'Independencia', 'Libertad', 'McGregor', 'Miranda',
                'Monagas', 'Peñalver', 'Píritu', 'San Juan de Capistrano', 'Santa Ana', 'Simón Rodríguez',
                'Sotillo'
            ],
            
            // Apure (VE-C)
            'VE-C' => [
                'Achaguas', 'Biruaca', 'Muñoz', 'Páez', 'Pedro Camejo', 'Rómulo Gallegos', 'San Fernando'
            ],
            
            // Aragua (VE-D)
            'VE-D' => [
                'Bolívar', 'Camatagua', 'Francisco Linares Alcántara', 'Girardot', 'Iragorry', 'Lamas',
                'Libertador', 'Mariño', 'Michelena', 'Ocumare de la Costa de Oro', 'Revenga', 'Ribas',
                'Salom', 'San Casimiro', 'San Sebastián', 'Santiago Mariño', 'Santos Michelena',
                'Sucre', 'Tovar', 'Urdaneta', 'Zamora'
            ],
            
            // Barinas (VE-E)
            'VE-E' => [
                'Alberto Arvelo Torrealba', 'Andrés Eloy Blanco', 'Antonio José de Sucre', 'Arismendi',
                'Barinas', 'Bolívar', 'Cruz Paredes', 'Ezequiel Zamora', 'Obispos', 'Pedraza', 'Rojas', 'Sosa'
            ],
            
            // Bolívar (VE-F)
            'VE-F' => [
                'Angostura', 'Caroní', 'Cedeño', 'Chien', 'El Callao', 'Gran Sabana', 'Padre Pedro Chien',
                'Piar', 'Raúl Leoni', 'Roscio', 'Sifontes', 'Sucre'
            ],
            
            // Carabobo (VE-G)
            'VE-G' => [
                'Bejuma', 'Carlos Arvelo', 'Diego Ibarra', 'Guacara', 'Juan José Mora', 'Libertador',
                'Los Guayos', 'Miranda', 'Montalbán', 'Naguanagua', 'Puerto Cabello', 'San Diego',
                'San Joaquín', 'Valencia'
            ],
            
            // Cojedes (VE-H)
            'VE-H' => [
                'Anzoátegui', 'Bernardino Rivadavia', 'Girardot', 'Lima Blanco', 'Pao de San Juan Bautista',
                'Ricaurte', 'Rómulo Gallegos', 'San Carlos', 'Tinaco', 'Tinaquillo'
            ],
            
            // Delta Amacuro (VE-Y)
            'VE-Y' => [
                'Antonio Díaz', 'Casacoima', 'Pedernales', 'Tucupita'
            ],
            
            // Distrito Capital (VE-A)
            'VE-A' => [
                'Libertador'
            ],
            
            // Falcón (VE-I)
            'VE-I' => [
                'Acosta', 'Bolívar', 'Buchivacoa', 'Cacique Manaure', 'Carirubana', 'Colina', 'Dabajuro',
                'Democracia', 'Falcón', 'Federación', 'Jacura', 'Los Taques', 'Mauroa', 'Miranda',
                'Monseñor Iturriza', 'Palmasola', 'Petit', 'Píritu', 'San Francisco', 'Silva', 'Sucre',
                'Tocópero', 'Unión', 'Urumaco', 'Zamora'
            ],
            
            // Guárico (VE-J)
            'VE-J' => [
                'Camaguán', 'Chaguaramas', 'El Socorro', 'José Félix Ribas', 'José Tadeo Monagas',
                'Juan Germán Roscio', 'Julián Mellado', 'Las Mercedes', 'Leonardo Infante', 'Ortiz',
                'Pedro Zaraza', 'San Gerónimo de Guayabal', 'San José de Guaribe', 'Santa María de Ipire'
            ],
            
            // Lara (VE-K)
            'VE-K' => [
                'Andrés Eloy Blanco', 'Crespo', 'Iribarren', 'Jiménez', 'Morán', 'Palavecino', 'Simón Planas',
                'Torres', 'Urdaneta'
            ],
            
            // Mérida (VE-L)
            'VE-L' => [
                'Alberto Adriani', 'Andrés Bello', 'Antonio Pinto Salinas', 'Aricagua', 'Arzobispo Chacón',
                'Campo Elías', 'Caracciolo Parra Olmedo', 'Cardenal Quintero', 'Guaraque', 'Julio César Salas',
                'Justo Briceño', 'Libertador', 'Miranda', 'Obispo Ramos de Lora', 'Padre Noguera', 'Pueblo Llano',
                'Rangel', 'Rivas Dávila', 'Santos Marquina', 'Sucre', 'Tovar', 'Tulio Febres Cordero',
                'Zea'
            ],
            
            // Miranda (VE-M)
            'VE-M' => [
                'Acevedo', 'Andrés Bello', 'Baruta', 'Brión', 'Bolívar', 'Carrizal', 'Chacao', 'Cristóbal Rojas',
                'El Hatillo', 'Guaicaipuro', 'Independencia', 'Lander', 'Los Salias', 'Páez', 'Paz Castillo',
                'Pedro Gual', 'Plaza', 'Simón Bolívar', 'Sucre', 'Urdaneta', 'Zamora'
            ],
            
            // Monagas (VE-N)
            'VE-N' => [
                'Acosta', 'Aguasay', 'Bolívar', 'Caripe', 'Cedeño', 'Ezequiel Zamora', 'Libertador',
                'Maturín', 'Piar', 'Punceres', 'Santa Bárbara', 'Sotillo', 'Uracoa'
            ],
            
            // Nueva Esparta (VE-O)
            'VE-O' => [
                'Antolín del Campo', 'Arismendi', 'Díaz', 'García', 'Gómez', 'Maneiro', 'Marcano',
                'Mariño', 'Península de Macanao', 'Tubores', 'Villalba'
            ],
            
            // Portuguesa (VE-P)
            'VE-P' => [
                'Agua Blanca', 'Araure', 'Esteller', 'Guanare', 'Guanarito', 'Monseñor José Vicente de Unda',
                'Ospino', 'Páez', 'Papelón', 'San Genaro de Boconoíto', 'San Rafael de Onoto', 'Santa Rosalía',
                'Sucre', 'Turén'
            ],
            
            // Sucre (VE-R)
            'VE-R' => [
                'Andrés Eloy Blanco', 'Andrés Mata', 'Arismendi', 'Benítez', 'Bermúdez', 'Bolívar',
                'Cajigal', 'Cruz Salmerón Acosta', 'Libertador', 'Mariño', 'Mejía', 'Montes', 'Ribero',
                'Sucre', 'Valdez'
            ],
            
            // Táchira (VE-S)
            'VE-S' => [
                'Andrés Bello', 'Antonio Rómulo Costa', 'Ayacucho', 'Bolívar', 'Cárdenas', 'Córdoba',
                'Fernández Feo', 'Francisco de Miranda', 'García de Hevia', 'Guásimos', 'Independencia',
                'Jáuregui', 'José María Vargas', 'Junín', 'Libertad', 'Libertador', 'Lobatera', 'Michelena',
                'Panamericano', 'Pedro María Ureña', 'Rafael Urdaneta', 'Samuel Darío Maldonado',
                'San Cristóbal', 'San Judas Tadeo', 'Seboruco', 'Simón Rodríguez', 'Sucre', 'Torbes', 'Uribante'
            ],
            
            // Trujillo (VE-T)
            'VE-T' => [
                'Andrés Bello', 'Boconó', 'Bolívar', 'Candelaria', 'Carache', 'Escuque', 'José Felipe Márquez Cañizalez',
                'Juan Vicente Campos Elías', 'La Ceiba', 'Miranda', 'Monte Carmelo', 'Motatán', 'Pampán',
                'Pampanito', 'Rafael Rangel', 'San Rafael de Carvajal', 'Sucre', 'Trujillo', 'Urdaneta',
                'Valera'
            ],
            
            // Vargas (VE-X)
            'VE-X' => [
                'Vargas'
            ],
            
            // Yaracuy (VE-U)
            'VE-U' => [
                'Arístides Bastidas', 'Bolívar', 'Bruzual', 'Cocorote', 'Independencia', 'José Antonio Páez',
                'La Trinidad', 'Manuel Monge', 'Nirgua', 'Peña', 'San Felipe', 'Sucre', 'Urachiche', 'Veroes'
            ],
            
            // Zulia (VE-V)
            'VE-V' => [
                'Almirante Padilla', 'Baralt', 'Cabimas', 'Catatumbo', 'Colón', 'Francisco Javier Pulgar',
                'Guajira', 'Jesús Enrique Losada', 'Jesús María Semprún', 'La Cañada de Urdaneta',
                'Lagunillas', 'Machiques de Perijá', 'Mara', 'Maracaibo', 'Miranda', 'Rosario de Perijá',
                'San Francisco', 'Santa Rita', 'Simón Bolívar', 'Sucre', 'Valmore Rodríguez'
            ]
        ];
        
        foreach ($municipios as $iso_estado => $lista_municipios) {
            if (isset($estados[$iso_estado])) {
                $estado_id = $estados[$iso_estado];
                
                foreach ($lista_municipios as $index => $nombre_municipio) {
                    // Coordenadas de ejemplo para algunos municipios principales
                    $coordenadas = $this->getCoordenadasMunicipio($nombre_municipio, $iso_estado);
                    
                    DB::table('municipios')->updateOrInsert(
                        ['nombre' => $nombre_municipio, 'estado_id' => $estado_id],
                        [
                            'nombre' => $nombre_municipio, 
                            'estado_id' => $estado_id,
                            'latitud' => $coordenadas['latitud'],
                            'longitud' => $coordenadas['longitud']
                        ]
                    );
                }
            }
        }
    }

    /**
     * Obtener coordenadas de municipios principales
     */
    private function getCoordenadasMunicipio($nombre, $estado_iso)
    {
        // Coordenadas de algunos municipios principales de Venezuela
        $coordenadas = [
            'VE-A' => [ // Distrito Capital
                'Libertador' => ['latitud' => 10.5000, 'longitud' => -66.9167]
            ],
            'VE-B' => [ // Anzoátegui
                'Anaco' => ['latitud' => 9.4333, 'longitud' => -64.4667],
                'Barcelona' => ['latitud' => 10.1333, 'longitud' => -64.6833],
                'Puerto La Cruz' => ['latitud' => 10.2167, 'longitud' => -64.6167]
            ],
            'VE-C' => [ // Apure
                'San Fernando' => ['latitud' => 7.8833, 'longitud' => -67.4667]
            ],
            'VE-D' => [ // Aragua
                'Maracay' => ['latitud' => 10.2446, 'longitud' => -67.5939]
            ],
            'VE-E' => [ // Barinas
                'Barinas' => ['latitud' => 8.6333, 'longitud' => -70.2000]
            ],
            'VE-F' => [ // Bolívar
                'Ciudad Bolívar' => ['latitud' => 8.1333, 'longitud' => -63.5500],
                'Ciudad Guayana' => ['latitud' => 8.3000, 'longitud' => -62.7500]
            ],
            'VE-G' => [ // Carabobo
                'Valencia' => ['latitud' => 10.1567, 'longitud' => -68.0475],
                'Puerto Cabello' => ['latitud' => 10.4667, 'longitud' => -68.0167]
            ],
            'VE-H' => [ // Cojedes
                'San Carlos' => ['latitud' => 9.3833, 'longitud' => -68.3333]
            ],
            'VE-I' => [ // Falcón
                'Coro' => ['latitud' => 11.4000, 'longitud' => -69.6667],
                'Punto Fijo' => ['latitud' => 11.7000, 'longitud' => -70.2000]
            ],
            'VE-J' => [ // Guárico
                'San Juan de los Morros' => ['latitud' => 9.9167, 'longitud' => -67.3500]
            ],
            'VE-K' => [ // Lara
                'Barquisimeto' => ['latitud' => 10.0636, 'longitud' => -69.3347]
            ],
            'VE-L' => [ // Mérida
                'Mérida' => ['latitud' => 8.6000, 'longitud' => -71.1500]
            ],
            'VE-M' => [ // Miranda
                'Los Teques' => ['latitud' => 10.2500, 'longitud' => -67.0333],
                'Guarenas' => ['latitud' => 10.4667, 'longitud' => -66.6167]
            ],
            'VE-N' => [ // Monagas
                'Maturín' => ['latitud' => 9.7500, 'longitud' => -63.1667]
            ],
            'VE-O' => [ // Nueva Esparta
                'La Asunción' => ['latitud' => 11.0333, 'longitud' => -63.8333],
                'Porlamar' => ['latitud' => 10.9500, 'longitud' => -63.8500]
            ],
            'VE-P' => [ // Portuguesa
                'Guanare' => ['latitud' => 9.0333, 'longitud' => -69.7333]
            ],
            'VE-R' => [ // Sucre
                'Cumaná' => ['latitud' => 10.4500, 'longitud' => -64.1667],
                'Carúpano' => ['latitud' => 10.6667, 'longitud' => -63.2500]
            ],
            'VE-S' => [ // Táchira
                'San Cristóbal' => ['latitud' => 7.7667, 'longitud' => -72.2333]
            ],
            'VE-T' => [ // Trujillo
                'Trujillo' => ['latitud' => 9.3667, 'longitud' => -70.4333],
                'Valera' => ['latitud' => 9.3167, 'longitud' => -70.6167]
            ],
            'VE-U' => [ // Yaracuy
                'San Felipe' => ['latitud' => 10.3333, 'longitud' => -68.7500]
            ],
            'VE-V' => [ // Zulia
                'Maracaibo' => ['latitud' => 10.6500, 'longitud' => -71.6500],
                'Cabimas' => ['latitud' => 10.3833, 'longitud' => -71.4333]
            ],
            'VE-X' => [ // Vargas
                'La Guaira' => ['latitud' => 10.6000, 'longitud' => -66.9333]
            ],
            'VE-Y' => [ // Delta Amacuro
                'Tucupita' => ['latitud' => 9.0667, 'longitud' => -62.0500]
            ]
        ];

        // Verificar si tenemos coordenadas para este municipio
        if (isset($coordenadas[$estado_iso]) && isset($coordenadas[$estado_iso][$nombre])) {
            return $coordenadas[$estado_iso][$nombre];
        }

        // Si no tenemos coordenadas específicas, usar coordenadas del estado como base
        // y agregar un pequeño desplazamiento aleatorio para diferenciar
        $estado_coords = DB::table('estados')
            ->where('iso_3166_2', $estado_iso)
            ->first(['latitud', 'longitud']);

        if ($estado_coords) {
            // Agregar un pequeño desplazamiento aleatorio
            $desplazamiento_lat = (mt_rand(-100, 100) / 1000); // -0.1 a 0.1 grados
            $desplazamiento_lng = (mt_rand(-100, 100) / 1000);
            
            return [
                'latitud' => $estado_coords->latitud + $desplazamiento_lat,
                'longitud' => $estado_coords->longitud + $desplazamiento_lng
            ];
        }

        // Coordenadas por defecto (centro aproximado de Venezuela)
        return ['latitud' => 8.0, 'longitud' => -66.0];
    }
}