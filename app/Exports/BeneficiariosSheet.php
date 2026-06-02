<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariosSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $beneficiarios;

    public function __construct(Collection $beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    }

    public function view(): View
    {
        return view('exports.beneficiarios', [
            'beneficiarios' => $this->beneficiarios,
        ]);
    }

    public function title(): string
    {
        return 'Beneficiarios';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
