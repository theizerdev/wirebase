<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolesAndPermissionsSeeder;

class ClienteUserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_user_with_role_on_cliente_creation(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $empresa = Empresa::create([
            'razon_social' => 'Empresa Prueba',
            'documento' => 'J123456789',
        ]);

        $cliente = Cliente::createWithUser([
            'empresa_id' => $empresa->id,
            'nombre' => 'Theizer',
            'apellido' => 'Gonzalez',
            'documento' => '12345678',
            'tipo_documento' => 'CI',
            'telefono' => '04121231234',
            'email' => null,
            'activo' => true,
        ]);

        $user = User::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($user);
        $this->assertEquals('tgonzalez', $user->username);
        $this->assertTrue(Hash::check('12345678', $user->password));
        $this->assertTrue($user->hasRole('Cliente'));
    }

    public function test_generates_unique_username_with_incremental_suffix(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $empresa = Empresa::create([
            'razon_social' => 'Empresa Prueba',
            'documento' => 'J123456789',
        ]);

        $c1 = Cliente::createWithUser([
            'empresa_id' => $empresa->id,
            'nombre' => 'Theizer',
            'apellido' => 'Gonzalez',
            'documento' => '11111111',
            'tipo_documento' => 'CI',
            'telefono' => '04120000001',
            'email' => null,
            'activo' => true,
        ]);
        $u1 = User::where('cliente_id', $c1->id)->first();
        $this->assertEquals('tgonzalez', $u1->username);

        $c2 = Cliente::createWithUser([
            'empresa_id' => $empresa->id,
            'nombre' => 'Tony',
            'apellido' => 'Gonzalez',
            'documento' => '22222222',
            'tipo_documento' => 'CI',
            'telefono' => '04120000002',
            'email' => null,
            'activo' => true,
        ]);
        $u2 = User::where('cliente_id', $c2->id)->first();
        $this->assertEquals('tgonzalez1', $u2->username);
    }
}

