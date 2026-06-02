<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariosDistributionSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $beneficiarios;

    public function __construct(Collection $beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    }

    public function view(): View
    {
        $ageGroups = [
            '0-12' => 0,
            '13-18' => 0,
            '19-35' => 0,
            '36-60' => 0,
            '60+' => 0,
        ];

        foreach ($this->beneficiarios as $beneficiario) {
            if ($beneficiario->edad === null) {
                continue;
            }
            if ($beneficiario->edad <= 12) {
                $ageGroups['0-12']++;
            } elseif ($beneficiario->edad <= 18) {
                $ageGroups['13-18']++;
            } elseif ($beneficiario->edad <= 35) {
                $ageGroups['19-35']++;
            } elseif ($beneficiario->edad <= 60) {
                $ageGroups['36-60']++;
            } else {
                $ageGroups['60+']++;
            }
        }

        $civilStatuses = $this->beneficiarios->groupBy('estado_civil')->map->count()->sortDesc();
        $educationLevels = $this->beneficiarios->groupBy('nivel_instruccion')->map->count()->sortDesc();
        $workStatus = [
            'Trabajan' => $this->beneficiarios->where('trabaja_actualmente', true)->count(),
            'No trabajan' => $this->beneficiarios->where('trabaja_actualmente', false)->count(),
        ];
        $studyStatus = [
            'Estudian' => $this->beneficiarios->where('estudia_actualmente', true)->count(),
            'No estudian' => $this->beneficiarios->where('estudia_actualmente', false)->count(),
        ];

        $ageCounts = [
            'niños' => $this->beneficiarios->filter(function ($item) {
                return $item->edad !== null && $item->edad <= 12;
            })->count(),
            'mayores' => $this->beneficiarios->filter(function ($item) {
                return $item->edad !== null && $item->edad >= 60;
            })->count(),
        ];

        $getEstadoNombre = function ($item) {
            return $item->estado?->nombre
                ?: $item->responsable?->estado?->nombre
                ?: 'Sin estado';
        };

        $casaAlimentacionByEstado = $this->beneficiarios
            ->filter(function ($item) {
                return filled($item->responsable?->codigo_casa_alimentacion);
            })
            ->groupBy($getEstadoNombre)
            ->map->count()
            ->sortDesc();

        $madresCasaAlimentacionByEstado = $this->beneficiarios
            ->filter(function ($item) {
                return filled($item->responsable?->codigo_casa_alimentacion)
                    && $item->es_mujer_embarazada;
            })
            ->groupBy($getEstadoNombre)
            ->map->count()
            ->sortDesc();

        return view('exports.beneficiarios_distribution', [
            'ageGroups' => $ageGroups,
            'civilStatuses' => $civilStatuses,
            'educationLevels' => $educationLevels,
            'workStatus' => $workStatus,
            'studyStatus' => $studyStatus,
            'ageCounts' => $ageCounts,
            'casaAlimentacionByEstado' => $casaAlimentacionByEstado,
            'madresCasaAlimentacionByEstado' => $madresCasaAlimentacionByEstado,
        ]);
    }

    public function title(): string
    {
        return 'Distribución';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
