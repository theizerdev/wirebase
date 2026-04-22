<?php

namespace Database\Seeders;

use App\Models\Moto;
use App\Models\MotoUnidad;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotoUnidadesSeeder extends Seeder
{
    public function run()
    {
        // Obtener empresa y sucursal
        $empresa = Empresa::first();
        $sucursal = Sucursal::where('empresa_id', $empresa->id)->first();
        
        if (!$empresa || !$sucursal) {
            $this->command->error('No se encontró empresa o sucursal. Por favor, ejecute EmpresaSeeder y SucursalSeeder primero.');
            return;
        }

        // Obtener todas las motos creadas
        $motos = Moto::all();
        
        if ($motos->isEmpty()) {
            $this->command->error('No se encontraron motos. Por favor, ejecute MotosSeeder primero.');
            return;
        }

        $this->command->info('Creando inventario de unidades de motos...');

        $unidadesCreadas = 0;
        $coloresDisponibles = [
            'Negro', 'Rojo', 'Azul', 'Blanco', 'Gris', 'Plateado', 'Verde', 
            'Naranja', 'Amarillo', 'Marrón', 'Bordó', 'Turquesa'
        ];

        // Crear múltiples unidades por cada modelo de moto
        foreach ($motos as $moto) {
            // Crear entre 2-5 unidades por cada modelo
            $cantidadUnidades = rand(2, 5);
            
            for ($i = 1; $i <= $cantidadUnidades; $i++) {
                $vin = $this->generarVIN();
                $numeroMotor = $this->generarNumeroMotor();
                $numeroChasis = $this->generarNumeroChasis();
                $placa = $this->generarPlaca();
                
                // Color aleatorio o el color principal de la moto
                $color = (rand(1, 3) == 1) ? $moto->color_principal : $coloresDisponibles[array_rand($coloresDisponibles)];
                
                // Estado aleatorio (mayoría disponible)
                $estados = ['disponible', 'disponible', 'disponible', 'reservado', 'mantenimiento'];
                $estado = $estados[array_rand($estados)];
                
                // Condición (mayoría nuevas)
                $condicion = (rand(1, 10) <= 8) ? 'nuevo' : 'usado';
                
                // Kilometraje según condición
                $kilometraje = ($condicion == 'nuevo') ? rand(0, 50) : rand(1000, 15000);
                
                // Precios con variación
                $precioVenta = $moto->precio_venta_base + rand(-200, 500) * 1000;
                $costoCompra = $moto->costo_referencial + rand(-100, 200) * 1000;

                try {
                    MotoUnidad::create([
                        'moto_id' => $moto->id,
                        'vin' => $vin,
                        'numero_motor' => $numeroMotor,
                        'numero_chasis' => $numeroChasis,
                        'placa' => ($estado != 'disponible') ? $placa : null, // Solo asignar placa si no está disponible
                        'color_especifico' => $color,
                        'kilometraje' => $kilometraje,
                        'costo_compra' => $costoCompra,
                        'precio_venta' => $precioVenta,
                        'estado' => $estado,
                        'condicion' => $condicion,
                        'fecha_ingreso' => now()->subDays(rand(1, 90)), // Fecha de ingreso en los últimos 3 meses
                        'fecha_venta' => null, // Se actualizará cuando se venda
                        'empresa_id' => $empresa->id,
                        'sucursal_id' => $sucursal->id,
                        'notas' => $this->generarNotas($estado, $condicion),
                    ]);
                    
                    $unidadesCreadas++;
                    
                } catch (\Exception $e) {
                    $this->command->error('Error al crear unidad: ' . $e->getMessage());
                }
            }
        }

        $this->command->info('✓ Inventario creado exitosamente');
        $this->command->info('Total de unidades creadas: ' . $unidadesCreadas);
        
        // Mostrar resumen por estado
        $resumen = MotoUnidad::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();
            
        $this->command->info('Resumen por estado:');
        foreach ($resumen as $item) {
            $this->command->info('  - ' . ucfirst($item->estado) . ': ' . $item->total . ' unidades');
        }
    }
    
    private function generarVIN(): string
    {
        // Generar un VIN de 17 caracteres simulado
        $vin = '';
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        
        for ($i = 0; $i < 17; $i++) {
            $vin .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        
        return $vin;
    }
    
    private function generarNumeroMotor(): string
    {
        // Generar número de motor de 12-15 caracteres
        $prefijos = ['MOT', 'ENG', 'NMR', 'MTR'];
        $prefijo = $prefijos[array_rand($prefijos)];
        $numero = rand(10000000, 99999999);
        $sufijo = rand(100, 999);
        
        return $prefijo . $numero . $sufijo;
    }
    
    private function generarNumeroChasis(): string
    {
        // Generar número de chasis de 10-12 caracteres
        $prefijos = ['CHA', 'CHS', 'CHI', 'FRAME'];
        $prefijo = $prefijos[array_rand($prefijos)];
        $numero = rand(1000000, 9999999);
        $sufijo = rand(10, 99);
        
        return $prefijo . $numero . $sufijo;
    }
    
    private function generarPlaca(): string
    {
        // Generar placa paraguaya (formato: ABC 123)
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';
        
        $placa = '';
        for ($i = 0; $i < 3; $i++) {
            $placa .= $letras[rand(0, strlen($letras) - 1)];
        }
        
        $placa .= ' ';
        
        for ($i = 0; $i < 3; $i++) {
            $placa .= $numeros[rand(0, strlen($numeros) - 1)];
        }
        
        return $placa;
    }
    
    private function generarNotas($estado, $condicion): ?string
    {
        $notas = [];
        
        if ($condicion == 'usado') {
            $notas[] = 'Unidad seminueva, excelente estado';
        }
        
        if ($estado == 'mantenimiento') {
            $notas[] = 'En mantenimiento preventivo';
        }
        
        if ($estado == 'reservado') {
            $notas[] = 'Reservada por cliente';
        }
        
        if (rand(1, 5) == 1) {
            $notasExtras = [
                'Incluye garantía extendida',
                'Kit de herramientas incluido',
                'Manual de usuario disponible',
                'Servicio de mantenimiento incluido',
                'Listo para entrega inmediata'
            ];
            $notas[] = $notasExtras[array_rand($notasExtras)];
        }
        
        return !empty($notas) ? implode('. ', $notas) : null;
    }
}