<?php

/**
 * Configuración del sistema de contabilidad para iglesias
 *
 * Mapeo de tipos de transacciones a códigos de cuentas contables
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cuentas Contables por Tipo de Transacción
    |--------------------------------------------------------------------------
    |
    | Estos códigos deben coincidir con las cuentas creadas en el seeder
    | PlanDeCuentasIglesiaSeeder
    |
    */

    'cuentas' => [
        // INGRESOS
        'diezmos' => '4.1.01',
        'ofrendas' => '4.1.02',
        'aportes_especiales' => '4.1.03',
        'otros_ingresos' => '4.1.05',

        // EGRESOS/GASTOS
        'gastos_operativos' => '5.1.01',
        'gastos_ministerio' => '5.1.02',
        'honorarios_pastorales' => '5.1.03',
        'otros_gastos' => '5.1.04',

        // ACTIVOS
        'caja_general' => '1.1.01',
        'caja_chica' => '1.1.02',
        'banco_cuenta_corriente' => '1.1.03',
        'banco_cuenta_ahorros' => '1.1.04',

        // PASIVOS
        'cuentas_por_pagar' => '2.1.01',
        'obligaciones_laborales' => '2.1.02',

        // PATRIMONIO
        'patrimonio_inicial' => '3.1.01',
        'resultado_ejercicio' => '3.2.01',
        'resultados_acumulados' => '3.2.02',
    ],

    /*
    |--------------------------------------------------------------------------
    | Métodos de Pago y sus Cuentas Asociadas
    |--------------------------------------------------------------------------
    */

    'metodos_pago' => [
        'efectivo' => '1.1.01',           // Caja General
        'transferencia' => '1.1.03',      // Banco - Cuenta Corriente
        'punto_venta' => '1.1.03',        // Banco - Cuenta Corriente
        'cheque' => '1.1.03',             // Banco - Cuenta Corriente
        'otro' => '1.1.01',               // Caja General (default)
    ],

    /*
    |--------------------------------------------------------------------------
    | Categorías de Gastos Operativos
    |--------------------------------------------------------------------------
    */

    'categorias_gastos_operativos' => [
        'servicios_publicos' => '5.1.01.001',    // Electricidad, agua, teléfono, internet
        'mantenimiento' => '5.1.01.002',         // Reparaciones y mantenimiento
        'alquiler' => '5.1.01.003',              // Alquiler del local
        'limpieza' => '5.1.01.004',              // Artículos de limpieza
        'papeleria' => '5.1.01.005',             // Papelería y útiles
        'seguros' => '5.1.01.006',               // Seguros
        'impuestos' => '5.1.01.007',             // Impuestos y tasas municipales
    ],

    /*
    |--------------------------------------------------------------------------
    | Categorías de Gastos de Ministerio
    |--------------------------------------------------------------------------
    */

    'categorias_gastos_ministerio' => [
        'material_educativo' => '5.1.02.001',    // Libros, materiales de estudio
        'eventos_evangelisticos' => '5.1.02.002',// Campañas, cruzadas
        'misiones' => '5.1.02.003',              // Apoyo misionero
        'obra_social' => '5.1.02.004',           // Ayuda comunitaria
        'musica_alabanza' => '5.1.02.005',       // Equipos de sonido, instrumentos
        'decoracion' => '5.1.02.006',            // Decoración del templo
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración General
    |--------------------------------------------------------------------------
    */

    // Moneda por defecto
    'moneda_default' => 'VES',

    // Monedas soportadas
    'monedas_soportadas' => ['VES', 'USD'],

    // Tasa de cambio por defecto (si no se puede obtener del API)
    'tasa_cambio_default' => 1.0,

    // Prefijo para números de comprobante
    'prefijo_comprobante' => 'TF',

    // Longitud del correlativo de comprobantes
    'longitud_correlativo' => 6,

];
