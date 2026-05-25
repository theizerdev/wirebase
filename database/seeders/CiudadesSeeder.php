<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los estados
        $estados = DB::table('estados')->pluck('id', 'iso_3166_2')->toArray();
        
        // Ciudades principales por estado
        $ciudades = [
            // Amazonas (VE-X)
            'VE-X' => [
                ['nombre' => 'Puerto Ayacucho', 'latitud' => 5.6639, 'longitud' => -67.6261],
                ['nombre' => 'San Fernando de Atabapo', 'latitud' => 4.0543, 'longitud' => -67.7040],
                ['nombre' => 'Maroa', 'latitud' => 1.8661, 'longitud' => -67.2617],
                ['nombre' => 'Puerto de Nutrias', 'latitud' => 5.9333, 'longitud' => -67.4333],
                ['nombre' => 'Isla Ratón', 'latitud' => 5.0833, 'longitud' => -67.3000],
                ['nombre' => 'San Juan de Manapiare', 'latitud' => 3.2333, 'longitud' => -66.2167],
                ['nombre' => 'La Esmeralda', 'latitud' => 1.4000, 'longitud' => -66.3667],
            ],
            
            // Anzoátegui (VE-B)
            'VE-B' => [
                ['nombre' => 'Barcelona', 'latitud' => 10.1333, 'longitud' => -64.6833],
                ['nombre' => 'Puerto La Cruz', 'latitud' => 10.2167, 'longitud' => -64.6167],
                ['nombre' => 'El Tigre', 'latitud' => 9.5333, 'longitud' => -62.6167],
                ['nombre' => 'Anaco', 'latitud' => 9.4333, 'longitud' => -64.4667],
                ['nombre' => 'Cantaura', 'latitud' => 9.2833, 'longitud' => -62.4500],
                ['nombre' => 'San Mateo', 'latitud' => 10.1833, 'longitud' => -64.8000],
                ['nombre' => 'Lecherías', 'latitud' => 9.2000, 'longitud' => -62.2833],
                ['nombre' => 'Pariaguán', 'latitud' => 10.2333, 'longitud' => -64.7167],
                ['nombre' => 'Pozuelos', 'latitud' => 10.2833, 'longitud' => -64.6000],
                ['nombre' => 'Onoto', 'latitud' => 9.8500, 'longitud' => -62.7333],
                ['nombre' => 'San José de Guanipa', 'latitud' => 9.9167, 'longitud' => -62.8333],
                ['nombre' => 'Clarines', 'latitud' => 10.0500, 'longitud' => -64.9500],
            ],
            
            // Apure (VE-C)
            'VE-C' => [
                ['nombre' => 'San Fernando de Apure', 'latitud' => 7.8833, 'longitud' => -67.4667],
                ['nombre' => 'Guasdualito', 'latitud' => 7.2333, 'longitud' => -70.7333],
                ['nombre' => 'Elorza', 'latitud' => 7.0833, 'longitud' => -69.5000],
                ['nombre' => 'Biruaca', 'latitud' => 8.1167, 'longitud' => -68.8333],
                ['nombre' => 'Las Delicias', 'latitud' => 7.5833, 'longitud' => -67.5333],
                ['nombre' => 'Achaguas', 'latitud' => 7.8833, 'longitud' => -68.5000],
                ['nombre' => 'Codazzi', 'latitud' => 8.2833, 'longitud' => -68.9833],
                ['nombre' => 'Bruzual', 'latitud' => 8.0333, 'longitud' => -68.7333],
            ],
            
            // Aragua (VE-D)
            'VE-D' => [
                ['nombre' => 'Maracay', 'latitud' => 10.2333, 'longitud' => -67.5833],
                ['nombre' => 'Turmero', 'latitud' => 10.2167, 'longitud' => -67.4667],
                ['nombre' => 'La Victoria', 'latitud' => 10.2000, 'longitud' => -67.3333],
                ['nombre' => 'Villa de Cura', 'latitud' => 10.0333, 'longitud' => -67.4667],
                ['nombre' => 'Santa Rita', 'latitud' => 10.1667, 'longitud' => -67.5000],
                ['nombre' => 'El Limón', 'latitud' => 10.3000, 'longitud' => -67.6333],
                ['nombre' => 'San Mateo', 'latitud' => 10.3667, 'longitud' => -67.3833],
                ['nombre' => 'Camatagua', 'latitud' => 10.1833, 'longitud' => -67.2500],
                ['nombre' => 'Santa Cruz', 'latitud' => 10.3000, 'longitud' => -67.8167],
                ['nombre' => 'Magdaleno', 'latitud' => 9.9833, 'longitud' => -67.3000],
                ['nombre' => 'Colonia Tovar', 'latitud' => 10.3333, 'longitud' => -67.3500],
                ['nombre' => 'Palo Negro', 'latitud' => 10.1833, 'longitud' => -67.5167],
            ],
            
            // Barinas (VE-E)
            'VE-E' => [
                ['nombre' => 'Barinas', 'latitud' => 8.6167, 'longitud' => -70.2167],
                ['nombre' => 'Barinitas', 'latitud' => 8.1667, 'longitud' => -70.9000],
                ['nombre' => 'Sabaneta', 'latitud' => 8.0000, 'longitud' => -71.2000],
                ['nombre' => 'El Cantón', 'latitud' => 7.8833, 'longitud' => -71.3667],
                ['nombre' => 'Socopó', 'latitud' => 8.2833, 'longitud' => -71.1167],
                ['nombre' => 'Ciudad Bolivia', 'latitud' => 8.1167, 'longitud' => -70.7667],
                ['nombre' => 'Libertad', 'latitud' => 8.3333, 'longitud' => -70.8333],
                ['nombre' => 'Arismendi', 'latitud' => 7.9667, 'longitud' => -71.2333],
                ['nombre' => 'Obispos', 'latitud' => 8.6167, 'longitud' => -70.8833],
                ['nombre' => 'Altamira', 'latitud' => 7.8000, 'longitud' => -71.3000],
                ['nombre' => 'Zona Delta', 'latitud' => 8.4000, 'longitud' => -70.5500],
            ],
            
            // Bolívar (VE-F)
            'VE-F' => [
                ['nombre' => 'Ciudad Bolívar', 'latitud' => 8.1167, 'longitud' => -63.5500],
                ['nombre' => 'Ciudad Guayana', 'latitud' => 8.3500, 'longitud' => -62.6500],
                ['nombre' => 'Upata', 'latitud' => 8.0167, 'longitud' => -62.4000],
                ['nombre' => 'Caicara del Orinoco', 'latitud' => 7.6167, 'longitud' => -66.1667],
                ['nombre' => 'El Callao', 'latitud' => 7.8000, 'longitud' => -61.8333],
                ['nombre' => 'Santa Elena de Uairén', 'latitud' => 4.5500, 'longitud' => -61.1333],
                ['nombre' => 'Ciudad Piar', 'latitud' => 8.2333, 'longitud' => -62.7500],
                ['nombre' => 'Tumeremo', 'latitud' => 8.1333, 'longitud' => -62.2167],
                ['nombre' => 'El Dorado', 'latitud' => 7.8333, 'longitud' => -61.7167],
                ['nombre' => 'Gran Sabana', 'latitud' => 5.3333, 'longitud' => -60.7000],
                ['nombre' => 'Ikabarú', 'latitud' => 5.8500, 'longitud' => -61.7333],
            ],
            
            // Carabobo (VE-G)
            'VE-G' => [
                ['nombre' => 'Valencia', 'latitud' => 10.1667, 'longitud' => -68.0000],
                ['nombre' => 'Puerto Cabello', 'latitud' => 10.4667, 'longitud' => -68.0333],
                ['nombre' => 'Guacara', 'latitud' => 10.2167, 'longitud' => -67.8667],
                ['nombre' => 'Vigirimita', 'latitud' => 10.1833, 'longitud' => -67.9500],
                ['nombre' => 'Naguanagua', 'latitud' => 10.2000, 'longitud' => -67.9500],
                ['nombre' => 'San Joaquín', 'latitud' => 10.2667, 'longitud' => -67.8167],
                ['nombre' => 'Los Guayos', 'latitud' => 10.1833, 'longitud' => -67.9333],
                ['nombre' => 'Bejuma', 'latitud' => 10.2500, 'longitud' => -68.1333],
                ['nombre' => 'Mariara', 'latitud' => 10.3000, 'longitud' => -67.7500],
                ['nombre' => 'Miranda', 'latitud' => 10.2167, 'longitud' => -67.7167],
                ['nombre' => 'Montalbán', 'latitud' => 10.1667, 'longitud' => -68.1000],
                ['nombre' => 'Morón', 'latitud' => 10.5000, 'longitud' => -68.2000],
            ],
            
            // Cojedes (VE-H)
            'VE-H' => [
                ['nombre' => 'San Carlos', 'latitud' => 9.6500, 'longitud' => -68.5667],
                ['nombre' => 'Tinaquillo', 'latitud' => 9.9167, 'longitud' => -68.8667],
                ['nombre' => 'El Baúl', 'latitud' => 9.5000, 'longitud' => -68.7500],
                ['nombre' => 'Libertad', 'latitud' => 9.7667, 'longitud' => -69.1333],
                ['nombre' => 'Las Vegas', 'latitud' => 9.8333, 'longitud' => -68.6667],
                ['nombre' => 'Aguirre', 'latitud' => 9.6000, 'longitud' => -68.6167],
                ['nombre' => 'El Pao', 'latitud' => 9.4167, 'longitud' => -68.7667],
                ['nombre' => 'Macapo', 'latitud' => 9.3667, 'longitud' => -69.0500],
                ['nombre' => 'El Amparo', 'latitud' => 9.3000, 'longitud' => -68.5000],
            ],
            
            // Delta Amacuro (VE-Y)
            'VE-Y' => [
                ['nombre' => 'Tucupita', 'latitud' => 9.0667, 'longitud' => -62.0500],
                ['nombre' => 'Pedernales', 'latitud' => 9.9833, 'longitud' => -62.2000],
                ['nombre' => 'Curiapo', 'latitud' => 8.8833, 'longitud' => -61.2000],
                ['nombre' => 'Sierra Imataca', 'latitud' => 9.3500, 'longitud' => -61.3500],
                ['nombre' => 'Morichal', 'latitud' => 9.6167, 'longitud' => -62.7000],
                ['nombre' => 'Boca de Chávez', 'latitud' => 8.9833, 'longitud' => -62.1167],
            ],
            
            // Distrito Capital (VE-A)
            'VE-A' => [
                ['nombre' => 'Caracas', 'latitud' => 10.4833, 'longitud' => -66.9167],
                ['nombre' => 'El Junquito', 'latitud' => 10.5000, 'longitud' => -66.9333],
                ['nombre' => 'Los Chaguaramos', 'latitud' => 10.4833, 'longitud' => -66.8667],
                ['nombre' => 'El Cafetal', 'latitud' => 10.4667, 'longitud' => -66.8333],
                ['nombre' => 'Petare', 'latitud' => 10.4667, 'longitud' => -66.8000],
                ['nombre' => 'Cúa', 'latitud' => 10.1500, 'longitud' => -66.9000],
                ['nombre' => 'Charallave', 'latitud' => 10.2500, 'longitud' => -66.8500],
                ['nombre' => 'Santa Teresa', 'latitud' => 10.2333, 'longitud' => -66.6500],
                ['nombre' => 'Los Teques', 'latitud' => 10.3333, 'longitud' => -67.0333],
                ['nombre' => 'Guarenas', 'latitud' => 10.4667, 'longitud' => -66.6167],
                ['nombre' => 'Guatire', 'latitud' => 10.4833, 'longitud' => -66.5333],
            ],
            
            // Falcón (VE-I)
            'VE-I' => [
                ['nombre' => 'Coro', 'latitud' => 11.4000, 'longitud' => -69.6667],
                ['nombre' => 'Punto Fijo', 'latitud' => 11.6833, 'longitud' => -70.2000],
                ['nombre' => 'Santa Ana de Coro', 'latitud' => 11.4000, 'longitud' => -69.6667],
                ['nombre' => 'Chichiriviche', 'latitud' => 11.1000, 'longitud' => -69.5333],
                ['nombre' => 'Dabajuro', 'latitud' => 11.1833, 'longitud' => -70.7833],
                ['nombre' => 'Paraguaná', 'latitud' => 11.7833, 'longitud' => -70.1500],
                ['nombre' => 'Tucacas', 'latitud' => 10.7833, 'longitud' => -68.3167],
                ['nombre' => 'La Vela de Coro', 'latitud' => 11.7667, 'longitud' => -70.0833],
                ['nombre' => 'Santa Cruz de Bucaral', 'latitud' => 11.7167, 'longitud' => -70.2500],
                ['nombre' => 'Mirimire', 'latitud' => 10.8667, 'longitud' => -68.4167],
                ['nombre' => 'Capadare', 'latitud' => 11.4667, 'longitud' => -69.4333],
                ['nombre' => 'Yaracal', 'latitud' => 11.1167, 'longitud' => -69.6167],
            ],
            
            // Guárico (VE-J)
            'VE-J' => [
                ['nombre' => 'San Juan de los Morros', 'latitud' => 9.9000, 'longitud' => -67.3500],
                ['nombre' => 'Valle de la Pascua', 'latitud' => 9.2167, 'longitud' => -65.9833],
                ['nombre' => 'El Socorro', 'latitud' => 9.3333, 'longitud' => -65.7833],
                ['nombre' => 'Calabozo', 'latitud' => 8.9167, 'longitud' => -67.4167],
                ['nombre' => 'Zaraza', 'latitud' => 9.3500, 'longitud' => -65.3333],
                ['nombre' => 'Altagracia de Orituco', 'latitud' => 9.8667, 'longitud' => -66.3667],
                ['nombre' => 'Camaguán', 'latitud' => 9.3000, 'longitud' => -67.4500],
                ['nombre' => 'San José de Guaribe', 'latitud' => 9.9667, 'longitud' => -65.4167],
                ['nombre' => 'Tucupido', 'latitud' => 9.9833, 'longitud' => -64.7000],
                ['nombre' => 'San Francisco de Macanao', 'latitud' => 10.3167, 'longitud' => -64.7167],
                ['nombre' => 'Chaguaramas', 'latitud' => 9.1833, 'longitud' => -67.3167],
                ['nombre' => 'Ortiz', 'latitud' => 9.5833, 'longitud' => -66.2167],
            ],
            
            // Lara (VE-K)
            'VE-K' => [
                ['nombre' => 'Barquisimeto', 'latitud' => 10.0667, 'longitud' => -69.3333],
                ['nombre' => 'Quibor', 'latitud' => 9.9000, 'longitud' => -69.5500],
                ['nombre' => 'El Tocuyo', 'latitud' => 9.7833, 'longitud' => -69.7500],
                ['nombre' => 'Cabudare', 'latitud' => 10.0000, 'longitud' => -69.2667],
                ['nombre' => 'Carora', 'latitud' => 10.1833, 'longitud' => -70.0667],
                ['nombre' => 'Sarare', 'latitud' => 9.9667, 'longitud' => -69.2333],
                ['nombre' => 'Siquisique', 'latitud' => 10.0333, 'longitud' => -69.6000],
                ['nombre' => 'Humocaro Alto', 'latitud' => 10.1333, 'longitud' => -69.3000],
                ['nombre' => 'Humocaro Bajo', 'latitud' => 10.1000, 'longitud' => -69.2833],
                ['nombre' => 'Moroturo', 'latitud' => 9.8167, 'longitud' => -69.5333],
                ['nombre' => 'Duaca', 'latitud' => 9.7667, 'longitud' => -69.2000],
                ['nombre' => 'Araure', 'latitud' => 9.5833, 'longitud' => -69.2333],
            ],
            
            // Mérida (VE-L)
            'VE-L' => [
                ['nombre' => 'Mérida', 'latitud' => 8.6000, 'longitud' => -71.1500],
                ['nombre' => 'Ejido', 'latitud' => 8.5500, 'longitud' => -71.2333],
                ['nombre' => 'Tovar', 'latitud' => 8.7167, 'longitud' => -71.6333],
                ['nombre' => 'Santa Cruz de Mora', 'latitud' => 8.2333, 'longitud' => -71.7333],
                ['nombre' => 'Santa Elena de Arenales', 'latitud' => 8.6833, 'longitud' => -71.2167],
                ['nombre' => 'Timotes', 'latitud' => 8.5833, 'longitud' => -71.0167],
                ['nombre' => 'La Azulita', 'latitud' => 8.5667, 'longitud' => -71.4333],
                ['nombre' => 'Santa María de Caparo', 'latitud' => 8.3500, 'longitud' => -71.3167],
                ['nombre' => 'Pueblo Llano', 'latitud' => 8.2667, 'longitud' => -71.5667],
                ['nombre' => 'Mucuchíes', 'latitud' => 8.8667, 'longitud' => -71.7333],
                ['nombre' => 'Bailadores', 'latitud' => 8.5333, 'longitud' => -71.4833],
                ['nombre' => 'Tabay', 'latitud' => 8.5333, 'longitud' => -71.2000],
            ],
            
            // Miranda (VE-M)
            'VE-M' => [
                ['nombre' => 'Los Teques', 'latitud' => 10.3333, 'longitud' => -67.0333],
                ['nombre' => 'Guarenas', 'latitud' => 10.4667, 'longitud' => -66.6167],
                ['nombre' => 'Guatire', 'latitud' => 10.4833, 'longitud' => -66.5333],
                ['nombre' => 'Santa Teresa del Tuy', 'latitud' => 10.2333, 'longitud' => -66.6500],
                ['nombre' => 'Charallave', 'latitud' => 10.2500, 'longitud' => -66.8500],
                ['nombre' => 'Cúa', 'latitud' => 10.1500, 'longitud' => -66.9000],
                ['nombre' => 'Ocumare del Tuy', 'latitud' => 10.1167, 'longitud' => -66.7833],
                ['nombre' => 'San Antonio de Los Altos', 'latitud' => 10.3833, 'longitud' => -66.9833],
                ['nombre' => 'El Hatillo', 'latitud' => 10.4333, 'longitud' => -66.8167],
                ['nombre' => 'Chacao', 'latitud' => 10.5000, 'longitud' => -66.8500],
                ['nombre' => 'Baruta', 'latitud' => 10.4333, 'longitud' => -66.8833],
                ['nombre' => 'Carrizal', 'latitud' => 10.3500, 'longitud' => -66.9500],
                ['nombre' => 'Petare', 'latitud' => 10.4667, 'longitud' => -66.8000],
                ['nombre' => 'Sucre', 'latitud' => 10.4500, 'longitud' => -66.6667],
                ['nombre' => 'Bolívar', 'latitud' => 10.4000, 'longitud' => -66.7833],
                ['nombre' => 'Guaicaipuro', 'latitud' => 10.3000, 'longitud' => -67.1000],
            ],
            
            // Monagas (VE-N)
            'VE-N' => [
                ['nombre' => 'Maturín', 'latitud' => 9.7500, 'longitud' => -63.1833],
                ['nombre' => 'Aragua de Maturín', 'latitud' => 9.6167, 'longitud' => -63.1667],
                ['nombre' => 'Punta de Mata', 'latitud' => 9.3000, 'longitud' => -63.2000],
                ['nombre' => 'Caripe', 'latitud' => 10.1333, 'longitud' => -63.4500],
                ['nombre' => 'Quiriquire', 'latitud' => 9.8833, 'longitud' => -63.1167],
                ['nombre' => 'Santa Bárbara', 'latitud' => 9.9667, 'longitud' => -63.7167],
                ['nombre' => 'Barrancas', 'latitud' => 9.5333, 'longitud' => -63.1000],
                ['nombre' => 'Uracoa', 'latitud' => 9.7000, 'longitud' => -63.3000],
                ['nombre' => 'Temblador', 'latitud' => 9.8500, 'longitud' => -62.9833],
                ['nombre' => 'Chaguaramas', 'latitud' => 9.8167, 'longitud' => -63.3833],
                ['nombre' => 'Jusepín', 'latitud' => 10.0000, 'longitud' => -63.4500],
                ['nombre' => 'La Toscana', 'latitud' => 9.7667, 'longitud' => -63.0667],
            ],
            
            // Nueva Esparta (VE-O)
            'VE-O' => [
                ['nombre' => 'La Asunción', 'latitud' => 11.0333, 'longitud' => -63.8833],
                ['nombre' => 'Porlamar', 'latitud' => 10.9500, 'longitud' => -63.8500],
                ['nombre' => 'Boca de Río', 'latitud' => 11.0667, 'longitud' => -63.8333],
                ['nombre' => 'Pampatar', 'latitud' => 11.0000, 'longitud' => -63.9833],
                ['nombre' => 'Juan Griego', 'latitud' => 11.1833, 'longitud' => -63.9500],
                ['nombre' => 'El Valle', 'latitud' => 11.0000, 'longitud' => -63.9167],
                ['nombre' => 'San Juan Bautista', 'latitud' => 10.9333, 'longitud' => -64.0667],
                ['nombre' => 'Santa Ana', 'latitud' => 11.0167, 'longitud' => -63.8333],
                ['nombre' => 'Punta de Piedras', 'latitud' => 11.0333, 'longitud' => -63.9333],
                ['nombre' => 'Los Barales', 'latitud' => 11.1333, 'longitud' => -63.9000],
            ],
            
            // Portuguesa (VE-P)
            'VE-P' => [
                ['nombre' => 'Guanare', 'latitud' => 9.0500, 'longitud' => -69.7500],
                ['nombre' => 'Acarigua', 'latitud' => 9.5500, 'longitud' => -69.2000],
                ['nombre' => 'Araure', 'latitud' => 9.5833, 'longitud' => -69.2333],
                ['nombre' => 'Píritu', 'latitud' => 9.2000, 'longitud' => -69.5000],
                ['nombre' => 'Guanarito', 'latitud' => 9.9167, 'longitud' => -69.7333],
                ['nombre' => 'Papelón', 'latitud' => 9.5333, 'longitud' => -69.6333],
                ['nombre' => 'Boconoíto', 'latitud' => 9.2167, 'longitud' => -69.8333],
                ['nombre' => 'San Rafael de Onoto', 'latitud' => 9.0333, 'longitud' => -69.5333],
                ['nombre' => 'Santa Rosalía', 'latitud' => 9.7667, 'longitud' => -69.3167],
                ['nombre' => 'Villa Bruzual', 'latitud' => 9.2667, 'longitud' => -69.6833],
                ['nombre' => 'Córdoba', 'latitud' => 9.1167, 'longitud' => -69.5833],
                ['nombre' => 'Florida', 'latitud' => 9.3833, 'longitud' => -69.4167],
            ],
            
            // Sucre (VE-R)
            'VE-R' => [
                ['nombre' => 'Cumaná', 'latitud' => 10.4500, 'longitud' => -64.1667],
                ['nombre' => 'Carúpano', 'latitud' => 10.6667, 'longitud' => -63.2500],
                ['nombre' => 'El Tigre', 'latitud' => 10.6167, 'longitud' => -62.9833],
                ['nombre' => 'Yaguaraparo', 'latitud' => 10.4833, 'longitud' => -62.8833],
                ['nombre' => 'Araya', 'latitud' => 10.5167, 'longitud' => -64.2000],
                ['nombre' => 'Cariaco', 'latitud' => 10.6667, 'longitud' => -63.4500],
                ['nombre' => 'Casanay', 'latitud' => 10.5667, 'longitud' => -63.2833],
                ['nombre' => 'Irapa', 'latitud' => 10.4833, 'longitud' => -62.6333],
                ['nombre' => 'San Antonio del Golfo', 'latitud' => 10.7167, 'longitud' => -63.1500],
                ['nombre' => 'Santa Fe', 'latitud' => 10.6000, 'longitud' => -62.7167],
                ['nombre' => 'Tunapuy', 'latitud' => 10.5333, 'longitud' => -62.8333],
                ['nombre' => 'Villa Frontado', 'latitud' => 10.4000, 'longitud' => -64.2333],
            ],
            
            // Táchira (VE-S)
            'VE-S' => [
                ['nombre' => 'San Cristóbal', 'latitud' => 7.7667, 'longitud' => -72.2167],
                ['nombre' => 'Táriba', 'latitud' => 7.7333, 'longitud' => -72.2667],
                ['nombre' => 'Rubio', 'latitud' => 7.7000, 'longitud' => -72.3667],
                ['nombre' => 'Capacho Nuevo', 'latitud' => 7.8000, 'longitud' => -72.3000],
                ['nombre' => 'La Fría', 'latitud' => 8.2167, 'longitud' => -72.2667],
                ['nombre' => 'Palmira', 'latitud' => 7.4833, 'longitud' => -72.2000],
                ['nombre' => 'Colón', 'latitud' => 8.0667, 'longitud' => -72.2333],
                ['nombre' => 'Ureña', 'latitud' => 7.8333, 'longitud' => -72.4000],
                ['nombre' => 'Delicias', 'latitud' => 7.7167, 'longitud' => -72.2833],
                ['nombre' => 'La Grita', 'latitud' => 7.9000, 'longitud' => -72.2333],
                ['nombre' => 'Michelena', 'latitud' => 7.9667, 'longitud' => -72.2500],
                ['nombre' => 'Abejales', 'latitud' => 7.9167, 'longitud' => -72.3000],
                ['nombre' => 'Borota', 'latitud' => 7.9333, 'longitud' => -72.1833],
                ['nombre' => 'Capacho Viejo', 'latitud' => 7.7833, 'longitud' => -72.3667],
                ['nombre' => 'Cordero', 'latitud' => 8.0500, 'longitud' => -72.1333],
            ],
            
            // Trujillo (VE-T)
            'VE-T' => [
                ['nombre' => 'Trujillo', 'latitud' => 9.3667, 'longitud' => -70.4333],
                ['nombre' => 'Valera', 'latitud' => 9.3167, 'longitud' => -70.6000],
                ['nombre' => 'Boconó', 'latitud' => 9.2500, 'longitud' => -70.2167],
                ['nombre' => 'Carache', 'latitud' => 9.5667, 'longitud' => -70.2000],
                ['nombre' => 'Escuque', 'latitud' => 9.2500, 'longitud' => -70.7500],
                ['nombre' => 'Campo Elías', 'latitud' => 9.4333, 'longitud' => -70.3333],
                ['nombre' => 'Pampan', 'latitud' => 9.1333, 'longitud' => -70.5167],
                ['nombre' => 'Santa Apolonia', 'latitud' => 9.5000, 'longitud' => -70.6833],
                ['nombre' => 'Sabana de Mendoza', 'latitud' => 9.2167, 'longitud' => -70.6333],
                ['nombre' => 'Chameta', 'latitud' => 9.6167, 'longitud' => -70.3167],
                ['nombre' => 'Chejendé', 'latitud' => 9.2833, 'longitud' => -70.7167],
                ['nombre' => 'Motatán', 'latitud' => 9.2000, 'longitud' => -70.8667],
                ['nombre' => 'Pampán', 'latitud' => 9.1167, 'longitud' => -70.5000],
                ['nombre' => 'Betijoque', 'latitud' => 9.3333, 'longitud' => -70.5167],
                ['nombre' => 'La Ceiba', 'latitud' => 9.1500, 'longitud' => -70.8167],
            ],
            
            // Vargas (VE-X)
            'VE-X' => [
                ['nombre' => 'La Guaira', 'latitud' => 10.6000, 'longitud' => -66.9333],
                ['nombre' => 'Caraballeda', 'latitud' => 10.6000, 'longitud' => -66.9000],
                ['nombre' => 'Naiguatá', 'latitud' => 10.5167, 'longitud' => -66.8000],
                ['nombre' => 'Maiquetía', 'latitud' => 10.6000, 'longitud' => -67.0000],
                ['nombre' => 'Carayaca', 'latitud' => 10.5833, 'longitud' => -67.2000],
                ['nombre' => 'Carlos Soublette', 'latitud' => 10.5667, 'longitud' => -67.1167],
                ['nombre' => 'Macuto', 'latitud' => 10.6167, 'longitud' => -66.9167],
                ['nombre' => 'Catia La Mar', 'latitud' => 10.6000, 'longitud' => -67.0333],
                ['nombre' => 'La Guaira', 'latitud' => 10.6000, 'longitud' => -66.9333],
                ['nombre' => 'San Antonio', 'latitud' => 10.5500, 'longitud' => -66.7667],
            ],
            
            // Yaracuy (VE-U)
            'VE-U' => [
                ['nombre' => 'San Felipe', 'latitud' => 10.3333, 'longitud' => -68.7500],
                ['nombre' => 'Yaritagua', 'latitud' => 10.0833, 'longitud' => -69.0000],
                ['nombre' => 'Nirgua', 'latitud' => 10.1500, 'longitud' => -68.5667],
                ['nombre' => 'Cocorote', 'latitud' => 10.3167, 'longitud' => -68.8000],
                ['nombre' => 'Boraure', 'latitud' => 10.2833, 'longitud' => -68.6833],
                ['nombre' => 'Guama', 'latitud' => 10.2167, 'longitud' => -68.8833],
                ['nombre' => 'Urachiche', 'latitud' => 10.1500, 'longitud' => -68.9500],
                ['nombre' => 'Farriar', 'latitud' => 10.3000, 'longitud' => -68.9167],
                ['nombre' => 'El Guayabo', 'latitud' => 10.3667, 'longitud' => -68.8667],
                ['nombre' => 'San Pablo', 'latitud' => 10.2500, 'longitud' => -68.7833],
                ['nombre' => 'Aroa', 'latitud' => 10.0167, 'longitud' => -69.1167],
                ['nombre' => 'Chivacoa', 'latitud' => 10.1500, 'longitud' => -68.9000],
            ],
            
            // Zulia (VE-V)
            'VE-V' => [
                ['nombre' => 'Maracaibo', 'latitud' => 10.6500, 'longitud' => -71.6000],
                ['nombre' => 'Cabimas', 'latitud' => 10.4000, 'longitud' => -71.4333],
                ['nombre' => 'Ciudad Ojeda', 'latitud' => 10.2333, 'longitud' => -71.3000],
                ['nombre' => 'Machiques', 'latitud' => 10.0500, 'longitud' => -72.5333],
                ['nombre' => 'Santa Rita', 'latitud' => 10.5167, 'longitud' => -71.5167],
                ['nombre' => 'Valera', 'latitud' => 10.4333, 'longitud' => -71.4333],
                ['nombre' => 'La Concepción', 'latitud' => 10.6333, 'longitud' => -71.7000],
                ['nombre' => 'Casigua', 'latitud' => 10.1667, 'longitud' => -71.2000],
                ['nombre' => 'San Carlos del Zulia', 'latitud' => 9.0167, 'longitud' => -71.8000],
                ['nombre' => 'San Francisco', 'latitud' => 10.5333, 'longitud' => -71.9667],
                ['nombre' => 'Baralt', 'latitud' => 10.4000, 'longitud' => -71.3667],
                ['nombre' => 'La Villa del Rosario', 'latitud' => 10.3000, 'longitud' => -71.3333],
                ['nombre' => 'Bachaquero', 'latitud' => 10.2333, 'longitud' => -71.3333],
                ['nombre' => 'Los Puertos de Altagracia', 'latitud' => 10.3500, 'longitud' => -71.3667],
                ['nombre' => 'Tía Juana', 'latitud' => 10.2833, 'longitud' => -71.3833],
                ['nombre' => 'Bobures', 'latitud' => 9.5833, 'longitud' => -70.9333],
                ['nombre' => 'Bolívar', 'latitud' => 9.8000, 'longitud' => -70.8667],
                ['nombre' => 'Sinamaica', 'latitud' => 10.1500, 'longitud' => -71.8833],
                ['nombre' => 'San Rafael del Moján', 'latitud' => 9.9667, 'longitud' => -70.9500],
                ['nombre' => 'La Ensenada', 'latitud' => 10.7000, 'longitud' => -71.7333],
            ],
        ];
        
        foreach ($ciudades as $iso_estado => $lista_ciudades) {
            if (isset($estados[$iso_estado])) {
                $estado_id = $estados[$iso_estado];
                
                foreach ($lista_ciudades as $ciudad) {
                    DB::table('ciudades')->updateOrInsert(
                        ['nombre' => $ciudad['nombre'], 'estado_id' => $estado_id],
                        [
                            'nombre' => $ciudad['nombre'],
                            'estado_id' => $estado_id,
                            'latitud' => $ciudad['latitud'],
                            'longitud' => $ciudad['longitud']
                        ]
                    );
                }
            }
        }
    }
}