<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Moto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MotosDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_auth()
    {
        $moto = Moto::factory()->create();
        $response = $this->get(route('admin.motos.details', ['moto' => $moto->id]));
        $response->assertStatus(302);
    }
}
