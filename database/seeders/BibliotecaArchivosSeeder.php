<?php

namespace Database\Seeders;

use App\Models\BibliotecaArchivo;
use App\Models\BibliotecaCategoria;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BibliotecaArchivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::whereNotNull('empresa_id')
                        ->whereNotNull('sucursal_id')
                        ->get();
        $categorias = BibliotecaCategoria::all();
        
        $archivos = [
            [
                'titulo' => 'Manual de Usuario del Sistema',
                'descripcion' => 'Manual completo de uso del sistema educativo',
                'nombre_archivo' => 'manual_usuario.pdf',
                'ruta_archivo' => 'biblioteca/manual_usuario.pdf',
                'tamaño' => 2048000,
                'tipo_mime' => 'application/pdf',
                'visibilidad' => 'publico',
                'etiquetas' => 'manual,usuario,sistema',
            ],
            [
                'titulo' => 'Políticas de Privacidad',
                'descripcion' => 'Documento de políticas de privacidad y protección de datos',
                'nombre_archivo' => 'politicas_privacidad.pdf',
                'ruta_archivo' => 'biblioteca/politicas_privacidad.pdf',
                'tamaño' => 1024000,
                'tipo_mime' => 'application/pdf',
                'visibilidad' => 'publico',
                'etiquetas' => 'políticas,privacidad,legal',
            ],
            [
                'titulo' => 'Formato de Matrícula 2024',
                'descripcion' => 'Formato oficial de matrícula para el año escolar 2024',
                'nombre_archivo' => 'formato_matricula_2024.docx',
                'ruta_archivo' => 'biblioteca/formato_matricula_2024.docx',
                'tamaño' => 512000,
                'tipo_mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'visibilidad' => 'privado',
                'etiquetas' => 'formato,matrícula,2024',
            ],
            [
                'titulo' => 'Reporte Anual de Gestión',
                'descripcion' => 'Reporte anual de gestión institucional 2023',
                'nombre_archivo' => 'reporte_gestion_2023.pdf',
                'ruta_archivo' => 'biblioteca/reporte_gestion_2023.pdf',
                'tamaño' => 4096000,
                'tipo_mime' => 'application/pdf',
                'visibilidad' => 'publico',
                'etiquetas' => 'reporte,gestión,2023,anual',
            ],
        ];

        // Crear archivos físicos de prueba
        foreach ($archivos as $archivo) {
            $contenido = "Contenido de prueba para: {$archivo['titulo']}";
            Storage::disk('local')->put($archivo['ruta_archivo'], $contenido);
        }

        // Crear registros en base de datos
        foreach ($usuarios as $usuario) {
            foreach ($archivos as $archivo) {
                BibliotecaArchivo::create([
                    'titulo' => $archivo['titulo'],
                    'descripcion' => $archivo['descripcion'],
                    'nombre_archivo' => $archivo['nombre_archivo'],
                    'ruta_archivo' => $archivo['ruta_archivo'],
                    'tamaño' => $archivo['tamaño'],
                    'tipo_mime' => $archivo['tipo_mime'],
                    'categoria_id' => $categorias->random()->id,
                    'usuario_subida_id' => $usuario->id,
                    'visibilidad' => $archivo['visibilidad'],
                    'etiquetas' => $archivo['etiquetas'],
                    'empresa_id' => $usuario->empresa_id,
                    'sucursal_id' => $usuario->sucursal_id,
                ]);
            }
        }
    }
}