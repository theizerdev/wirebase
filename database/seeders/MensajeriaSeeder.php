<?php

namespace Database\Seeders;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MensajeriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::whereNotNull('empresa_id')
                        ->whereNotNull('sucursal_id')
                        ->get();

        if ($usuarios->count() < 2) {
            $this->command->info('Se necesitan al menos 2 usuarios con empresa y sucursal para crear mensajes de prueba.');
            return;
        }

        $asuntos = [
            'Reunión de padres este viernes',
            'Cambio de horario de clases',
            'Entrega de calificaciones',
            'Recordatorio de pago de matrícula',
            'Suspensión de clases por mantenimiento',
            'Nuevo horario de atención administrativa',
            'Convocatoria a reunión de docentes',
            'Actualización del sistema de notas',
            'Entrega de uniformes escolares',
            'Cambio de aulas temporales',
        ];

        $contenidos = [
            'Estimados padres de familia, les recordamos que este viernes a las 3:00 PM tendremos la reunión mensual de padres. Por favor confirmar asistencia.',
            'Se informa a todos los estudiantes que el horario de clases ha sido modificado temporalmente. Los nuevos horarios están disponibles en la plataforma.',
            'Las calificaciones del período actual estarán disponibles a partir del próximo lunes en el portal de estudiantes.',
            'Les recordamos que el pago de matrícula debe realizarse antes del 15 de cada mes para evitar recargos.',
            'Debido a labores de mantenimiento en las instalaciones, las clases quedan suspendidas el día jueves.',
            'El horario de atención en la oficina administrativa cambia a partir de la próxima semana: lunes a viernes de 8:00 AM a 5:00 PM.',
            'Se convoca a todos los docentes a la reunión programada para este miércoles a las 2:00 PM en el auditorio principal.',
            'El sistema de registro de notas ha sido actualizado. Les pedimos actualizar sus contraseñas para acceder al nuevo sistema.',
            'La entrega de uniformes escolares se realizará en la cancha cubierta el próximo martes a partir de las 9:00 AM.',
            'Temporalmente las clases se trasladarán a las aulas del edificio B mientras se realizan reparaciones en el edificio principal.',
        ];

        // Crear mensajes de prueba
        for ($i = 0; $i < 20; $i++) {
            $remitente = $usuarios->random();
            $cantidadDestinatarios = min(rand(1, 3), $usuarios->where('id', '!=', $remitente->id)->count());
            $destinatarios = $usuarios->where('id', '!=', $remitente->id)->random($cantidadDestinatarios);
            
            $mensaje = Mensaje::create([
                'asunto' => $asuntos[array_rand($asuntos)],
                'contenido' => $contenidos[array_rand($contenidos)],
                'prioridad' => collect(['baja', 'media', 'alta', 'urgente'])->random(),
                'remitente_id' => $remitente->id,
                'empresa_id' => $remitente->empresa_id,
                'sucursal_id' => $remitente->sucursal_id,
            ]);

            // Adjuntar destinatarios
            foreach ($destinatarios as $destinatario) {
                DB::table('mensaje_destinatarios')->insert([
                    'mensaje_id' => $mensaje->id,
                    'user_id' => $destinatario->id,
                    'leido' => rand(0, 1),
                    'archivado' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Se han creado 20 mensajes de prueba exitosamente.');
    }
}