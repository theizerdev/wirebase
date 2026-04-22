<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Moto;
use App\Models\MotoUnidad;
use App\Models\Contrato;
use App\Models\Pago;
use Database\Seeders\RolesAndPermissionsSeeder;
use Carbon\Carbon;

class ClientDataFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_shows_contratos_and_pagos_for_authenticated_cliente(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $empresa = Empresa::create([
            'razon_social' => 'Empresa Prueba',
            'documento' => 'J000000000',
        ]);
        $sucursal = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Centro',
        ]);

        $moto = Moto::create([
            'marca' => 'Yamaha',
            'modelo' => 'FZ',
            'anio' => 2024,
            'precio_venta_base' => 2000,
            'empresa_id' => $empresa->id,
        ]);

        $unidad1 = MotoUnidad::create([
            'moto_id' => $moto->id,
            'vin' => 'VINTEST0001',
            'numero_motor' => 'MOTOR0001',
            'costo_compra' => 1500,
            'precio_venta' => 2200,
            'fecha_ingreso' => Carbon::now()->toDateString(),
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
        ]);
        $unidad2 = MotoUnidad::create([
            'moto_id' => $moto->id,
            'vin' => 'VINTEST0002',
            'numero_motor' => 'MOTOR0002',
            'costo_compra' => 1500,
            'precio_venta' => 2200,
            'fecha_ingreso' => Carbon::now()->toDateString(),
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
        ]);

        $c1 = Cliente::createWithUser([
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Ana',
            'apellido' => 'Perez',
            'documento' => '33333333',
            'tipo_documento' => 'CI',
            'telefono' => '04120000003',
            'email' => null,
            'activo' => true,
        ]);
        $u1 = User::where('cliente_id', $c1->id)->first();

        $c2 = Cliente::createWithUser([
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Bruno',
            'apellido' => 'Gomez',
            'documento' => '44444444',
            'tipo_documento' => 'CI',
            'telefono' => '04120000004',
            'email' => null,
            'activo' => true,
        ]);
        $u2 = User::where('cliente_id', $c2->id)->first();

        $contrato1 = Contrato::create([
            'numero_contrato' => 'CNT-001',
            'cliente_id' => $c1->id,
            'moto_unidad_id' => $unidad1->id,
            'vendedor_id' => null,
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'fecha_inicio' => Carbon::now()->toDateString(),
            'fecha_fin_estimada' => Carbon::now()->addMonths(12)->toDateString(),
            'precio_venta_final' => 2200,
            'cuota_inicial' => 200,
            'monto_financiado' => 2000,
            'tasa_interes_anual' => 12,
            'plazo_meses' => 12,
            'dia_pago_mensual' => 5,
            'estado' => 'activo',
            'saldo_pendiente' => 2000,
            'cuotas_pagadas' => 0,
            'cuotas_totales' => 12,
            'cuotas_vencidas' => 0,
        ]);

        $contrato2 = Contrato::create([
            'numero_contrato' => 'CNT-002',
            'cliente_id' => $c2->id,
            'moto_unidad_id' => $unidad2->id,
            'vendedor_id' => null,
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'fecha_inicio' => Carbon::now()->toDateString(),
            'fecha_fin_estimada' => Carbon::now()->addMonths(12)->toDateString(),
            'precio_venta_final' => 2200,
            'cuota_inicial' => 200,
            'monto_financiado' => 2000,
            'tasa_interes_anual' => 12,
            'plazo_meses' => 12,
            'dia_pago_mensual' => 5,
            'estado' => 'activo',
            'saldo_pendiente' => 2000,
            'cuotas_pagadas' => 0,
            'cuotas_totales' => 12,
            'cuotas_vencidas' => 0,
        ]);

        $pago1 = Pago::create([
            'tipo_pago' => 'recibo',
            'fecha' => Carbon::now()->toDateString(),
            'cliente_id' => $c1->id,
            'caja_id' => null,
            'user_id' => $u1->id,
            'subtotal' => 100,
            'descuento' => 0,
            'total' => 100,
            'estado' => 'aprobado',
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
        ]);
        $pago2 = Pago::create([
            'tipo_pago' => 'recibo',
            'fecha' => Carbon::now()->toDateString(),
            'cliente_id' => $c2->id,
            'caja_id' => null,
            'user_id' => $u2->id,
            'subtotal' => 150,
            'descuento' => 0,
            'total' => 150,
            'estado' => 'aprobado',
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
        ]);

        $this->actingAs($u1);

        $contractsVisibleToU1 = Contrato::query()
            ->when(auth()->user()->cliente_id, fn($q) => $q->where('cliente_id', auth()->user()->cliente_id))
            ->pluck('id')
            ->toArray();
        $this->assertContains($contrato1->id, $contractsVisibleToU1);
        $this->assertNotContains($contrato2->id, $contractsVisibleToU1);

        $pagosVisibleToU1 = Pago::query()
            ->when(auth()->user()->cliente_id, fn($q) => $q->where('cliente_id', auth()->user()->cliente_id))
            ->pluck('id')
            ->toArray();
        $this->assertContains($pago1->id, $pagosVisibleToU1);
        $this->assertNotContains($pago2->id, $pagosVisibleToU1);
    }
}

