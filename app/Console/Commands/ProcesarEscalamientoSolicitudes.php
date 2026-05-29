<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SolicitudWorkflowService;

class ProcesarEscalamientoSolicitudes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solicitudes:procesar-escalamiento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa el escalamiento automático de solicitudes pendientes';

    /**
     * Execute the console command.
     */
    public function handle(SolicitudWorkflowService $workflowService)
    {
        $this->info('Iniciando procesamiento de escalamiento automático...');
        
        try {
            $workflowService->procesarEscalamientoAutomatico();
            
            $this->info('✅ Escalamiento automático procesado correctamente.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error al procesar escalamiento: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
