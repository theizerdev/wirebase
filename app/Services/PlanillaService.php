<?php

namespace App\Services;

use App\Models\Pastor;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class PlanillaService
{
    private $tempFiles = [];

    // SAIME-style color palette
    private $sectionBg   = [41, 128, 185];   // Blue section headers
    private $sectionTx   = [255, 255, 255];   // White text on headers
    private $labelBg     = [236, 240, 241];   // Light gray for labels
    private $valueBg     = [255, 255, 255];   // White for values
    private $textColor   = [0, 0, 0];         // Black
    private $subHeaderBg = [52, 73, 94];      // Dark blue for sub-headers

    // ---------------------------------------------------------------
    //  HELPER: Section header (full-width colored bar)
    // ---------------------------------------------------------------
    private function sectionHeader($fpdf, string $title, float $h = 8): void
    {
        $fpdf->SetFillColor($this->sectionBg[0], $this->sectionBg[1], $this->sectionBg[2]);
        $fpdf->SetTextColor($this->sectionTx[0], $this->sectionTx[1], $this->sectionTx[2]);
        $fpdf->SetFont('Arial', 'B', 11);
        $fpdf->Cell(0, $h, utf8_decode($title), 1, 1, 'C', true);
        $fpdf->SetTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);
        $fpdf->SetFont('Arial', '', 9);
    }

    // ---------------------------------------------------------------
    //  HELPER: Sub-section header (for church blocks)
    // ---------------------------------------------------------------
    private function subHeader($fpdf, string $title, float $h = 6): void
    {
        $fpdf->SetFillColor($this->subHeaderBg[0], $this->subHeaderBg[1], $this->subHeaderBg[2]);
        $fpdf->SetTextColor($this->sectionTx[0], $this->sectionTx[1], $this->sectionTx[2]);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(0, $h, utf8_decode($title), 1, 1, 'C', true);
        $fpdf->SetTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);
        $fpdf->SetFont('Arial', '', 9);
    }

    // ---------------------------------------------------------------
    //  HELPER: Data row with label-value pairs
    //  $fields = [
    //    ['label'=>'X', 'value'=>'Y', 'lw'=>35, 'vw'=>60],
    //    ['label'=>'A', 'value'=>'B', 'lw'=>35, 'vw'=>60],
    //  ]
    // ---------------------------------------------------------------
    private function dataRow($fpdf, array $fields, float $h = 7): void
    {
        foreach ($fields as $f) {
            $lw = $f['lw'] ?? 35;
            $vw = $f['vw'] ?? 60;
            $fpdf->SetFillColor($this->labelBg[0], $this->labelBg[1], $this->labelBg[2]);
            $fpdf->Cell($lw, $h, utf8_decode($f['label']), 1, 0, 'L', true);
            $fpdf->SetFillColor($this->valueBg[0], $this->valueBg[1], $this->valueBg[2]);
            $fpdf->Cell($vw, $h, utf8_decode($f['value'] ?? 'No especificado'), 1, 0, 'L', true);
        }
        $fpdf->Ln($h);
    }

    // ---------------------------------------------------------------
    //  HELPER: Full-width data row (label on top, value below)
    // ---------------------------------------------------------------
    private function dataRowFull($fpdf, string $label, string $value, float $h = 7): void
    {
        $fpdf->SetFillColor($this->labelBg[0], $this->labelBg[1], $this->labelBg[2]);
        $fpdf->Cell(0, $h, utf8_decode($label), 1, 1, 'L', true);
        $fpdf->SetFillColor($this->valueBg[0], $this->valueBg[1], $this->valueBg[2]);
        $fpdf->MultiCell(0, $h, utf8_decode($value ?: 'No especificado'), 1, 'L', true);
    }

    // ---------------------------------------------------------------
    //  HELPER: Generate QR code PNG and return temp path
    // ---------------------------------------------------------------
    private function generarQrCode(Pastor $pastor): string
    {
        $url = route('admin.pastores.show', $pastor->id);

        $qr = new QrCode(
            data: $url,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 200,
            margin: 5
        );

        

        $path = storage_path('app/temp/qr_pastor_' . $pastor->id . '_' . time() . '.png');
        (new PngWriter())->write($qr)->saveToFile($path);
        $this->tempFiles[] = $path;

        return $path;
    }

    private function cleanupTempFiles(): void
    {
        foreach ($this->tempFiles as $f) {
            if (file_exists($f)) {
                @unlink($f);
            }
        }
        $this->tempFiles = [];
    }

    // ---------------------------------------------------------------
    //  HELPER: Split string into first / second parts
    // ---------------------------------------------------------------
    private function splitName(?string $full): array
    {
        $parts = preg_split('/\s+/', trim($full ?? ''), 2);
        return [$parts[0] ?? '', $parts[1] ?? ''];
    }

    // ---------------------------------------------------------------
    //  HELPER: Build full address string
    // ---------------------------------------------------------------
    public function construirDireccionCompleta($pastor): string
    {
        $parts = [];
        if ($pastor->edificio_casa_quinta) $parts[] = $pastor->edificio_casa_quinta;
        if ($pastor->piso)                 $parts[] = 'Piso ' . $pastor->piso;
        if ($pastor->apartamento)          $parts[] = 'Apto ' . $pastor->apartamento;
        if ($pastor->calle_avenida)        $parts[] = $pastor->calle_avenida;
        if ($pastor->urbanizacion)         $parts[] = $pastor->urbanizacion;
        return $parts ? implode(', ', $parts) : 'No especificada';
    }

    // ---------------------------------------------------------------
    //  HELPER: Deduplicate array values (case-insensitive)
    // ---------------------------------------------------------------
    private function dedupeImplode(array $items): string
    {
        $filtered = array_filter($items);
        if (empty($filtered)) return '';
        return implode(', ', array_intersect_key($filtered, array_unique(array_map('mb_strtolower', $filtered))));
    }

    // ===============================================================
    //  MAIN: Generate PDF
    // ===============================================================
    public function generarPdfParaPastor(Pastor $pastor, $fpdf = null)
    {
        if (!$fpdf) {
            $fpdf = app('fpdf');
        }

        // Load iglesias (include principal's churches if pastor is spouse)
        $iglesias = $pastor->iglesias;
        if ($pastor->esConyuge() && $pastor->pastorPrincipal) {
            $iglesias = $iglesias->merge($pastor->pastorPrincipal->iglesias);
        }

        // Generate QR code
        $qrPath = $this->generarQrCode($pastor);

        // Split names
        [$primerNombre, $segundoNombre]   = $this->splitName($pastor->nombres);
        [$primerApellido, $segundoApellido] = $this->splitName($pastor->apellidos);

        // ==============================================================
        //  PAGE SETUP
        // ==============================================================
        $fpdf->AddPage();
        $fpdf->SetAutoPageBreak(true, 10);
        $fpdf->SetMargins(10, 10, 10);
        $fpdf->SetTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);

        // ==============================================================
        //  HEADER - SAIME STYLE
        // ==============================================================
        $headerTop = 10;

        // Logo (upper left)
        $logoPath = public_path('logo/1719430882.png');
        if (file_exists($logoPath)) {
            $fpdf->Image($logoPath, 10, $headerTop, 22, 15);
        }

        // Organization text (centered, offset for logo)
        $fpdf->SetXY(34, $headerTop);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->MultiCell(130, 4.5, utf8_decode("IGLESIA CRISTIANA PENTECOSTÉS DE VENEZUELA\nDEL MOVIMIENTO MISIONERO MUNDIAL"), 0, 'C');

        $fpdf->SetXY(34, $fpdf->GetY() + 1);
        $fpdf->SetFont('Arial', '', 7.5);
        $fpdf->Cell(130, 3.5, utf8_decode('RIF: J-301874463  |  Tel: 0212-8600173'), 0, 1, 'C');
        $fpdf->SetX(34);
        $fpdf->Cell(130, 3.5, utf8_decode('Sede Central: Av. Sucre de Catia, cruce con Calle El Carmen, Local 5B, Caracas'), 0, 1, 'C');

        // QR Code (upper right)
       // if (file_exists($qrPath)) {
       //     $fpdf->Image($qrPath, 170, $headerTop, 20, 20);
       // }

        // Separator line
        $fpdf->SetDrawColor($this->sectionBg[0], $this->sectionBg[1], $this->sectionBg[2]);
        $fpdf->SetLineWidth(0.5);
        $lineY = $headerTop + 27;
        $fpdf->Line(10, $lineY, 200, $lineY);

        // ==============================================================
        //  TITLE BAR
        // ==============================================================
        $fpdf->SetY($lineY + 2);
        $fpdf->SetFillColor($this->sectionBg[0], $this->sectionBg[1], $this->sectionBg[2]);
        $fpdf->SetTextColor($this->sectionTx[0], $this->sectionTx[1], $this->sectionTx[2]);
        $fpdf->SetFont('Arial', 'B', 12);
        //$fpdf->Cell(0, 9, utf8_decode('PLANILLA DE REGISTRO DE DATOS DEL OBRERO'), 1, 1, 'C', true);

        // ==============================================================
        //  PHOTO (right side, overlapping with first data section)
        // ==============================================================
        $photoX = 170;
        $photoY = $fpdf->GetY() - 35;
        $photoW = 28;
        $photoH = 29;

        // Draw photo placeholder
        $fpdf->SetFillColor($this->labelBg[0], $this->labelBg[1], $this->labelBg[2]);
        $fpdf->Rect($photoX, $photoY, $photoW, $photoH, 'D');

        if ($pastor->foto && file_exists(public_path('pastores/' . str_replace(' ', '', $pastor->foto)))) {
            $fpdf->Image(public_path('pastores/' . str_replace(' ', '', $pastor->foto)), $photoX, $photoY, $photoW, $photoH);
        } else {
            $fpdf->Image(public_path('pastores/sin-foto.jpg'), $photoX, $photoY, $photoW, $photoH);
        }

        // Reset for data sections
        $fpdf->SetTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);
        $fpdf->SetFont('Arial', '', 9);

        // ==============================================================
        //  DATOS PERSONALES
        // ==============================================================
        $fpdf->SetY($fpdf->GetY() + 2);
        $this->sectionHeader($fpdf, 'DATOS PERSONALES');

        $fechaNac = $pastor->fe_nacimiento ? date('d/m/Y', strtotime($pastor->fe_nacimiento)) : 'No especificada';
        $telefono = $pastor->telefono_hab ?? $pastor->telefono_tlf ?? $pastor->telefono_otro ?? 'No especificado';
        $email    = $pastor->email ?? 'No especificado';

        $this->dataRow($fpdf, [
            ['label' => 'Código:',           'value' => $pastor->codigo,           'lw' => 25, 'vw' => 70],
            ['label' => 'Documento:',         'value' => $pastor->documento,         'lw' => 25, 'vw' => 70],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Primer Nombre:',     'value' => $primerNombre,              'lw' => 30, 'vw' => 65],
            ['label' => 'Segundo Nombre:',     'value' => $segundoNombre ?: '-',      'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Primer Apellido:',   'value' => $primerApellido,            'lw' => 30, 'vw' => 65],
            ['label' => 'Segundo Apellido:',  'value' => $segundoApellido ?: '-',    'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Sexo:',              'value' => $pastor->genero,            'lw' => 25, 'vw' => 70],
            ['label' => 'Fecha Nacimiento:',   'value' => $fechaNac,                  'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Estado Civil:',      'value' => $pastor->estado_civil,      'lw' => 25, 'vw' => 70],
            ['label' => 'Edad:',              'value' => $pastor->edad ? $pastor->edad . ' años' : null, 'lw' => 25, 'vw' => 70],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Teléfono:',          'value' => $telefono,                  'lw' => 25, 'vw' => 70],
            ['label' => 'Email:',             'value' => $email,                     'lw' => 25, 'vw' => 70],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Grado Instrucción:', 'value' => $pastor->grado_instruccion, 'lw' => 30, 'vw' => 65],
            ['label' => 'Título Obtenido:',   'value' => $pastor->titulo_obtenido,   'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Bautizado E.S.:',    'value' => $pastor->batizado_espiritu_santo ? 'Sí' : 'No',  'lw' => 25, 'vw' => 70],
            ['label' => 'En Ministerio:',     'value' => $pastor->pertenece_ministerio ? 'Sí' : 'No',     'lw' => 25, 'vw' => 70],
        ]);

        $fpdf->Ln(4);

        // ==============================================================
        //  DATOS DE UBICACIÓN
        // ==============================================================
        $this->sectionHeader($fpdf, 'DATOS DE UBICACIÓN');

        $this->dataRow($fpdf, [
            ['label' => 'Estado:',    'value' => $pastor->estado->nombre ?? null,    'lw' => 25, 'vw' => 70],
            ['label' => 'Ciudad:',    'value' => $pastor->ciudad->nombre ?? null,    'lw' => 25, 'vw' => 70],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Municipio:', 'value' => $pastor->municipio->nombre ?? null, 'lw' => 25, 'vw' => 70],
            ['label' => 'Parroquia:', 'value' => $pastor->parroquia->nombre ?? null, 'lw' => 25, 'vw' => 70],
        ]);

        $this->dataRowFull($fpdf, 'Dirección:', $this->construirDireccionCompleta($pastor));

        $fpdf->Ln(4);

        // ==============================================================
        //  DATOS MINISTERIALES
        // ==============================================================
        $this->sectionHeader($fpdf, 'DATOS MINISTERIALES');

        $this->dataRow($fpdf, [
            ['label' => 'Nivel Ministerial:', 'value' => $pastor->nivel_ministerial, 'lw' => 30, 'vw' => 65],
            ['label' => 'Año Promoción:',     'value' => $pastor->ano_promocion,     'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'T. Ministerial:',    'value' => $pastor->tiempo_colaborando, 'lw' => 30, 'vw' => 65],
            ['label' => 'Cargo Nacional:',    'value' => $pastor->cargo_nacional,     'lw' => 30, 'vw' => 65],
        ]);

        $this->dataRow($fpdf, [
            ['label' => 'Estado:',            'value' => $pastor->status ? 'Activo' : 'Inactivo',       'lw' => 25, 'vw' => 70],
            ['label' => 'Estudios Teológicos:','value' => $pastor->estudio_teologico ? 'Sí' : 'No',     'lw' => 30, 'vw' => 65],
        ]);

        if ($pastor->estudio_teologico) {
            $this->dataRowFull($fpdf, 'Título Teológico:', $pastor->titulo_teologico ?? 'No especificado');

            $this->dataRow($fpdf, [
                ['label' => 'Tiempo de Estudio:', 'value' => $pastor->tiempo_de_estudio_teologico, 'lw' => 30, 'vw' => 65],
                ['label' => 'Instituto Teológico:', 'value' => $pastor->instituto_teologico,       'lw' => 30, 'vw' => 65],
            ]);
        }

        $fpdf->Ln(4);

        // ==============================================================
        //  DATOS DEL CÓNYUGE (conditional)
        // ==============================================================
        if ($pastor->nombre_conyuge) {
            $this->sectionHeader($fpdf, 'DATOS DEL CÓNYUGE');

            $this->dataRowFull($fpdf, 'Nombre:', $pastor->nombre_conyuge);

            if ($pastor->conyuge) {
                $telConyuge = $pastor->conyuge->telefono_hab ?? $pastor->conyuge->telefono_tlf ?? $pastor->conyuge->telefono_otro ?? 'No especificado';

                $this->dataRow($fpdf, [
                    ['label' => 'Documento:', 'value' => $pastor->conyuge->documento, 'lw' => 25, 'vw' => 70],
                    ['label' => 'Teléfono:',  'value' => $telConyuge,                 'lw' => 25, 'vw' => 70],
                ]);
            }

            $fpdf->Ln(4);
        }

        // ==============================================================
        //  EXTENSIONES ASOCIADAS
        // ==============================================================
        if ($iglesias->count() > 0) {
            $this->sectionHeader($fpdf, 'EXTENSIONES ASOCIADAS');

            foreach ($iglesias as $index => $iglesia) {
                // Church name sub-header
                $fpdf->SetFillColor($this->sectionBg[0], $this->sectionBg[1], $this->sectionBg[2]);
                $fpdf->SetTextColor($this->sectionTx[0], $this->sectionTx[1], $this->sectionTx[2]);
                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(0, 7, utf8_decode('Iglesia #' . ($index + 1) . ': ' . ($iglesia->nombre ?? 'Sin nombre')), 1, 1, 'L', true);
                $fpdf->SetTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);
                $fpdf->SetFont('Arial', '', 9);

                // --- INFORMACIÓN BÁSICA ---
                $this->subHeader($fpdf, 'INFORMACIÓN BÁSICA');

                $fechaFund = $iglesia->fecha_fundacion ? date('d/m/Y', strtotime($iglesia->fecha_fundacion)) : 'No especificada';

                $this->dataRow($fpdf, [
                    ['label' => 'Tipo Local:',      'value' => $iglesia->tipoLocal->nombre ?? null, 'lw' => 28, 'vw' => 67],
                    ['label' => 'Fecha Fundación:', 'value' => $fechaFund,                          'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Años Activa:', 'value' => ($iglesia->anios_activa ?? '0') . ' años', 'lw' => 28, 'vw' => 67],
                    ['label' => 'Estado:',       'value' => $iglesia->activa ? 'Activa' : 'Inactiva', 'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRowFull($fpdf, 'Descripción:', $iglesia->descripcion ?? 'No especificada');

                // --- CONTACTO ---
                $this->subHeader($fpdf, 'INFORMACIÓN DE CONTACTO');

                $this->dataRow($fpdf, [
                    ['label' => 'Teléfono:', 'value' => $iglesia->telefono, 'lw' => 28, 'vw' => 67],
                    ['label' => 'Email:',    'value' => $iglesia->email,    'lw' => 28, 'vw' => 67],
                ]);

                // --- UBICACIÓN ---
                $this->subHeader($fpdf, 'INFORMACIÓN DE UBICACIÓN');

                $this->dataRow($fpdf, [
                    ['label' => 'Estado:',    'value' => $iglesia->estado->nombre ?? null,    'lw' => 28, 'vw' => 67],
                    ['label' => 'Ciudad:',    'value' => $iglesia->ciudad->nombre ?? null,    'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Municipio:', 'value' => $iglesia->municipio->nombre ?? null, 'lw' => 28, 'vw' => 67],
                    ['label' => 'Parroquia:', 'value' => $iglesia->parroquia->nombre ?? null, 'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Zona:',     'value' => $iglesia->zona,     'lw' => 28, 'vw' => 67],
                    ['label' => 'Distrito:', 'value' => $iglesia->distrito, 'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Sector:',  'value' => $iglesia->sector,  'lw' => 28, 'vw' => 67],
                    ['label' => 'Calle:',   'value' => $iglesia->calle,   'lw' => 28, 'vw' => 67],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Avenida:', 'value' => $iglesia->avenida, 'lw' => 28, 'vw' => 162],
                ]);

                $this->dataRowFull($fpdf, 'Dirección:', $iglesia->direccion ?? 'No especificada');

                $coordenadas = ($iglesia->latitud && $iglesia->longitud)
                    ? $iglesia->latitud . ', ' . $iglesia->longitud
                    : 'No especificadas';
                $this->dataRow($fpdf, [
                    ['label' => 'Coordenadas:', 'value' => $coordenadas, 'lw' => 28, 'vw' => 162],
                ]);

                // --- ESTADÍSTICAS ---
                $this->subHeader($fpdf, 'ESTADÍSTICAS DE LA IGLESIA');

                $this->dataRow($fpdf, [
                    ['label' => 'Miembros Activos:', 'value' => ($iglesia->miembros_activos ?? '0') . ' miembros', 'lw' => 32, 'vw' => 63],
                    ['label' => 'Campos Blancos:',   'value' => ($iglesia->cantidad_campos_blancos ?? '0') . ' campos', 'lw' => 32, 'vw' => 63],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Miembro Probante:', 'value' => ($iglesia->miembro_probante ?? '0') . ' miembros', 'lw' => 32, 'vw' => 63],
                    ['label' => 'Tiempo Trabajo:',   'value' => $iglesia->tiempo_trabajo,                         'lw' => 32, 'vw' => 63],
                ]);

                $this->dataRow($fpdf, [
                    ['label' => 'Iglesias Fundadas:',    'value' => ($iglesia->iglesias_fundadas ?? '0') . ' iglesias',  'lw' => 32, 'vw' => 63],
                    ['label' => 'Pastores Ministerio:',  'value' => ($iglesia->pastores_ministerio ?? '0') . ' pastores', 'lw' => 32, 'vw' => 63],
                ]);

                $this->dataRowFull($fpdf, 'Logros Obtenidos:', $iglesia->logros_obtenidos ?? 'No especificados');

                // --- MEDIOS DE COMUNICACIÓN ---
                $this->subHeader($fpdf, 'MEDIOS DE COMUNICACIÓN');

                $this->dataRow($fpdf, [
                    ['label' => 'Tiene Medio Com.:', 'value' => $iglesia->posee_medio_comunicacion ? 'Sí' : 'No', 'lw' => 32, 'vw' => 63],
                ]);

                if ($iglesia->posee_medio_comunicacion) {
                    $medioComunicacion = $iglesia->medio_comunicacion;

                    // Tipo de medio
                    if (is_array($medioComunicacion) && !empty($medioComunicacion)) {
                        $tipos = array_map(function ($m) {
                            return (is_array($m) && isset($m['tipo'])) ? $m['tipo'] : (is_string($m) ? $m : '');
                        }, $medioComunicacion);
                        $tipoTexto = $this->dedupeImplode($tipos) ?: 'No especificado';
                    } else {
                        $tipoTexto = is_string($medioComunicacion) ? $medioComunicacion : 'No especificado';
                    }
                    $this->dataRow($fpdf, [
                        ['label' => 'Tipo Medio:', 'value' => $tipoTexto, 'lw' => 28, 'vw' => 67],
                    ]);

                    // Nombre del medio
                    $nombreTexto = $iglesia->nombre_medio_comunicacion;
                    if (empty($nombreTexto) && is_array($medioComunicacion) && !empty($medioComunicacion)) {
                        $nombres = array_map(function ($m) {
                            return (is_array($m) && isset($m['nombre'])) ? $m['nombre'] : '';
                        }, $medioComunicacion);
                        $nombreTexto = $this->dedupeImplode($nombres);
                    }
                    $this->dataRow($fpdf, [
                        ['label' => 'Nombre Medio:', 'value' => $nombreTexto ?: 'No especificado', 'lw' => 28, 'vw' => 162],
                    ]);

                    // Ubicación del medio
                    $ubicTexto = $iglesia->donde_medio_comunicacion;
                    if (empty($ubicTexto) && is_array($medioComunicacion) && !empty($medioComunicacion)) {
                        $ubicaciones = array_map(function ($m) {
                            return (is_array($m) && isset($m['ubicacion'])) ? $m['ubicacion'] : '';
                        }, $medioComunicacion);
                        $ubicTexto = $this->dedupeImplode($ubicaciones);
                    }
                    $this->dataRow($fpdf, [
                        ['label' => 'Ubicación:', 'value' => $ubicTexto ?: 'No especificada', 'lw' => 28, 'vw' => 162],
                    ]);
                }

                // --- REGISTRO ---
                $this->subHeader($fpdf, 'INFORMACIÓN DE REGISTRO');

                $usuarioRegistro = $iglesia->usuarioRegistro
                    ? $iglesia->usuarioRegistro->name
                    : 'No especificado';
                $this->dataRow($fpdf, [
                    ['label' => 'Registrado Por:', 'value' => $usuarioRegistro, 'lw' => 28, 'vw' => 162],
                ]);

                if ($index < $iglesias->count() - 1) {
                    $fpdf->Ln(4);
                }
            }
        }

        // ==============================================================
        //  FOOTER
        // ==============================================================
        $fpdf->Ln(8);
        $fpdf->SetDrawColor($this->sectionBg[0], $this->sectionBg[1], $this->sectionBg[2]);
        $fpdf->SetLineWidth(0.3);
        $fpdf->Line(10, $fpdf->GetY(), 200, $fpdf->GetY());
        $fpdf->Ln(3);
         if (file_exists($qrPath)) {
            $fpdf->Image($qrPath, 10, $fpdf->GetY(), 40, 40);
        }
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(0, 5, utf8_decode('Serial: ' . ($pastor->codigo ?? 'N/A')), 0, 1, 'C');
        $fpdf->SetFont('Arial', 'I', 7);
        $fpdf->Cell(0, 4, utf8_decode('Planilla generada el ' . date('d/m/Y H:i:s')), 0, 1, 'C');

        // Cleanup temp files
        $this->cleanupTempFiles();
    }
}
