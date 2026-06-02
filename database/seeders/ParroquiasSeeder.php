<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParroquiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los municipios con sus IDs y nombres
        $municipios = DB::table('municipios')->get();
        $estados = DB::table('estados')->pluck('id', 'iso_3166_2')->toArray();
        
        // Organizar municipios por estado y nombre para facilitar la búsqueda
        $municipios_por_estado = [];
        foreach ($municipios as $municipio) {
            // Obtener el estado al que pertenece el municipio
            $estado = DB::table('estados')->where('id', $municipio->estado_id)->first();
            if ($estado) {
                $municipios_por_estado[$estado->iso_3166_2][$municipio->nombre] = $municipio->id;
            }
        }
        
        // Parroquias por estado y municipio
        $parroquias = [
            // Distrito Capital (VE-A)
            'VE-A' => [
                'Libertador' => [
                    'Altagracia', 'Antímano', 'Caricuao', 'Catedral', 'Coche', 'El Junquito', 
                    'El Paraíso', 'El Recreo', 'El Valle', 'La Candelaria', 'La Pastora', 
                    'Las Mercedes', 'Propatria', 'San Agustín', 'San Bernardino', 'San José', 
                    'San Juan', 'Santa Rosalía', 'Sucre', 'Villa Brasil'
                ]
            ],
            
            // Amazonas (VE-X)
            'VE-X' => [
                'Alto Orinoco' => ['La Esmeralda', 'Huachamacare', 'Marawaka', 'Mavaca', 'Sierra Parima'],
                'Atabapo' => ['Caname', 'Fernando Girón Tovar', 'Luis Alberto Gómez', 'Parhueña', 'Placeres del Seño'],
                'Atures' => ['Samariapo', 'Sipapo', 'Munduapo', 'Alto Ventuari', 'Bajo Ventuari'],
                'Autana' => ['Isla Ratón', 'San Juan de Manapiare', 'San Pedro de Manapiare', 'Sabanita de Manapiare', 'Cacique Manapiare'],
                'Manapiare' => ['San Francisco de Asís', 'San Rafael', 'San Juan Bautista', 'Zacarías Zamora', 'Marueta'],
                'Maroa' => ['Victorino', 'Bolívar', 'Guayapo', 'Zurima', 'Sarría'],
                'Río Negro' => ['San Carlos de Río Negro', 'Casiquiare', 'Cucuy', 'San Juan de Río Negro', 'Solano']
            ],
            
            // Anzoátegui (VE-B)
            'VE-B' => [
                'Anaco' => ['Anaco', 'San Joaquín'],
                'Aragua' => ['Cachipo', 'Aragua de Barcelona'],
                'Bolívar' => ['Bergantín', 'Caigua', 'El Carmen', 'El Pilar', 'Naricual', 'San Cristóbal'],
                'Bruzual' => ['Clarines', 'Guanape', 'Sabana de Uchire'],
                'Cajigal' => ['Onoto', 'San Pablo'],
                'Carvajal' => ['San Diego de Cabrutica', 'Santa Rosa', 'Urica'],
                'Diego Bautista Urbaneja' => ['Lechería', 'El Morro'],
                'Freites' => ['Cantaura', 'Libertador', 'Santa Rosa', 'Urica'],
                'Guanipa' => ['San José de Guanipa', 'Pariaguán'],
                'Guanta' => ['Chorrerón', 'Guanta'],
                'Independencia' => ['Mamo', 'Soledad'],
                'Libertad' => ['San Mateo', 'Pozuelos', 'Pirítu'],
                'McGregor' => ['Edmundo Barrios', 'Miguel Otero Silva'],
                'Miranda' => ['Punta de Mata', 'La Venta'],
                'Monagas' => ['Alto de los Godos', 'Boca de Uchire', 'El Corozo', 'Los Barales', 'Santa Cruz'],
                'Peñalver' => ['Juan Griego', 'Clarines'],
                'Píritu' => ['Píritu', 'San Miguel'],
                'San Juan de Capistrano' => ['Boca de Chávez', 'San Juan de Capistrano'],
                'Santa Ana' => ['Santa Ana', 'Pueblo Nuevo'],
                'Simón Rodríguez' => ['El Tigre', 'Poza Verde'],
                'Sotillo' => ['Puerto La Cruz', 'Pozuelos']
            ],
            
            // Apure (VE-C)
            'VE-C' => [
                'Achaguas' => ['Achaguas', 'Apurito', 'El Yagual', 'Guachara', 'Mucuritas', 'Queseras del Medio'],
                'Biruaca' => ['Biruaca'],
                'Muñoz' => ['Bruzual', 'Mantecal', 'Quintero', 'Rincón Hondo', 'San Vicente'],
                'Páez' => ['Aramendi', 'El Amparo', 'San Fernando', 'San Rafael de Atamaica'],
                'Pedro Camejo' => ['Codazzi', 'Cunaviche'],
                'Rómulo Gallegos' => ['Elorza', 'La Trinidad'],
                'San Fernando' => ['San Fernando', 'El Recreo', 'Peñalver', 'San Rafael de Orituco']
            ],
            
            // Aragua (VE-D)
            'VE-D' => [
                'Bolívar' => ['Bolívar', 'Camatagua'],
                'Camatagua' => ['Camatagua', 'Carmen de Cura'],
                'Francisco Linares Alcántara' => ['Santa Rita', 'Francisco de Miranda', 'Mondragón'],
                'Girardot' => ['Andrés Eloy Blanco', 'Choroní', 'José Casanova Godoy', 'Juan Vicente Bolívar y Ponte', 'Los Tacariguas', 'Castro Silva', 'Las Delicias', 'Palo Negro', 'San Martín de Porres'],
                'Iragorry' => ['El Limón', 'Caña de Azúcar'],
                'Lamas' => ['Bolívar', 'Camatagua'],
                'Libertador' => ['Santa Cruz', 'Mariño'],
                'Mariño' => ['Arévalo Aponte', 'Chuao', 'Samán de Güere', 'Turmero'],
                'Michelena' => ['Santos Michelena', 'Tiara'],
                'Ocumare de la Costa de Oro' => ['Ocumare de la Costa', 'La Cruz'],
                'Revenga' => ['Augusto Mijares', 'Revenga'],
                'Ribas' => ['El Consejo', 'Pao de Zúñiga'],
                'Salom' => ['Zuata', 'Pao de Carabobo'],
                'San Casimiro' => ['San Casimiro', 'Güiripa', 'Ollas de Caramacate', 'Valle Morín'],
                'San Sebastián' => ['San Sebastián'],
                'Santiago Mariño' => ['Turmero', 'Arevalo Aponte', 'Chuao', 'Samán de Güere'],
                'Santos Michelena' => ['Santos Michelena', 'Tiara'],
                'Sucre' => ['Cagua', 'Bella Vista'],
                'Tovar' => ['Tovar'],
                'Urdaneta' => ['Urdaneta', 'Las Peñitas', 'San Francisco de Cara', 'Taguay'],
                'Zamora' => ['Villa de Cura', 'Magdaleno', 'San Francisco de Asís', 'Valles de Tucutunemo']
            ],
            
            // Barinas (VE-E)
            'VE-E' => [
                'Alberto Arvelo Torrealba' => ['Sabaneta', 'Juan Antonio Rodríguez Domínguez'],
                'Andrés Eloy Blanco' => ['El Cantón', 'Santa Cruz de Guacas', 'Puerto Vivas'],
                'Antonio José de Sucre' => ['Ticoporo', 'Nicolás Pulido', 'Andrés Bello'],
                'Arismendi' => ['Arismendi', 'Guadarrama', 'La Unión', 'San Antonio'],
                'Barinas' => ['Barinas', 'Alberto Arvelo Larriva', 'San Silvestre', 'Santa Inés', 'Santa Lucía', 'Torunos', 'El Carmen', 'Rómulo Betancourt', 'Corazón de Jesús', 'Ramón Ignacio Méndez', 'Alto Barinas', 'Manuel Palacio Fajardo', 'Juan Antonio Rodríguez Domínguez', 'Dominga Ortiz de Páez'],
                'Bolívar' => ['Barinitas', 'Altamira', 'Calderas'],
                'Cruz Paredes' => ['Barrancas', 'El Socorro', 'Mazparrito'],
                'Ezequiel Zamora' => ['Santa Bárbara', 'Pedro Briceño Méndez', 'Ramón Ignacio Méndez', 'José Ignacio del Pumar'],
                'Obispos' => ['Obispos', 'Guasimitos', 'El Real', 'La Luz'],
                'Pedraza' => ['Ciudad Bolivia', 'Ignacio Briceño', 'José Félix Ribas', 'Páez'],
                'Rojas' => ['Libertad', 'Dolores', 'Santa Rosa', 'Palacio Fajardo', 'Simón Rodríguez'],
                'Sosa' => ['Ciudad de Nutrias', 'El Regalo', 'Puerto de Nutrias', 'Santa Catalina']
            ],
            
            // Bolívar (VE-F)
            'VE-F' => [
                'Angostura' => ['Raúl Leoni', 'Barceloneta', 'Santa Bárbara'],
                'Caroní' => ['Cachamay', 'Chirica', 'Dalla Costa', 'Once de Abril', 'Simón Bolívar', 'Unare', 'Universidad', 'Vista al Sol', 'Pozo Verde', 'Yocoima'],
                'Cedeño' => ['Cedeño', 'Altagracia', 'Ascensión Farreras', 'Guaniamo', 'La Urbana', 'Pijiguaos'],
                'Chien' => ['Padre Pedro Chien', 'Río Grande'],
                'El Callao' => ['El Callao'],
                'Gran Sabana' => ['Gran Sabana', 'Ikabarú'],
                'Padre Pedro Chien' => ['Padre Pedro Chien', 'Río Grande'],
                'Piar' => ['Andrés Eloy Blanco', 'Pedro Cova'],
                'Raúl Leoni' => ['Raúl Leoni', 'Barceloneta'],
                'Roscio' => ['Roscio', 'Salóm'],
                'Sifontes' => ['Sifontes', 'Dalla Costa', 'San Isidro'],
                'Sucre' => ['Sucre', 'Aripao', 'Guarataro', 'Las Majadas', 'Moitaco']
            ],
            
            // Carabobo (VE-G)
            'VE-G' => [
                'Bejuma' => ['Bejuma', 'Canoabo', 'Simón Bolívar'],
                'Carlos Arvelo' => ['Carlos Arvelo', 'Güigüe', 'Tacarigua'],
                'Diego Ibarra' => ['Diego Ibarra', 'Independencia', 'Santa Rosa'],
                'Guacara' => ['Guacara', 'Ciudad Alianza', 'Yagua'],
                'Juan José Mora' => ['Juan José Mora', 'Morón', 'Urama'],
                'Libertador' => ['Libertador', 'Tocuyito', 'Valencia'],
                'Los Guayos' => ['Los Guayos'],
                'Miranda' => ['Miranda', 'Montalbán'],
                'Montalbán' => ['Montalbán'],
                'Naguanagua' => ['Naguanagua'],
                'Puerto Cabello' => ['Puerto Cabello', 'Pueblo Nuevo', 'Santa Ana'],
                'San Diego' => ['San Diego'],
                'San Joaquín' => ['San Joaquín'],
                'Valencia' => ['Valencia', 'Candelaria', 'Catedral', 'El Socorro', 'Miguel Peña', 'Rafael Urdaneta', 'San Blas', 'San José', 'Santa Rosa']
            ],
            
            // Cojedes (VE-H)
            'VE-H' => [
                'Anzoátegui' => ['Anzoátegui', 'Cojedes', 'Juan de Mata Suárez'],
                'Bernardino Rivadavia' => ['Bernardino Rivadavia', 'Bartolomé Salóm', 'Sabaneta'],
                'Girardot' => ['Girardot', 'Camuriquito'],
                'Lima Blanco' => ['Lima Blanco', 'Cabure'],
                'Pao de San Juan Bautista' => ['Pao de San Juan Bautista', 'El Pao'],
                'Ricaurte' => ['Ricaurte', 'El Amparo'],
                'Rómulo Gallegos' => ['Rómulo Gallegos', 'San Carlos de Austria', 'Juan Ángel Bravo', 'Manuel Manrique'],
                'San Carlos' => ['San Carlos', 'General en Jefe José Laurencio Silva'],
                'Tinaco' => ['Tinaco', 'Tinaquillo'],
                'Tinaquillo' => ['Tinaquillo']
            ],
            
            // Delta Amacuro (VE-Y)
            'VE-Y' => [
                'Antonio Díaz' => ['Curiapo', 'Almirante Luis Brión', 'Francisco Aniceto Lugo', 'Manuel Renaud'],
                'Casacoima' => ['Casacoima', 'Imataca', 'Juan Bautista Arismendi', 'Romulo Gallegos'],
                'Pedernales' => ['Pedernales', 'Luis Beltrán Prieto Figueroa'],
                'Tucupita' => ['Tucupita', 'San José', 'José Vidal Marcano', 'Juan Millán']
            ],
            
            // Falcón (VE-I)
            'VE-I' => [
                'Acosta' => ['Capadare', 'La Pastora', 'Libertador', 'San Juan de los Cayos'],
                'Bolívar' => ['Aracua', 'La Peña', 'San Luis'],
                'Buchivacoa' => ['Bariro', 'Buchivacoa', 'Capatárida'],
                'Cacique Manaure' => ['Carirubana', 'Norte', 'Punta Cardón', 'Santa Ana'],
                'Carirubana' => ['Carirubana', 'Norte', 'Punta Cardón', 'Santa Ana'],
                'Colina' => ['La Vela de Coro', 'Acurigua', 'Guaibacoa', 'Las Calderas', 'Macoruca'],
                'Dabajuro' => ['Dabajuro'],
                'Democracia' => ['Pedregal', 'Agua Clara', 'Avaria', 'Piedra Grande', 'Purureche'],
                'Falcón' => ['Pueblo Nuevo', 'Adícora', 'Baraived', 'Buena Vista', 'Jadacaquiva', 'El Vínculo', 'El Hato', 'Moruy', 'Pueblo Nuevo', 'Santa Ana', 'San Pedro del Río'],
                'Federación' => ['Churuguara', 'Agua Larga', 'El Paují', 'Independencia', 'Mapararí'],
                'Jacura' => ['Jacura', 'Agua Linda', 'Araurima'],
                'Los Taques' => ['Los Taques', 'Judibana'],
                'Mauroa' => ['Mene de Mauroa', 'Casigua', 'San Félix'],
                'Miranda' => ['Guzmán Guillermo', 'Mitare', 'Río Seco', 'Sabaneta', 'Santa Ana'],
                'Monseñor Iturriza' => ['Boquerón', 'El Charal', 'Las Vegas del Tuy'],
                'Palmasola' => ['Palmasola'],
                'Petit' => ['Cabure', 'Colina', 'Curimagua'],
                'Píritu' => ['Píritu', 'San José de la Costa'],
                'San Francisco' => ['Mirimire', 'Tucacas', 'Boca de Aroa'],
                'Silva' => ['Tocópero'],
                'Sucre' => ['Sucre', 'Pecaya'],
                'Tocópero' => ['Tocópero'],
                'Unión' => ['El Tarairal', 'Santa Cruz de Bucaral'],
                'Urumaco' => ['Urumaco', 'Bruzual'],
                'Zamora' => ['Zamora', 'La Concepción', 'San Pablo', 'Olívula']
            ],
            
            // Guárico (VE-J)
            'VE-J' => [
                'Camaguán' => ['Camaguán', 'Puerto Miranda', 'Uverito'],
                'Chaguaramas' => ['Chaguaramas'],
                'El Socorro' => ['El Socorro'],
                'José Félix Ribas' => ['José Félix Ribas', 'Fréitez', 'Santa Rita de Manapire'],
                'José Tadeo Monagas' => ['José Tadeo Monagas', 'Cazorla', 'Santa Cruz de Guacas'],
                'Juan Germán Roscio' => ['Juan Germán Roscio', 'El Sombrero'],
                'Julián Mellado' => ['Julián Mellado', 'Espino'],
                'Las Mercedes' => ['Las Mercedes', 'Cabruta', 'Santa Rita'],
                'Leonardo Infante' => ['Leonardo Infante', 'Valle de La Pascua', 'Espino'],
                'Ortiz' => ['Ortiz', 'San Francisco de Tiznado', 'San José de Tiznado', 'Santa María de Ipire'],
                'Pedro Zaraza' => ['Pedro Zaraza', 'San José de Unare'],
                'San Gerónimo de Guayabal' => ['San Gerónimo de Guayabal', 'Cantaclaro'],
                'San José de Guaribe' => ['San José de Guaribe', 'Uveral'],
                'Santa María de Ipire' => ['Santa María de Ipire', 'Altamira']
            ],
            
            // Lara (VE-K)
            'VE-K' => [
                'Andrés Eloy Blanco' => ['Quebrada Honda de Guache', 'Pío Tamayo', 'Yacambú'],
                'Crespo' => ['Freitez', 'José María Blanco'],
                'Iribarren' => ['Iribarren', 'Aguedo Felipe Alvarado', 'Buena Vista', 'Catedral', 'Concepción', 'El Cují', 'Juárez', 'Santa Rosa', 'Tamaca', 'Unión'],
                'Jiménez' => ['Jiménez', 'Buría', 'Santana'],
                'Morán' => ['Morán', 'El Tocuyo', 'Cabudare', 'José Gregorio Bastidas'],
                'Palavecino' => ['Palavecino', 'La Trinidad'],
                'Simón Planas' => ['Simón Planas', 'Gustavo Vegas León', 'Torres'],
                'Torres' => ['Torres', 'Altagracia', 'Antonio Díaz', 'Camacaro', 'Castañeda', 'Cecilio Acosta', 'Chiquinquira', 'El Blanco', 'Espinoza de los Monteros', 'Heriberto Arroyo', 'Lara', 'Las Mercedes', 'Manuel Morillo', 'Montaña Verde', 'Montes de Oca', 'Reyes Vargas', 'Torres'],
                'Urdaneta' => ['Urdaneta', 'Siquisique', 'San Miguel', 'Xaguas']
            ],
            
            // Mérida (VE-L)
            'VE-L' => [
                'Alberto Adriani' => ['Presidente Betancourt', 'Presidente Páez', 'Presidente Rómulo Gallegos', 'Gabriel Picón González', 'Héctor Amable Mora', 'José Nucete Sardi', 'Pulido Méndez'],
                'Andrés Bello' => ['Andrés Bello', 'La Azulita'],
                'Antonio Pinto Salinas' => ['Antonio Pinto Salinas', 'Mesa de Las Palmas', 'Mesa de Bolívar'],
                'Aricagua' => ['Aricagua', 'San Antonio'],
                'Arzobispo Chacón' => ['Arzobispo Chacón', 'Capurí', 'Chacantá', 'El Molino', 'Guaimaral', 'Mucutuy', 'Mucuchachí'],
                'Campo Elías' => ['Campo Elías', 'Arnoldo Gabaldón', 'Santa Apolonia', 'El Limón', 'Santa Rosa'],
                'Caracciolo Parra Olmedo' => ['Caracciolo Parra Olmedo', 'Fernández Peña', 'La Venta', 'Matriz', 'Montalván', 'San José'],
                'Cardenal Quintero' => ['Cardenal Quintero', 'Rivas Berti', 'Santa Cruz de Mora', 'Pueblo Llano', 'Mesa de Quintero'],
                'Guaraque' => ['Guaraque', 'Chinameca', 'Nueva Bolivia', 'Santa Cruz de Guacas', 'El Tejero'],
                'Julio César Salas' => ['Julio César Salas', 'Octavio Cordero Palacios', 'La Mesa', 'Santo Domingo'],
                'Justo Briceño' => ['Justo Briceño', 'San Cristóbal de Torondoy', 'Torondoy'],
                'Libertador' => ['Libertador', 'Antonio Spinetti Dini', 'Arias', 'Caracciolo Parra Pérez', 'Domingo Peña', 'El Llano', 'Gonzalo Picón Febres', 'Jacinto Plaza', 'Juan Rodríguez Suárez', 'Lasso de la Vega', 'Mariano Picón Salas', 'Milla', 'Osuna Rodríguez', 'Sagrario'],
                'Miranda' => ['Miranda', 'Atarraga', 'La Fría', 'Santo Domingo'],
                'Obispo Ramos de Lora' => ['Obispo Ramos de Lora', 'Padre Noguera', 'Jorge Rodríguez'],
                'Padre Noguera' => ['Padre Noguera'],
                'Pueblo Llano' => ['Pueblo Llano'],
                'Rangel' => ['Rangel', 'Cacute', 'La Toma', 'Mucurubá', 'San Rafael'],
                'Rivas Dávila' => ['Rivas Dávila', 'Bailadores', 'Gerónimo Maldonado'],
                'Santos Marquina' => ['Santos Marquina', 'Tabay'],
                'Sucre' => ['Sucre', 'Arapuey', 'Palmira'],
                'Tovar' => ['Tovar', 'El Amparo', 'San José del Sur'],
                'Tulio Febres Cordero' => ['Tulio Febres Cordero', 'Independencia', 'María de la Concepción Palacios Blanco', 'Nueva América'],
                'Zea' => ['Zea', 'Caño El Tigre']
            ],
            
            // Miranda (VE-M)
            'VE-M' => [
                'Acevedo' => ['Acevedo', 'Caucagua', 'Aragüita', 'Arévalo González', 'Capaya', 'El Café', 'Marizapa', 'Panaquire', 'Ribas'],
                'Andrés Bello' => ['Andrés Bello', 'Cumbo'],
                'Baruta' => ['Baruta', 'El Cafetal', 'Las Minas'],
                'Brión' => ['Brión', 'Higuerote', 'Curiepe', 'Tacarigua'],
                'Bolívar' => ['Bolívar', 'San Francisco de Yare'],
                'Carrizal' => ['Carrizal'],
                'Chacao' => ['Chacao'],
                'Cristóbal Rojas' => ['Cristóbal Rojas', 'Charallave'],
                'El Hatillo' => ['El Hatillo'],
                'Guaicaipuro' => ['Guaicaipuro', 'Altagracia de la Montaña', 'Cecilio Acosta', 'El Jarillo', 'Los Teques', 'Paracotos', 'San Pedro'],
                'Independencia' => ['Independencia', 'Santa Teresa del Tuy'],
                'Lander' => ['Lander', 'Ocumare del Tuy', 'San Fernando del Guapo'],
                'Los Salias' => ['Los Salias', 'San Antonio de Los Altos'],
                'Páez' => ['Páez', 'Cúpira', 'Machurucuto'],
                'Paz Castillo' => ['Paz Castillo', 'Santa Lucía'],
                'Pedro Gual' => ['Pedro Gual', 'Caucagüita', 'Filas de Mariche', 'La Dolorita', 'Petare'],
                'Plaza' => ['Plaza', 'Guarenas'],
                'Simón Bolívar' => ['Simón Bolívar', 'San Antonio de Yare', 'San Francisco de Yare'],
                'Sucre' => ['Sucre', 'Carrizal', 'Chaguaramas'],
                'Urdaneta' => ['Urdaneta', 'Cúa', 'Nueva Cúa'],
                'Zamora' => ['Zamora', 'Guatire', 'Bolívar']
            ],
            
            // Monagas (VE-N)
            'VE-N' => [
                'Acosta' => ['Acosta', 'San Antonio de Maturín'],
                'Aguasay' => ['Aguasay'],
                'Bolívar' => ['Bolívar', 'Caripito'],
                'Caripe' => ['Caripe', 'El Guácharo', 'La Guanota', 'Sabana de Piedra', 'San Agustín', 'Teresen'],
                'Cedeño' => ['Cedeño', 'Aguasay', 'Caicara de Maturín'],
                'Ezequiel Zamora' => ['Ezequiel Zamora', 'El Tejero', 'Punta de Mata'],
                'Libertador' => ['Libertador', 'Chaguaramas', 'Las Alhuacas', 'Tabasca'],
                'Maturín' => ['Maturín', 'Alto de los Godos', 'Boquerón', 'Las Cocuizas', 'San Simón', 'Santa Cruz'],
                'Piar' => ['Piar', 'Aragua de Maturín', 'Chaguaramal', 'El Furrial', 'Jusepín', 'La Pica', 'San Vicente'],
                'Punceres' => ['Punceres', 'Cachipo', 'Quiriquire'],
                'Santa Bárbara' => ['Santa Bárbara', 'Barrancas', 'Los Barrancos de Fajardo'],
                'Sotillo' => ['Sotillo', 'Bideau', 'Santa Rosa', 'Uracoa'],
                'Uracoa' => ['Uracoa']
            ],
            
            // Nueva Esparta (VE-O)
            'VE-O' => [
                'Antolín del Campo' => ['Antolín del Campo', 'La Plaza'],
                'Arismendi' => ['Arismendi', 'La Asunción'],
                'Díaz' => ['Díaz', 'Zabala'],
                'García' => ['García', 'Francisco Fajardo'],
                'Gómez' => ['Gómez', 'Bolívar', 'Guevara', 'Matasiete', 'Santa Ana', 'Sucre'],
                'Maneiro' => ['Maneiro', 'Aguirre'],
                'Marcano' => ['Marcano', 'Adrián'],
                'Mariño' => ['Mariño', 'Juan Griego'],
                'Península de Macanao' => ['Península de Macanao', 'San Francisco'],
                'Tubores' => ['Tubores', 'Los Barales'],
                'Villalba' => ['Villalba', 'Vicente Fuentes']
            ],
            
            // Portuguesa (VE-P)
            'VE-P' => [
                'Agua Blanca' => ['Agua Blanca'],
                'Araure' => ['Araure', 'Río Acarigua'],
                'Esteller' => ['Esteller', 'Uveral'],
                'Guanare' => ['Guanare', 'Córdoba', 'San José de la Montaña', 'San Juan de Guanaguanare', 'Virgen del Socorro'],
                'Guanarito' => ['Guanarito', 'Trinidad de la Capilla', 'Divina Pastora'],
                'Monseñor José Vicente de Unda' => ['Monseñor José Vicente de Unda', 'Peña Blanca'],
                'Ospino' => ['Ospino', 'Aparición', 'La Estación'],
                'Páez' => ['Páez', 'Guayabal', 'La Misión'],
                'Papelón' => ['Papelón', 'Caño Delgadito'],
                'San Genaro de Boconoíto' => ['San Genaro de Boconoíto', 'Antolín Tovar'],
                'San Rafael de Onoto' => ['San Rafael de Onoto', 'Santa Fe', 'Thermo Morles'],
                'Santa Rosalía' => ['Santa Rosalía', 'Florida'],
                'Sucre' => ['Sucre', 'Concepción', 'San José de Saguazá'],
                'Turén' => ['Turén', 'Canelones', 'Santa Cruz', 'San Isidro']
            ],
            
            // Sucre (VE-R)
            'VE-R' => [
                'Andrés Eloy Blanco' => ['Andrés Eloy Blanco', 'Romulo Gallegos'],
                'Andrés Mata' => ['Andrés Mata', 'San José de Aerocuar'],
                'Arismendi' => ['Arismendi', 'Río Caribe', 'San Juan de las Galdonas', 'El Morro de Puerto Santo', 'Puerto Santo'],
                'Benítez' => ['Benítez', 'El Pilar', 'El Rincón', 'General Francisco Antonio Vásquez', 'Guaraúnos', 'Tunapuicito', 'Unión'],
                'Bermúdez' => ['Bermúdez', 'Santa Catalina', 'Santa Rosa', 'Santa Teresa', 'Bolívar', 'Maracapana'],
                'Bolívar' => ['Bolívar', 'Carúpano'],
                'Cajigal' => ['Cajigal', 'Yaguaraparo', 'Libertad'],
                'Cruz Salmerón Acosta' => ['Cruz Salmerón Acosta', 'Chacopata', 'Manicuare'],
                'Libertador' => ['Libertador', 'Campo Elías'],
                'Mariño' => ['Mariño', 'Irapa', 'Campo Claro', 'Marabal', 'San Antonio de Irapa', 'Soro'],
                'Mejía' => ['Mejía', 'Cumanacoa', 'Arenas', 'Aricagua', 'Cocollar', 'San Fernando', 'San Lorenzo'],
                'Montes' => ['Montes', 'Cariaco', 'Catuaro', 'Rendón', 'Santa Cruz', 'Santa María'],
                'Ribero' => ['Ribero', 'Santa Fe', 'San Lorenzo'],
                'Sucre' => ['Sucre', 'Altagracia', 'Ayacucho', 'Santa Inés', 'Valentín Valiente', 'San Juan'],
                'Valdez' => ['Valdez', 'Bideau', 'Cristóbal Colón', 'Güiria', 'Punta de Piedras']
            ],
            
            // Táchira (VE-S)
            'VE-S' => [
                'Andrés Bello' => ['Andrés Bello', 'Antonio Rómulo Costa'],
                'Antonio Rómulo Costa' => ['Antonio Rómulo Costa'],
                'Ayacucho' => ['Ayacucho', 'Rivas Berti', 'San Pedro del Río'],
                'Bolívar' => ['Bolívar', 'Palotal', 'General Juan Vicente Gómez', 'Isaías Medina Angarita'],
                'Cárdenas' => ['Cárdenas', 'Amenodoro Ángel Lamus', 'La Florida'],
                'Córdoba' => ['Córdoba'],
                'Fernández Feo' => ['Fernández Feo', 'Alberto Adriani', 'Santo Domingo'],
                'Francisco de Miranda' => ['Francisco de Miranda', 'El Dividive', 'La Pedrera'],
                'García de Hevia' => ['García de Hevia', 'Boca de Grita', 'José Antonio Páez'],
                'Guásimos' => ['Guásimos'],
                'Independencia' => ['Independencia', 'Juan Germán Roscio', 'Román Cárdenas'],
                'Jáuregui' => ['Jáuregui', 'Emilio Constantino Guerrero', 'Monseñor Miguel Antonio Salas'],
                'José María Vargas' => ['José María Vargas', 'La Concordia'],
                'Junín' => ['Junín', 'La Petrólea', 'Quinimarí', 'Bramón'],
                'Libertad' => ['Libertad', 'Cipriano Castro', 'Manuel Felipe Rugeles'],
                'Libertador' => ['Libertador', 'Doradas', 'Emeterio Ochoa', 'San Joaquín de Navay'],
                'Lobatera' => ['Lobatera', 'Constitución'],
                'Michelena' => ['Michelena'],
                'Panamericano' => ['Panamericano', 'La Palmita'],
                'Pedro María Ureña' => ['Pedro María Ureña', 'Nueva Arcadia'],
                'Rafael Urdaneta' => ['Rafael Urdaneta', 'Samuel Darío Maldonado', 'La Grita'],
                'Samuel Darío Maldonado' => ['Samuel Darío Maldonado', 'Boconó', 'Hernández'],
                'San Cristóbal' => ['San Cristóbal', 'La Concordia', 'Pedro María Morantes', 'San Juan Bautista', 'San Sebastián'],
                'San Judas Tadeo' => ['San Judas Tadeo', 'Uribante', 'El Carmen', 'Nueva Bolivia'],
                'Seboruco' => ['Seboruco'],
                'Simón Rodríguez' => ['Simón Rodríguez', 'Eleazar López Contreras'],
                'Sucre' => ['Sucre', 'San Pablo', 'San Simón'],
                'Torbes' => ['Torbes'],
                'Uribante' => ['Uribante', 'Cardenal Quintero', 'Juan Pablo Peñalosa', 'Potosí']
            ],
            
            // Trujillo (VE-T)
            'VE-T' => [
                'Andrés Bello' => ['Andrés Bello', 'Chiguará', 'Estánquez', 'La Mesa', 'La Puerta', 'Mendoza'],
                'Boconó' => ['Boconó', 'El Carmen', 'Mosquey', 'Rafael Rangel', 'San José', 'San Miguel'],
                'Bolívar' => ['Bolívar', 'Anzoátegui', 'Chamaca', 'Santa Cruz de Guacas'],
                'Candelaria' => ['Candelaria', 'Aguas Calientes', 'Mucurubá'],
                'Carache' => ['Carache', 'Cuicas', 'La Concepción', 'Panamericana', 'Santa Cruz'],
                'Escuque' => ['Escuque', 'La Unión', 'Santa Rita', 'Sabana Libre'],
                'José Felipe Márquez Cañizalez' => ['José Felipe Márquez Cañizalez', 'Arnoldo Gabaldón'],
                'Juan Vicente Campos Elías' => ['Juan Vicente Campos Elías', 'El Progreso', 'La Ceiba'],
                'La Ceiba' => ['La Ceiba', 'Tres de Febrero'],
                'Miranda' => ['Miranda', 'Monseñor Jáuregui', 'Motatán', 'Salvador'],
                'Monte Carmelo' => ['Monte Carmelo', 'Buena Vista', 'Santa María del Horcón'],
                'Motatán' => ['Motatán', 'El Baño', 'Jalisco'],
                'Pampán' => ['Pampán', 'Flor de Patria', 'La Paz', 'Santa Ana'],
                'Pampanito' => ['Pampanito', 'La Concepción', 'Pampanito II'],
                'Rafael Rangel' => ['Rafael Rangel', 'Betijoque', 'José Gregorio Hernández', 'La Pueblita'],
                'San Rafael de Carvajal' => ['San Rafael de Carvajal', 'Antonio Nicolás Briceño', 'Campo Alegre', 'José Leonardo Suárez'],
                'Sucre' => ['Sucre', 'Chiquinquirá', 'Santa Apolonia', 'Valera'],
                'Trujillo' => ['Trujillo', 'Andrés Linares', 'Cristóbal Mendoza', 'Cruz Carrillo', 'Matriz', 'Monseñor Carrillo', 'Tres Esquinas'],
                'Urdaneta' => ['Urdaneta', 'Cabimbú', 'Jajó', 'La Mesa de Esnujaque', 'Santiago'],
                'Valera' => ['Valera', 'Juan Ignacio Montilla', 'La Beatriz', 'La Puerta', 'Mendoza', 'San Luis']
            ],
            
            // Vargas (VE-X)
            'VE-X' => [
                'Vargas' => [
                    'Caraballeda', 'Carayaca', 'Carlos Soublette', 'Caruao', 'Catia La Mar', 
                    'El Junko', 'La Guaira', 'Macuto', 'Maiquetía', 'Naiguatá', 'Urimare'
                ]
            ],
            
            // Yaracuy (VE-U)
            'VE-U' => [
                'Arístides Bastidas' => ['Arístides Bastidas'],
                'Bolívar' => ['Bolívar', 'Chivacoa', 'Campo Elías'],
                'Bruzual' => ['Bruzual', 'José Antonio Páez'],
                'Cocorote' => ['Cocorote'],
                'Independencia' => ['Independencia', 'Olivera'],
                'José Antonio Páez' => ['José Antonio Páez', 'La Trinidad'],
                'La Trinidad' => ['La Trinidad'],
                'Manuel Monge' => ['Manuel Monge'],
                'Nirgua' => ['Nirgua', 'Salom', 'Temerla'],
                'Peña' => ['Peña', 'San Andrés'],
                'San Felipe' => ['San Felipe', 'Albarico', 'San Javier'],
                'Sucre' => ['Sucre', 'Guama', 'Urachiche'],
                'Urachiche' => ['Urachiche'],
                'Veroes' => ['Veroes', 'El Guayabo', 'Farriar']
            ],
            
            // Zulia (VE-V)
            'VE-V' => [
                'Almirante Padilla' => ['Almirante Padilla', 'El Toro', 'Las Piedras', 'San Francisco', 'Santa Rita'],
                'Baralt' => ['Baralt', 'General Urdaneta', 'Libertador', 'Manuel Guanipa Matos', 'Marcelino Briceño', 'Pueblo Nuevo', 'San Timoteo'],
                'Cabimas' => ['Cabimas', 'Carlos Quevedo', 'Francisco Javier Pulgar', 'Simón Rodríguez'],
                'Catatumbo' => ['Catatumbo', 'Chiquinquirá', 'Concepción', 'Pedro Lucas Urribarri', 'San Rafael'],
                'Colón' => ['Colón', 'Francisco Herrería', 'Los Cortijos', 'Marcial Hernández'],
                'Francisco Javier Pulgar' => ['Francisco Javier Pulgar', 'La Rosa', 'Pueblo Bello', 'Rómulo Betancourt', 'San Benito'],
                'Guajira' => ['Guajira', 'Donaldo García', 'Elías Sánchez Rubio', 'Las Parcelas', 'San Francisco'],
                'Jesús Enrique Losada' => ['Jesús Enrique Losada', 'La Concepción', 'Mariano Parra León', 'San José'],
                'Jesús María Semprún' => ['Jesús María Semprún', 'Barí'],
                'La Cañada de Urdaneta' => ['La Cañada de Urdaneta', 'Chiquichirgua', 'El Carmelo', 'Potreritos'],
                'Lagunillas' => ['Lagunillas', 'Donaldo García', 'Elías Sánchez Rubio', 'Las Parcelas', 'San Francisco'],
                'Machiques de Perijá' => ['Machiques de Perijá', 'Alonso de Ojeda', 'Libertad', 'Campo Lara', 'Eleazar López Contreras', 'Venezuela'],
                'Mara' => ['Mara', 'José Canelones', 'Miguel Segundo Rubio', 'Rafael Urdaneta', 'San Rafael'],
                'Maracaibo' => ['Maracaibo', 'Ana María Campos', 'Faria', 'San Antonio', 'San José', 'Santa Lucía'],
                'Miranda' => ['Miranda', 'La Victoria', 'Raúl Cuenca'],
                'Rosario de Perijá' => ['Rosario de Perijá', 'El Mene', 'José Cenobio Pastrán', 'Pedro Lucas Urribarri'],
                'San Francisco' => ['San Francisco', 'El Bajo', 'Domitila Flores', 'Francisco Ochoa', 'Los Cortijos', 'Marcial Hernández'],
                'Santa Rita' => ['Santa Rita', 'El Mene', 'José Cenobio Pastrán', 'Pedro Lucas Urribarri'],
                'Simón Bolívar' => ['Simón Bolívar', 'Coquivacoa', 'Cristo de Aranza', 'Chiquinquirá', 'Francisco Eugenio Bustamante', 'Idelfonso Vásquez', 'Juana de Avila', 'Luis Hurtado Higuera', 'Manuel Dagnino', 'Olegario Villalobos', 'Raúl Leoni', 'Santa Lucía'],
                'Sucre' => ['Sucre', 'Andrés Bello', 'Chiquinquirá', 'Concepción', 'El Carmelo', 'Potreritos'],
                'Valmore Rodríguez' => ['Valmore Rodríguez', 'Bolívar', 'Cacique Mara', 'Carrasquero', 'Cecilio Acosta']
            ]
        ];
        
        // Insertar parroquias
        foreach ($parroquias as $iso_estado => $municipios_data) {
            if (isset($estados[$iso_estado])) {
                foreach ($municipios_data as $nombre_municipio => $lista_parroquias) {
                    // Buscar el ID del municipio
                    if (isset($municipios_por_estado[$iso_estado][$nombre_municipio])) {
                        $municipio_id = $municipios_por_estado[$iso_estado][$nombre_municipio];
                        
                        foreach ($lista_parroquias as $nombre_parroquia) {
                            // Coordenadas de ejemplo para algunas parroquias principales
                            $coordenadas = $this->getCoordenadasParroquia($nombre_parroquia, $nombre_municipio, $iso_estado);
                            
                            DB::table('parroquias')->updateOrInsert(
                                ['nombre' => $nombre_parroquia, 'municipio_id' => $municipio_id],
                                [
                                    'nombre' => $nombre_parroquia, 
                                    'municipio_id' => $municipio_id,
                                    'latitud' => $coordenadas['latitud'],
                                    'longitud' => $coordenadas['longitud']
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Obtener coordenadas de parroquias principales
     */
    private function getCoordenadasParroquia($nombre_parroquia, $nombre_municipio, $estado_iso)
    {
        // Coordenadas de algunas parroquias principales de Venezuela
        $coordenadas = [
            'VE-A' => [ // Distrito Capital
                'Libertador' => [
                    'Catedral' => ['latitud' => 10.5000, 'longitud' => -66.9167],
                    'Altagracia' => ['latitud' => 10.4833, 'longitud' => -66.9000],
                    'La Candelaria' => ['latitud' => 10.5167, 'longitud' => -66.9333],
                    'El Paraíso' => ['latitud' => 10.4667, 'longitud' => -66.8833],
                    'El Valle' => ['latitud' => 10.4500, 'longitud' => -66.9333],
                    'Las Mercedes' => ['latitud' => 10.4667, 'longitud' => -66.8667],
                    'Petare' => ['latitud' => 10.4667, 'longitud' => -66.8000],
                    'El Junquito' => ['latitud' => 10.5000, 'longitud' => -66.9333]
                ]
            ],
            'VE-B' => [ // Anzoátegui
                'Barcelona' => [
                    'Barcelona' => ['latitud' => 10.1333, 'longitud' => -64.6833]
                ],
                'Sotillo' => [
                    'Puerto La Cruz' => ['latitud' => 10.2167, 'longitud' => -64.6167]
                ],
                'Simón Rodríguez' => [
                    'El Tigre' => ['latitud' => 9.5333, 'longitud' => -62.6167]
                ]
            ],
            'VE-C' => [ // Apure
                'San Fernando' => [
                    'San Fernando' => ['latitud' => 7.8833, 'longitud' => -67.4667]
                ]
            ],
            'VE-D' => [ // Aragua
                'Girardot' => [
                    'Maracay' => ['latitud' => 10.2446, 'longitud' => -67.5939]
                ]
            ],
            'VE-E' => [ // Barinas
                'Barinas' => [
                    'Barinas' => ['latitud' => 8.6167, 'longitud' => -70.2167]
                ]
            ],
            'VE-F' => [ // Bolívar
                'Caroní' => [
                    'Ciudad Guayana' => ['latitud' => 8.3500, 'longitud' => -62.6500]
                ],
                'Angostura' => [
                    'Ciudad Bolívar' => ['latitud' => 8.1167, 'longitud' => -63.5500]
                ]
            ],
            'VE-G' => [ // Carabobo
                'Valencia' => [
                    'Valencia' => ['latitud' => 10.1667, 'longitud' => -68.0000]
                ],
                'Puerto Cabello' => [
                    'Puerto Cabello' => ['latitud' => 10.4667, 'longitud' => -68.0333]
                ]
            ],
            'VE-H' => [ // Cojedes
                'San Carlos' => [
                    'San Carlos' => ['latitud' => 9.6500, 'longitud' => -68.5667]
                ]
            ],
            'VE-I' => [ // Falcón
                'Colina' => [
                    'La Vela de Coro' => ['latitud' => 11.7667, 'longitud' => -70.0833]
                ],
                'Falcón' => [
                    'Coro' => ['latitud' => 11.4000, 'longitud' => -69.6667]
                ]
            ],
            'VE-J' => [ // Guárico
                'José Félix Ribas' => [
                    'San Juan de los Morros' => ['latitud' => 9.9000, 'longitud' => -67.3500]
                ]
            ],
            'VE-K' => [ // Lara
                'Iribarren' => [
                    'Barquisimeto' => ['latitud' => 10.0667, 'longitud' => -69.3333]
                ]
            ],
            'VE-L' => [ // Mérida
                'Libertador' => [
                    'Mérida' => ['latitud' => 8.6000, 'longitud' => -71.1500]
                ]
            ],
            'VE-M' => [ // Miranda
                'Guaicaipuro' => [
                    'Los Teques' => ['latitud' => 10.2500, 'longitud' => -67.0333]
                ]
            ],
            'VE-N' => [ // Monagas
                'Maturín' => [
                    'Maturín' => ['latitud' => 9.7500, 'longitud' => -63.1667]
                ]
            ],
            'VE-O' => [ // Nueva Esparta
                'Mariño' => [
                    'Porlamar' => ['latitud' => 10.9500, 'longitud' => -63.8500]
                ],
                'Arismendi' => [
                    'La Asunción' => ['latitud' => 11.0333, 'longitud' => -63.8333]
                ]
            ],
            'VE-P' => [ // Portuguesa
                'Guanare' => [
                    'Guanare' => ['latitud' => 9.0333, 'longitud' => -69.7333]
                ]
            ],
            'VE-R' => [ // Sucre
                'Montes' => [
                    'Cumaná' => ['latitud' => 10.4500, 'longitud' => -64.1667]
                ]
            ],
            'VE-S' => [ // Táchira
                'San Cristóbal' => [
                    'San Cristóbal' => ['latitud' => 7.7667, 'longitud' => -72.2333]
                ]
            ],
            'VE-T' => [ // Trujillo
                'Trujillo' => [
                    'Trujillo' => ['latitud' => 9.3667, 'longitud' => -70.4333]
                ]
            ],
            'VE-U' => [ // Yaracuy
                'San Felipe' => [
                    'San Felipe' => ['latitud' => 10.3333, 'longitud' => -68.7500]
                ]
            ],
            'VE-V' => [ // Zulia
                'Maracaibo' => [
                    'Maracaibo' => ['latitud' => 10.6500, 'longitud' => -71.6500]
                ]
            ],
            'VE-X' => [ // Vargas
                'Vargas' => [
                    'La Guaira' => ['latitud' => 10.6000, 'longitud' => -66.9333]
                ]
            ],
            'VE-Y' => [ // Delta Amacuro
                'Tucupita' => [
                    'Tucupita' => ['latitud' => 9.0667, 'longitud' => -62.0500]
                ]
            ]
        ];

        // Verificar si tenemos coordenadas para esta parroquia
        if (isset($coordenadas[$estado_iso]) && 
            isset($coordenadas[$estado_iso][$nombre_municipio]) && 
            isset($coordenadas[$estado_iso][$nombre_municipio][$nombre_parroquia])) {
            return $coordenadas[$estado_iso][$nombre_municipio][$nombre_parroquia];
        }

        // Si no tenemos coordenadas específicas, usar coordenadas del municipio como base
        // y agregar un pequeño desplazamiento aleatorio para diferenciar
        $municipio_coords = DB::table('municipios')
            ->where('nombre', $nombre_municipio)
            ->where('estado_id', function($query) use ($estado_iso) {
                $query->select('id')->from('estados')->where('iso_3166_2', $estado_iso);
            })
            ->first(['latitud', 'longitud']);

        if ($municipio_coords && $municipio_coords->latitud && $municipio_coords->longitud) {
            // Agregar un pequeño desplazamiento aleatorio
            $desplazamiento_lat = (mt_rand(-50, 50) / 1000); // -0.05 a 0.05 grados
            $desplazamiento_lng = (mt_rand(-50, 50) / 1000);
            
            return [
                'latitud' => $municipio_coords->latitud + $desplazamiento_lat,
                'longitud' => $municipio_coords->longitud + $desplazamiento_lng
            ];
        }

        // Si no hay coordenadas del municipio, usar coordenadas del estado
        $estado_coords = DB::table('estados')
            ->where('iso_3166_2', $estado_iso)
            ->first(['latitud', 'longitud']);

        if ($estado_coords) {
            // Agregar un desplazamiento un poco mayor
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