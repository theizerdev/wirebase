<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Empresa;
use App\Models\Pais;
use App\Services\WhatsAppService;

class PhoneFormattingTest extends TestCase
{
    use RefreshDatabase;

    public function test_formats_venezuelan_number_with_leading_zero()
    {
        $pais = Pais::create([
            'nombre' => 'Venezuela',
            'codigo_iso2' => 'VE',
            'codigo_iso3' => 'VEN',
            'codigo_telefonico' => '+58',
            'moneda_principal' => 'VES',
            'idioma_principal' => 'es',
            'continente' => 'América del Sur',
            'activo' => true,
        ]);

        $empresa = Empresa::create([
            'razon_social' => 'Empresa VE',
            'documento' => 'J123456789',
            'pais_id' => $pais->id,
            'status' => true,
        ]);

        $wa = WhatsAppService::forCompany($empresa);

        $this->assertEquals('584241703465', $wa->formatPhone('04241703465'));
        $this->assertEquals('584241703465', $wa->formatPhone('+58 424 170 3465'));
        $this->assertEquals('584241703465', $wa->formatPhone('00584241703465'));
    }
}

