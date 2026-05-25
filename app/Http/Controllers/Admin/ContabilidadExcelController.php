<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentaContable;
use App\Models\AsientoContable;
use App\Models\AsientoDetalle;
use App\Models\Empresa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ContabilidadExcelController extends Controller
{
    private function setupHeader(Spreadsheet $spreadsheet, string $titulo, ?string $periodo = null): int
    {
        $sheet = $spreadsheet->getActiveSheet();
        $empresa = Empresa::find(auth()->user()->empresa_id);

        $sheet->setCellValue('A1', $empresa->razon_social ?? 'Empresa');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        if ($empresa->rif_fiscal) {
            $sheet->setCellValue('A2', 'RIF: ' . $empresa->rif_fiscal);
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row = 3;
        }

        $sheet->setCellValue('A' . $row, $titulo);
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        if ($periodo) {
            $sheet->setCellValue('A' . $row, $periodo);
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        return $row + 1;
    }

    private function styleHeaderRow($sheet, string $range)
    {
        $sheet->getStyle($range)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('333333');
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function styleTotalsRow($sheet, string $range)
    {
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    // ================================================
    // LIBRO DIARIO
    // ================================================
    public function libroDiario()
    {
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->endOfMonth()->format('Y-m-d'));
        $empresaId = auth()->user()->empresa_id;
        $iglesiaId = request('iglesia_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Libro Diario');
        
        $empresa = Empresa::find($empresaId);
        $iglesia = $iglesiaId ? \App\Models\Iglesia::find($iglesiaId) : null;
        $pastor = $iglesia ? $iglesia->pastor : null;

        // Row 1-3: Logo + Empresa info
        $row = 1;
        
        // Logo (column A)
        $logoPath = null;
        if ($empresa && $empresa->logo) {
            $logoPath = public_path($empresa->logo);
            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo de la empresa');
                $drawing->setPath($logoPath);
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension(1)->setRowHeight(65);
            }
        }
        
        // Company name (column B onwards)
        $startCol = $logoPath ? 'B' : 'A';
        $endCol = 'H';
        
        $sheet->setCellValue($startCol . '1', $empresa->razon_social ?? 'Empresa');
        $sheet->mergeCells($startCol . '1:' . $endCol . '1');
        $sheet->getStyle($startCol . '1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle($startCol . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        if ($empresa->rif_fiscal) {
            $sheet->setCellValue($startCol . '2', 'RIF: ' . $empresa->rif_fiscal);
            $sheet->mergeCells($startCol . '2:' . $endCol . '2');
            $sheet->getStyle($startCol . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row = 3;
        }

        // Iglesia info
        if ($iglesia) {
            $sheet->setCellValue($startCol . $row, 'EXTENSIÓN: ' . strtoupper($iglesia->nombre));
            $sheet->mergeCells($startCol . $row . ':' . $endCol . $row);
            $sheet->getStyle($startCol . $row)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle($startCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            
            if ($iglesia->direccion) {
                $sheet->setCellValue($startCol . $row, 'Dirección: ' . $iglesia->direccion);
                $sheet->mergeCells($startCol . $row . ':' . $endCol . $row);
                $sheet->getStyle($startCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            
            if ($pastor) {
                $pastorInfo = 'Pastor: ' . ($pastor->nombres ?? '') . ' ' . ($pastor->apellidos ?? '');
                if ($pastor->telefono_tlf) {
                    $pastorInfo .= ' | Tel: ' . $pastor->telefono_tlf;
                }
                $sheet->setCellValue($startCol . $row, $pastorInfo);
                $sheet->mergeCells($startCol . $row . ':' . $endCol . $row);
                $sheet->getStyle($startCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            
            $row++; // Extra spacing
        }

        // Title
        $sheet->setCellValue($startCol . $row, 'LIBRO DIARIO');
        $sheet->mergeCells($startCol . $row . ':' . $endCol . $row);
        $sheet->getStyle($startCol . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($startCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Period
        $sheet->setCellValue($startCol . $row, "Desde: {$desde}  Hasta: {$hasta}");
        $sheet->mergeCells($startCol . $row . ':' . $endCol . $row);
        $sheet->getStyle($startCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        
        $row++; // Extra spacing

        $asientos = AsientoContable::with(['detalles.cuenta', 'user', 'iglesia'])
            ->where('empresa_id', $empresaId)
            ->when($iglesiaId, fn($q) => $q->where('iglesia_id', $iglesiaId))
            ->where('estado', 'aprobado')
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('numero')
            ->get();

        $grandTotalDebe = 0;
        $grandTotalHaber = 0;

        foreach ($asientos as $asiento) {
            // Asiento header row
            $sheet->setCellValue('A' . $row, $asiento->numero);
            $sheet->setCellValue('B' . $row, $asiento->fecha->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, ucfirst($asiento->tipo));
            $sheet->setCellValue('D' . $row, $asiento->descripcion);
            $sheet->mergeCells('D' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8EDF5');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;

            // Detail header
            $sheet->setCellValue('A' . $row, 'Código');
            $sheet->setCellValue('B' . $row, 'Cuenta');
            $sheet->setCellValue('C' . $row, 'Descripción');
            $sheet->mergeCells('C' . $row . ':D' . $row);
            $sheet->setCellValue('E' . $row, 'Debe');
            $sheet->setCellValue('F' . $row, 'Haber');
            $this->styleHeaderRow($sheet, 'A' . $row . ':F' . $row);
            $row++;

            $asientoDebe = 0;
            $asientoHaber = 0;

            foreach ($asiento->detalles as $det) {
                $d = (float) $det->debe;
                $h = (float) $det->haber;
                $asientoDebe += $d;
                $asientoHaber += $h;

                $sheet->setCellValue('A' . $row, $det->cuenta->codigo ?? '');
                $sheet->setCellValue('B' . $row, $det->cuenta->nombre ?? '');
                $sheet->setCellValue('C' . $row, $det->descripcion ?? '');
                $sheet->mergeCells('C' . $row . ':D' . $row);
                $sheet->setCellValue('E' . $row, $d > 0 ? $d : '');
                $sheet->setCellValue('F' . $row, $h > 0 ? $h : '');
                $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $row++;
            }

            // Asiento totals
            $sheet->setCellValue('D' . $row, 'Totales asiento:');
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, $asientoDebe);
            $sheet->setCellValue('F' . $row, $asientoHaber);
            $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $this->styleTotalsRow($sheet, 'A' . $row . ':F' . $row);
            $row++;

            $grandTotalDebe += $asientoDebe;
            $grandTotalHaber += $asientoHaber;
            $row++; // blank separator
        }

        // Grand totals
        $sheet->setCellValue('D' . $row, 'TOTALES GENERALES (' . $asientos->count() . ' asientos):');
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, $grandTotalDebe);
        $sheet->setCellValue('F' . $row, $grandTotalHaber);
        $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('333333');
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Generado el: ' . now()->format('d/m/Y H:i:s'));

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);

        $writer = new Xlsx($spreadsheet);
        $filename = 'libro_diario';
        if ($iglesia) {
            $filename .= '_' . strtolower(str_replace(' ', '_', preg_replace('/[^\p{L}\p{N}\s]/u', '', $iglesia->nombre)));
        }
        $filename .= '_' . str_replace('-', '', $desde) . '_' . str_replace('-', '', $hasta) . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    // ================================================
    // LIBRO MAYOR
    // ================================================
    public function libroMayor()
    {
        $cuentaId = request('cuenta_id');
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->endOfMonth()->format('Y-m-d'));
        $empresaId = auth()->user()->empresa_id;
        $iglesiaId = request('iglesia_id');

        if (!$cuentaId) {
            abort(400, 'Debe seleccionar una cuenta');
        }

        $cuenta = CuentaContable::findOrFail($cuentaId);
        $empresa = Empresa::find($empresaId);
        $iglesia = $iglesiaId ? \App\Models\Iglesia::find($iglesiaId) : null;
        $pastor = $iglesia ? $iglesia->pastor : null;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Libro Mayor');
        
        // Row 1-3: Logo + Empresa info
        $row = 1;
        
        // Logo (column A)
        $logoPath = null;
        if ($empresa && $empresa->logo) {
            $logoPath = storage_path('app/public/' . $empresa->logo);
            if (!file_exists($logoPath)) {
                $logoPath = null;
            }
        }
        
        if ($logoPath) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo de la empresa');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
            
            // Company name next to logo
            $sheet->setCellValue('B' . $row, strtoupper($empresa->nombre ?? ''));
            $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->mergeCells('B' . $row . ':F' . $row);
            $row++;
            
            // RIF
            if ($empresa->rif) {
                $sheet->setCellValue('B' . $row, 'RIF: ' . $empresa->rif);
                $sheet->mergeCells('B' . $row . ':F' . $row);
                $row++;
            }
        } else {
            // No logo - just text
            $sheet->setCellValue('A' . $row, strtoupper($empresa->nombre ?? ''));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            
            // RIF
            if ($empresa->rif) {
                $sheet->setCellValue('A' . $row, 'RIF: ' . $empresa->rif);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        }
        
        // Church/Extension info
        if ($iglesia) {
            $sheet->setCellValue('A' . $row, 'EXTENSIÓN: ' . strtoupper($iglesia->nombre));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            
            // Address
            if ($iglesia->direccion) {
                $sheet->setCellValue('A' . $row, $iglesia->direccion);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            
            // Pastor info
            if ($pastor) {
                $pastorInfo = 'Pastor: ' . ($pastor->nombres ?? '') . ' ' . ($pastor->apellidos ?? '');
                if ($pastor->telefono_tlf) {
                    $pastorInfo .= ' | Tel: ' . $pastor->telefono_tlf;
                }
                $sheet->setCellValue('A' . $row, $pastorInfo);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        }
        
        // Empty row for spacing
        $row++;
        
        // Report title
        $sheet->setCellValue('A' . $row, 'LIBRO MAYOR');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        
        // Account and period info
        $infoText = "Cuenta: {$cuenta->codigo} - {$cuenta->nombre}  |  Desde: {$desde}  Hasta: {$hasta}";
        if ($iglesia) {
            $infoText .= "  |  Extensión: {$iglesia->nombre}";
        }
        $sheet->setCellValue('A' . $row, $infoText);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
        $row++;
        
        // Empty row before table
        $row++;

        // Saldo anterior
        $queryAnterior = AsientoDetalle::where('cuenta_id', $cuentaId)
            ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')->whereDate('fecha', '<', $desde));
        $di = (float) $queryAnterior->sum('debe');
        $hi = (float) (clone $queryAnterior)->sum('haber');
        $saldoAnterior = $cuenta->naturaleza === 'deudora' ? ($di - $hi) : ($hi - $di);
        $saldo = $saldoAnterior;

        // Header row
        $headers = ['Fecha', 'Asiento', 'Descripción', 'Debe', 'Haber', 'Saldo'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . $row, $h);
        }
        $this->styleHeaderRow($sheet, 'A' . $row . ':F' . $row);
        $row++;

        // Saldo anterior row
        $sheet->setCellValue('A' . $row, 'Saldo Anterior');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, $saldoAnterior);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setItalic(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
        $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $row++;

        // Movimientos
        $movimientos = AsientoDetalle::where('cuenta_id', $cuentaId)
            ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                ->when($iglesiaId, fn($iq) => $iq->where('iglesia_id', $iglesiaId))
                ->whereBetween('fecha', [$desde, $hasta]))
            ->with('asiento')
            ->get()
            ->sortBy('asiento.fecha');

        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($movimientos as $det) {
            $d = (float) $det->debe;
            $h = (float) $det->haber;
            $totalDebe += $d;
            $totalHaber += $h;

            if ($cuenta->naturaleza === 'deudora') {
                $saldo += ($d - $h);
            } else {
                $saldo += ($h - $d);
            }

            $sheet->setCellValue('A' . $row, $det->asiento->fecha->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $det->asiento->numero);
            $sheet->setCellValue('C' . $row, $det->descripcion ?: $det->asiento->descripcion);
            $sheet->setCellValue('D' . $row, $d > 0 ? $d : '');
            $sheet->setCellValue('E' . $row, $h > 0 ? $h : '');
            $sheet->setCellValue('F' . $row, $saldo);
            $sheet->getStyle('D' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Totals
        $sheet->setCellValue('C' . $row, 'TOTALES PERÍODO');
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, $totalDebe);
        $sheet->setCellValue('E' . $row, $totalHaber);
        $sheet->setCellValue('F' . $row, $saldo);
        $sheet->getStyle('D' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $this->styleTotalsRow($sheet, 'A' . $row . ':F' . $row);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Generado el: ' . now()->format('d/m/Y H:i:s'));

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);

        $writer = new Xlsx($spreadsheet);
        $filename = 'libro_mayor_' . $cuenta->codigo . '_' . str_replace('-', '', $desde) . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    // ================================================
    // BALANCE DE COMPROBACIÓN
    // ================================================
    public function balanceComprobacion()
    {
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->endOfMonth()->format('Y-m-d'));
        $empresaId = auth()->user()->empresa_id;
        $iglesiaId = request('iglesia_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Balance Comprobación');
        
        $empresa = Empresa::find($empresaId);
        $iglesia = $iglesiaId ? \App\Models\Iglesia::find($iglesiaId) : null;
        $pastor = $iglesia ? $iglesia->pastor : null;

        // Row 1-3: Logo + Empresa info
        $row = 1;
        
        // Logo (column A)
        $logoPath = null;
        if ($empresa && $empresa->logo) {
            $logoPath = storage_path('app/public/' . $empresa->logo);
            if (!file_exists($logoPath)) {
                $logoPath = null;
            }
        }
        
        if ($logoPath) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo de la empresa');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
            
            // Company name next to logo
            $sheet->setCellValue('B' . $row, strtoupper($empresa->nombre ?? ''));
            $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->mergeCells('B' . $row . ':F' . $row);
            $row++;
            
            // RIF
            if ($empresa->rif) {
                $sheet->setCellValue('B' . $row, 'RIF: ' . $empresa->rif);
                $sheet->mergeCells('B' . $row . ':F' . $row);
                $row++;
            }
        } else {
            // No logo - just text
            $sheet->setCellValue('A' . $row, strtoupper($empresa->nombre ?? ''));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            
            // RIF
            if ($empresa->rif) {
                $sheet->setCellValue('A' . $row, 'RIF: ' . $empresa->rif);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        }
        
        // Church/Extension info
        if ($iglesia) {
            $sheet->setCellValue('A' . $row, 'EXTENSIÓN: ' . strtoupper($iglesia->nombre));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            
            // Address
            if ($iglesia->direccion) {
                $sheet->setCellValue('A' . $row, $iglesia->direccion);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            
            // Pastor info
            if ($pastor) {
                $pastorInfo = 'Pastor: ' . ($pastor->nombres ?? '') . ' ' . ($pastor->apellidos ?? '');
                if ($pastor->telefono_tlf) {
                    $pastorInfo .= ' | Tel: ' . $pastor->telefono_tlf;
                }
                $sheet->setCellValue('A' . $row, $pastorInfo);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        }
        
        // Empty row for spacing
        $row++;
        
        // Report title
        $sheet->setCellValue('A' . $row, 'BALANCE DE COMPROBACIÓN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        
        // Period and church info
        $infoText = "Desde: {$desde}  Hasta: {$hasta}";
        if ($iglesia) {
            $infoText .= "  |  Extensión: {$iglesia->nombre}";
        }
        $sheet->setCellValue('A' . $row, $infoText);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
        $row++;
        
        // Empty row before table
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'Código');
        $sheet->setCellValue('B' . $row, 'Cuenta');
        $sheet->setCellValue('C' . $row, 'Debe');
        $sheet->setCellValue('D' . $row, 'Haber');
        $sheet->setCellValue('E' . $row, 'Saldo Deudor');
        $sheet->setCellValue('F' . $row, 'Saldo Acreedor');
        $this->styleHeaderRow($sheet, 'A' . $row . ':F' . $row);
        $row++;

        $cuentas = CuentaContable::where('empresa_id', $empresaId)
            ->where('acepta_movimientos', true)->where('activo', true)
            ->when($iglesiaId, fn($q) => $q->whereHas('asientosDetalles.asiento', fn($aq) => $aq->where('iglesia_id', $iglesiaId)))
            ->orderBy('codigo')->get();

        $totDebe = $totHaber = $totSD = $totSA = 0;

        foreach ($cuentas as $cuenta) {
            $detalles = AsientoDetalle::where('cuenta_id', $cuenta->id)
                ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                    ->when($iglesiaId, fn($iq) => $iq->where('iglesia_id', $iglesiaId))
                    ->whereBetween('fecha', [$desde, $hasta]));
            $debe = (float) $detalles->sum('debe');
            $haber = (float) (clone $detalles)->sum('haber');

            if ($debe == 0 && $haber == 0) continue;

            $saldoDeudor = $saldoAcreedor = 0;
            if ($cuenta->naturaleza === 'deudora') {
                $s = $debe - $haber;
                $s >= 0 ? $saldoDeudor = $s : $saldoAcreedor = abs($s);
            } else {
                $s = $haber - $debe;
                $s >= 0 ? $saldoAcreedor = $s : $saldoDeudor = abs($s);
            }

            $totDebe += $debe;
            $totHaber += $haber;
            $totSD += $saldoDeudor;
            $totSA += $saldoAcreedor;

            $sheet->setCellValue('A' . $row, $cuenta->codigo);
            $sheet->setCellValue('B' . $row, $cuenta->nombre);
            $sheet->setCellValue('C' . $row, $debe);
            $sheet->setCellValue('D' . $row, $haber);
            $sheet->setCellValue('E' . $row, $saldoDeudor > 0 ? $saldoDeudor : '');
            $sheet->setCellValue('F' . $row, $saldoAcreedor > 0 ? $saldoAcreedor : '');
            $sheet->getStyle('C' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Totals
        $sheet->setCellValue('B' . $row, 'TOTALES');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, $totDebe);
        $sheet->setCellValue('D' . $row, $totHaber);
        $sheet->setCellValue('E' . $row, $totSD);
        $sheet->setCellValue('F' . $row, $totSA);
        $sheet->getStyle('C' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $this->styleTotalsRow($sheet, 'A' . $row . ':F' . $row);

        $row += 2;
        $cuadrado = round($totSD, 2) === round($totSA, 2) ? '✓ Balance Cuadrado' : '✗ Descuadre: ' . format_money(abs($totSD - $totSA), 2, ',', '.');
        $sheet->setCellValue('A' . $row, $cuadrado);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Generado el: ' . now()->format('d/m/Y H:i:s'));

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);

        $writer = new Xlsx($spreadsheet);
        $filename = 'balance_comprobacion_' . str_replace('-', '', $desde) . '_' . str_replace('-', '', $hasta) . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    // ================================================
    // LIBRO DE VENTAS
    // ================================================
    public function libroVentas()
    {
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->endOfMonth()->format('Y-m-d'));
        $empresaId = auth()->user()->empresa_id;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Libro de Ventas');

        $row = $this->setupHeader($spreadsheet, 'LIBRO DE VENTAS - SENIAT', "Desde: {$desde}  Hasta: {$hasta}");

        // Headers
        $headers = [
            '#', 'Fecha', 'Hora', 'Tipo Doc.', 'Serie', 'Factura', 
            'N° Control', 'Doc. Afectado', 'RIF Cliente', 'Razón Social', 
            'Caja', 'Tasa', 'Creado por', 'Base Imp.', 'Monto Exento', 'IVA', 'IGTF', 'Total'
        ];
        foreach ($headers as $i => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $row, $h);
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $this->styleHeaderRow($sheet, 'A' . $row . ':' . $lastCol . $row);
        $row++;

        $documentos = \App\Models\Pago::where('empresa_id', $empresaId)
            ->where('es_factura_fiscal', true)
            ->whereIn('tipo_pago', ['factura', 'nota_credito', 'nota_debito'])
            ->where('estado', 'aprobado')
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['clienteFiscal', 'pagoOrigen', 'caja', 'user'])
            ->orderBy('fecha')
            ->orderBy('numero_control_fiscal')
            ->get();

        $totBase = 0;
        $totExento = 0;
        $totIva = 0;
        $totIgtf = 0;
        $totTotal = 0;

        foreach ($documentos as $index => $doc) {
            $tipoDoc = match($doc->tipo_pago) {
                'factura' => '01 - Factura',
                'nota_debito' => '02 - N. Débito',
                'nota_credito' => '03 - N. Crédito',
                default => $doc->tipo_pago,
            };

            $esNC = $doc->tipo_pago === 'nota_credito';
            $factor = $esNC ? -1 : 1;

            $base = (float) ($doc->base_imponible ?? 0);
            $exento = (float) ($doc->monto_exento ?? 0);
            $iva = (float) ($doc->iva_monto ?? 0);
            $igtf = (float) ($doc->igtf_monto ?? 0);
            $total = (float) ($doc->total_con_impuestos ?? 0);

            $totBase += ($base * $factor);
            $totExento += ($exento * $factor);
            $totIva += ($iva * $factor);
            $totIgtf += ($igtf * $factor);
            $totTotal += ($total * $factor);

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $doc->fecha->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $doc->created_at->format('H:i'));
            $sheet->setCellValue('D' . $row, $tipoDoc);
            $sheet->setCellValue('E' . $row, $doc->serie);
            $sheet->setCellValue('F' . $row, str_pad($doc->numero, 8, '0', STR_PAD_LEFT));
            $sheet->setCellValue('G' . $row, $doc->numero_control_fiscal ?? '-');
            
            $docAfectado = '-';
            if (($doc->tipo_pago === 'nota_credito' || $doc->tipo_pago === 'nota_debito') && $doc->pagoOrigen) {
                $docAfectado = $doc->pagoOrigen->numero_completo;
            }
            $sheet->setCellValue('H' . $row, $docAfectado);
            
            // Cliente / Paciente
            $docIdentidad = '-';
            $razonSocial = '-';

            if ($doc->clienteFiscal) {
                $docIdentidad = $doc->clienteFiscal->documento_completo;
                $razonSocial = $doc->clienteFiscal->razon_social;
            } elseif ($doc->consulta && $doc->consulta->paciente) {
                $docIdentidad = $doc->consulta->paciente->documento_identidad;
                $razonSocial = $doc->consulta->paciente->nombre_completo;
            }

            $sheet->setCellValue('I' . $row, $docIdentidad);
            $sheet->setCellValue('J' . $row, $razonSocial);
            $sheet->setCellValue('K' . $row, $doc->caja->nombre ?? '-');
            $sheet->setCellValue('L' . $row, $doc->tasa_cambio_usd);
            $sheet->setCellValue('M' . $row, $doc->user->name ?? '-');
            
            $sheet->setCellValue('N' . $row, $base);
            $sheet->setCellValue('O' . $row, $exento);
            $sheet->setCellValue('P' . $row, $iva);
            $sheet->setCellValue('Q' . $row, $igtf);
            $sheet->setCellValue('R' . $row, $total);

            // Formato de número
            $sheet->getStyle('L' . $row . ':R' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $row . ':R' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Totals
        $sheet->setCellValue('M' . $row, 'TOTALES:');
        $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue('N' . $row, $totBase);
        $sheet->setCellValue('O' . $row, $totExento);
        $sheet->setCellValue('P' . $row, $totIva);
        $sheet->setCellValue('Q' . $row, $totIgtf);
        $sheet->setCellValue('R' . $row, $totTotal);
        
        $sheet->getStyle('N' . $row . ':R' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $this->styleTotalsRow($sheet, 'A' . $row . ':R' . $row);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Generado el: ' . now()->format('d/m/Y H:i:s'));

        // Ajustar anchos de columna
        $widths = [
            'A' => 5, 'B' => 12, 'C' => 8, 'D' => 16, 'E' => 10, 
            'F' => 12, 'G' => 14, 'H' => 16, 'I' => 16, 'J' => 30, 
            'K' => 15, 'L' => 10, 'M' => 20, 
            'N' => 15, 'O' => 15, 'P' => 15, 'Q' => 15, 'R' => 16
        ];
        
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'libro_ventas_' . str_replace('-', '', $desde) . '_' . str_replace('-', '', $hasta) . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
