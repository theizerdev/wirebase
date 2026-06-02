<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BeneficiariosExport implements WithMultipleSheets
{
    protected Collection $beneficiarios;

    public function __construct(Collection $beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    }

    public function sheets(): array
    {
        return [
            new BeneficiariosSheet($this->beneficiarios),
            new BeneficiariosSummarySheet($this->beneficiarios),
            new BeneficiariosDistributionSheet($this->beneficiarios),
            new BeneficiariosResponsablesSheet($this->beneficiarios),
        ];
    }
}
