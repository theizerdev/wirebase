<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Services\WhatsAppApiIntegrationService;

class EmpresaObserver
{
    protected $whatsappService;

    public function __construct(WhatsAppApiIntegrationService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function created(Empresa $empresa)
    {
        // Sincronizar con WhatsApp API cuando se crea una empresa
        $this->whatsappService->createCompany($empresa);
    }

    public function updated(Empresa $empresa)
    {
        // Sincronizar cambios si es necesario
        if ($empresa->isDirty(['nombre'])) {
            $this->whatsappService->updateCompany($empresa);
        }
    }

    public function deleted(Empresa $empresa)
    {
        // Limpiar datos en WhatsApp API si es necesario
        $this->whatsappService->deleteCompany($empresa);
    }
}