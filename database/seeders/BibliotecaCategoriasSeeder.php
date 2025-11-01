<?php

namespace Database\Seeders;

use App\Models\BibliotecaCategoria;
use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibliotecaCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();
        
        $categorias = [
            [
                'nombre' => 'Documentos Administrativos',
                'descripcion' => 'Documentos de gestión administrativa',
                'color' => '#3498db',
                'icon' => 'fas fa-file-alt',
            ],
            [
                'nombre' => 'Manuales y Tutoriales',
                'descripcion' => 'Manuales de usuario y tutoriales',
                'color' => '#2ecc71',
                'icon' => 'fas fa-book',
            ],
            [
                'nombre' => 'Informes y Reportes',
                'descripcion' => 'Informes periódicos y reportes institucionales',
                'color' => '#e74c3c',
                'icon' => 'fas fa-chart-bar',
            ],
            [
                'nombre' => 'Recursos Educativos',
                'descripcion' => 'Materiales educativos y recursos académicos',
                'color' => '#9b59b6',
                'icon' => 'fas fa-graduation-cap',
            ],
            [
                'nombre' => 'Políticas y Normativas',
                'descripcion' => 'Políticas internas y normativas institucionales',
                'color' => '#f39c12',
                'icon' => 'fas fa-gavel',
            ],
            [
                'nombre' => 'Plantillas y Formatos',
                'descripcion' => 'Plantillas y formatos oficiales',
                'color' => '#1abc9c',
                'icon' => 'fas fa-file-invoice',
            ],
        ];

        foreach ($empresas as $empresa) {
            foreach ($categorias as $categoria) {
                BibliotecaCategoria::create([
                    'nombre' => $categoria['nombre'],
                    'descripcion' => $categoria['descripcion'],
                    'color' => $categoria['color'],
                    'icono' => $categoria['icon'],
                    'empresa_id' => $empresa->id,
                    'sucursal_id' => $empresa->sucursales->first()->id ?? null,
                    'activo' => true,
                ]);
            }
        }
    }
}