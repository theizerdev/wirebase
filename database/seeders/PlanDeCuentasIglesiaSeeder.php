<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaContable;
use App\Models\Iglesia;

class PlanDeCuentasIglesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $iglesias = Iglesia::all();

        foreach ($iglesias as $iglesia) {
            $this->crearPlanDeCuentas($iglesia);
        }
    }

    /**
     * Crear plan de cuentas para una iglesia específica
     */
    private function crearPlanDeCuentas(Iglesia $iglesia): void
    {
        // ACTIVOS
        $activos = [
            ['codigo' => '1.1.01', 'nombre' => 'Caja General', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '1.1.02', 'nombre' => 'Caja Chica', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '1.1.03', 'nombre' => 'Banco - Cuenta Corriente', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '1.1.04', 'nombre' => 'Banco - Cuenta de Ahorros', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'nivel' => 2],
        ];

        // PASIVOS
        $pasivos = [
            ['codigo' => '2.1.01', 'nombre' => 'Cuentas por Pagar', 'tipo' => 'pasivo', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '2.1.02', 'nombre' => 'Obligaciones Laborales', 'tipo' => 'pasivo', 'naturaleza' => 'acreedora', 'nivel' => 2],
        ];

        // PATRIMONIO
        $patrimonio = [
            ['codigo' => '3.1.01', 'nombre' => 'Patrimonio Inicial', 'tipo' => 'patrimonio', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '3.2.01', 'nombre' => 'Resultado del Ejercicio', 'tipo' => 'patrimonio', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '3.2.02', 'nombre' => 'Resultados Acumulados', 'tipo' => 'patrimonio', 'naturaleza' => 'acreedora', 'nivel' => 2],
        ];

        // INGRESOS
        $ingresos = [
            ['codigo' => '4.1.01', 'nombre' => 'Diezmos', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '4.1.02', 'nombre' => 'Ofrendas Generales', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '4.1.03', 'nombre' => 'Aportes Especiales', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '4.1.04', 'nombre' => 'Donaciones', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'nivel' => 2],
            ['codigo' => '4.1.05', 'nombre' => 'Otros Ingresos', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'nivel' => 2],
        ];

        // EGRESOS - Gastos Operativos
        $gastosOperativos = [
            ['codigo' => '5.1.01', 'nombre' => 'Gastos Operativos', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '5.1.01.001', 'nombre' => 'Servicios Públicos', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.002', 'nombre' => 'Mantenimiento y Reparaciones', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.003', 'nombre' => 'Alquiler', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.004', 'nombre' => 'Artículos de Limpieza', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.005', 'nombre' => 'Papelería y Útiles', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.006', 'nombre' => 'Seguros', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
            ['codigo' => '5.1.01.007', 'nombre' => 'Impuestos y Tasas', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.01'],
        ];

        // EGRESOS - Gastos de Ministerio
        $gastosMinisterio = [
            ['codigo' => '5.1.02', 'nombre' => 'Gastos de Ministerio', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '5.1.02.001', 'nombre' => 'Material Educativo', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
            ['codigo' => '5.1.02.002', 'nombre' => 'Eventos Evangélicos', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
            ['codigo' => '5.1.02.003', 'nombre' => 'Misiones', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
            ['codigo' => '5.1.02.004', 'nombre' => 'Obra Social', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
            ['codigo' => '5.1.02.005', 'nombre' => 'Música y Alabanza', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
            ['codigo' => '5.1.02.006', 'nombre' => 'Decoración del Templo', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 3, 'padre' => '5.1.02'],
        ];

        // EGRESOS - Otros
        $otrosGastos = [
            ['codigo' => '5.1.03', 'nombre' => 'Honorarios Pastorales', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 2],
            ['codigo' => '5.1.04', 'nombre' => 'Otros Gastos', 'tipo' => 'egreso', 'naturaleza' => 'deudora', 'nivel' => 2],
        ];

        // Combinar todas las cuentas
        $todasLasCuentas = array_merge(
            $activos,
            $pasivos,
            $patrimonio,
            $ingresos,
            $gastosOperativos,
            $gastosMinisterio,
            $otrosGastos
        );

        // Crear cuentas
        foreach ($todasLasCuentas as $cuentaData) {
            $cuentaPadreId = null;

            // Buscar cuenta padre si existe
            if (isset($cuentaData['padre'])) {
                $cuentaPadre = CuentaContable::where('codigo', $cuentaData['padre'])
                    ->where('iglesia_id', $iglesia->id)
                    ->first();

                if ($cuentaPadre) {
                    $cuentaPadreId = $cuentaPadre->id;
                }
            }

            // Verificar si la cuenta ya existe para esta iglesia
            $exists = CuentaContable::where('codigo', $cuentaData['codigo'])
                ->where('iglesia_id', $iglesia->id)
                ->exists();

            if ($exists) {
                continue; // Saltar esta cuenta si ya existe
            }

            CuentaContable::create([
                'codigo' => $cuentaData['codigo'],
                'nombre' => $cuentaData['nombre'],
                'tipo' => $cuentaData['tipo'],
                'naturaleza' => $cuentaData['naturaleza'],
                'nivel' => $cuentaData['nivel'],
                'cuenta_padre_id' => $cuentaPadreId,
                'acepta_movimientos' => $cuentaData['nivel'] >= 2, // Solo cuentas de nivel 2+ aceptan movimientos
                'descripcion' => "Cuenta contable para {$cuentaData['nombre']}",
                'activo' => true,
                'iglesia_id' => $iglesia->id,
                'empresa_id' => $iglesia->empresa_id ?? 1, // Default a empresa 1 si es null
                'sucursal_id' => $iglesia->sucursal_id && \App\Models\Sucursal::find($iglesia->sucursal_id) ? $iglesia->sucursal_id : null,
            ]);
        }
    }
}
