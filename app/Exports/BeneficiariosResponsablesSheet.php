<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariosResponsablesSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $beneficiarios;

    public function __construct(Collection $beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    }

    public function view(): View
    {
        $responsables = $this->beneficiarios
            ->groupBy(function ($item) {
                return $item->responsable?->nombre_completo ?? 'Sin responsable';
            })
            ->map->count()
            ->sortDesc();

        return view('exports.beneficiarios_responsables', [
            'responsables' => $responsables,
        ]);
    }

    public function title(): string
    {
        return 'Responsables';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
