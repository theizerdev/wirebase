<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariosSummarySheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $beneficiarios;

    public function __construct(Collection $beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    }

    public function view(): View
    {
        $sinResponsable = $this->beneficiarios->whereNull('responsable_id')->count();
        $sinTelefono = $this->beneficiarios->filter(function ($item) {
            return empty($item->telefono_principal);
        })->count();
        $sinCedula = $this->beneficiarios->filter(function ($item) {
            return empty($item->cedula);
        })->count();

        $summary = [
            'Total beneficiarios' => $this->beneficiarios->count(),
            'Con responsable' => $this->beneficiarios->whereNotNull('responsable_id')->count(),
            'Con discapacidad' => $this->beneficiarios->where('padece_discapacidad_enfermedad', true)->count(),
            'Embarazadas' => $this->beneficiarios->where('es_mujer_embarazada', true)->count(),
            'Promedio de edad' => round($this->beneficiarios->avg('edad'), 1),
            'Registrados últimos 7 días' => $this->beneficiarios->filter(function ($item) {
                return $item->created_at && $item->created_at->greaterThanOrEqualTo(now()->subDays(7));
            })->count(),
            'Sin teléfono principal' => $sinTelefono,
            'Sin cédula' => $sinCedula,
            'Sin responsable asignado' => $sinResponsable,
        ];

        return view('exports.beneficiarios_summary', [
            'summary' => $summary,
        ]);
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
