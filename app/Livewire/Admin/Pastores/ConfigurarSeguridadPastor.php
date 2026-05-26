<?php

namespace App\Livewire\Admin\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Pastor;
use App\Models\PreguntasSeguridadPastor;
use Illuminate\Support\Facades\Auth;

class ConfigurarSeguridadPastor extends Component
{
    use HasDynamicLayout;

    public Pastor $pastor;
    public $preguntasSeguridad = [];
    public $backupCodes = [];
    public $mostrarBackupCodes = false;
    public $step = 1; // 1: preguntas, 2: backup codes, 3: completado
    public $error = '';
    public $success = '';

    // Preguntas predefinidas
    public $preguntasDisponibles = [
        '¿Cuál es el nombre de tu madre?',
        '¿Cuál es el nombre de tu padre?',
        '¿En qué ciudad naciste?',
        '¿Cuál es el nombre de tu primera iglesia?',
        '¿Cuál es tu fecha de bautismo?',
        '¿Cuál es el nombre de tu cónyuge?',
        '¿Cuál es tu animal favorito?',
        '¿Cuál fue tu primer ministerio?',
        '¿Cuál es el nombre de tu mejor amigo de la infancia?',
        '¿En qué año fuiste ordenado pastor?',
    ];

    protected function rules()
    {
        return [
            'preguntasSeguridad.*.pregunta' => 'required|string',
            'preguntasSeguridad.*.respuesta' => 'required|string|min:3',
        ];
    }

    public function mount(Pastor $pastor)
    {
        $this->pastor = $pastor;
        
        // Inicializar 3 preguntas vacías
        if (empty($this->preguntasSeguridad)) {
            for ($i = 0; $i < 3; $i++) {
                $this->preguntasSeguridad[] = [
                    'pregunta' => '',
                    'respuesta' => '',
                ];
            }
        }
    }

    /**
     * Guardar preguntas de seguridad
     */
    public function guardarPreguntas()
    {
        $this->validate();

        try {
            // Verificar que todas las preguntas sean diferentes
            $preguntasUnicas = collect($this->preguntasSeguridad)->pluck('pregunta')->unique();
            if ($preguntasUnicas->count() < 3) {
                $this->error = 'Las preguntas deben ser diferentes entre sí.';
                return;
            }

            // Crear o actualizar registro
            $registro = PreguntasSeguridadPastor::firstOrCreate(
                ['pastor_id' => $this->pastor->id],
                ['preguntas' => [], 'backup_codes' => [], 'activado' => false]
            );

            // Guardar preguntas (hasheadas automáticamente)
            $registro->guardarPreguntas($this->preguntasSeguridad);

            // Generar backup codes
            $this->backupCodes = $registro->generarBackupCodes();
            
            $this->step = 2;
            $this->success = 'Preguntas guardadas correctamente. Ahora genere sus códigos de respaldo.';

        } catch (\Exception $e) {
            $this->error = 'Error al guardar: ' . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Error guardando preguntas de seguridad', [
                'pastor_id' => $this->pastor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Activar configuración de seguridad
     */
    public function activarSeguridad()
    {
        try {
            $registro = PreguntasSeguridadPastor::where('pastor_id', $this->pastor->id)->first();
            
            if ($registro) {
                $registro->activar();
                
                // Marcar solicitud como completada si existe
                $solicitudPendiente = $this->pastor->solicitudesModificacion()
                    ->where('estado', 'aprobado')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($solicitudPendiente) {
                    app(\App\Services\PastorAuthorizationService::class)
                        ->completarSolicitud($solicitudPendiente);
                }

                $this->step = 3;
                $this->success = '✅ Configuración de seguridad activada exitosamente.';
            }

        } catch (\Exception $e) {
            $this->error = 'Error al activar: ' . $e->getMessage();
        }
    }

    /**
     * Regenerar backup codes
     */
    public function regenerarBackupCodes()
    {
        try {
            $registro = PreguntasSeguridadPastor::where('pastor_id', $this->pastor->id)->first();
            
            if ($registro) {
                $this->backupCodes = $registro->generarBackupCodes();
                $this->mostrarBackupCodes = true;
            }

        } catch (\Exception $e) {
            $this->error = 'Error al regenerar códigos: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $pastor = \App\Models\Pastor::find($this->pastor->id);
        return view('livewire.admin.pastores.configurar-seguridad-pastor',compact('pastor'))
            ->layout('components.layouts.auth-basic');
    }
}
