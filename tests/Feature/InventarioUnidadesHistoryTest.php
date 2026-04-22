<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MotoUnidad;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventarioUnidadesHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_auth_and_permission()
    {
        $unidad = MotoUnidad::factory()->create();
        $response = $this->get(route('admin.inventario.unidades.history', ['unidad' => $unidad->id]));
        $response->assertStatus(302); // redirect to login
    }
}
