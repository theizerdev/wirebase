<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pastor;
use App\Services\PlanillaService;
use Codedge\Fpdf\Facades\Fpdf;
use Illuminate\Http\Response;

class PastorPlanillaController extends Controller
{
    protected $planillaService;

    public function __construct(PlanillaService $planillaService)
    {
        $this->planillaService = $planillaService;
    }

    public function planilla($id)
    {
        $pastor = Pastor::with(['iglesias', 'ciudad', 'estado', 'municipio', 'parroquia', 'conyuge'])->findOrFail($id);

        $fpdf = new \Codedge\Fpdf\Fpdf\Fpdf();
        $this->planillaService->generarPdfParaPastor($pastor, $fpdf);

        // Salida del PDF
        $nombre_archivo = 'planilla_pastor_' . str_replace(' ', '_', utf8_decode($pastor->nombres) . '_' . utf8_decode($pastor->apellidos)) . '.pdf';
        $fpdf->Output('I', $nombre_archivo);

        // Terminar la ejecución para evitar errores de headers
        exit;
    }
}
