<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SucursalSeeder extends Seeder
{
    public function run()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            $sucursal = Sucursal::create([
                'empresa_id' => $empresa->id,
                'nombre' => 'Sucursal ' . $empresa->razon_social,
                'direccion' => $empresa->direccion,
                'telefono' => $empresa->telefono,
            ]);

          
        }
    }
}
