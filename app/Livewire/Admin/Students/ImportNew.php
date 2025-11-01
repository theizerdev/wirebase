<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\EducationalLevel;
use App\Models\Turno;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportNew extends Component
{
    use WithFileUploads;

    public $file;
    public $headers = [];
    public $previewData = [];
    public $totalRows = 0;
    public $selectedRows = [];
    public $selectAll = true;
    
    public $columnMapping = [];
    public $updateExisting = true;
    public $fillMissingWithNA = true;
    
    public $step = 1; // 1: Upload, 2: Preview, 3: Mapping, 4: Import
    public $importing = false;
    public $importedCount = 0;
    public $updatedCount = 0;
    public $failedCount = 0;
    public $errors = [];
    public $progress = 0;

    protected $rules = [
        'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240',
    ];

    public function mount()
    {
        $this->initializeColumnMapping();
    }

    public function render()
    {
        return view('livewire.admin.students.import-new')
            ->layout('components.layouts.admin');
    }

    private function initializeColumnMapping()
    {
        $fields = [
            'nombres', 'apellidos', 'documento_identidad', 'fecha_nacimiento',
            'grado', 'seccion', 'nivel_educativo', 'turno', 'school_period',
            'correo_electronico', 'representante_nombres', 'representante_apellidos',
            'representante_documento_identidad', 'representante_telefonos', 'representante_correo'
        ];
        
        foreach ($fields as $field) {
            $this->columnMapping[$field] = '';
        }
    }

    public function updatedFile()
    {
        $this->validate();
        $this->processFile();
    }

    private function processFile()
    {
        try {
            $path = $this->file->getRealPath();
            $extension = $this->file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->readExcel($path);
            } else {
                $this->readCsv($path);
            }

            $this->step = 2;
            $this->autoMapColumns();
        } catch (\Exception $e) {
            Log::error('Error procesando archivo: ' . $e->getMessage());
            session()->flash('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function readExcel($path)
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Leer encabezados
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $this->headers[] = $worksheet->getCellByColumnAndRow($col, 1)->getValue() ?? "Columna {$col}";
        }
        
        // Leer datos (máximo 500 filas para preview)
        $highestRow = min($worksheet->getHighestRow(), 501);
        $this->totalRows = $highestRow - 1;
        
        for ($row = 2; $row <= min($highestRow, 102); $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getValue();
                
                // Convertir fechas de Excel
                if ($cell->getDataType() === \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && 
                    \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
                }
                
                $rowData[] = $value;
            }
            $this->previewData[] = $rowData;
        }
        
        // Seleccionar todas las filas por defecto
        $this->selectedRows = range(0, count($this->previewData) - 1);
    }

    private function readCsv($path)
    {
        $file = fopen($path, 'r');
        
        // Leer encabezados
        $this->headers = fgetcsv($file, 0, ',') ?: [];
        
        // Leer datos (máximo 100 filas para preview)
        $rowCount = 0;
        while (($data = fgetcsv($file, 0, ',')) !== false && $rowCount < 100) {
            $this->previewData[] = $data;
            $rowCount++;
        }
        
        // Contar total de filas
        while (fgetcsv($file, 0, ',') !== false) {
            $rowCount++;
        }
        
        $this->totalRows = $rowCount;
        fclose($file);
        
        // Seleccionar todas las filas por defecto
        $this->selectedRows = range(0, count($this->previewData) - 1);
    }

    private function autoMapColumns()
    {
        $mapping = [
            'nombres' => ['nombre', 'nombres', 'name', 'first name', 'primer nombre'],
            'apellidos' => ['apellido', 'apellidos', 'surname', 'last name'],
            'documento_identidad' => ['documento', 'cedula', 'dni', 'id', 'documento identidad', 'documento_identidad'],
            'fecha_nacimiento' => ['fecha nacimiento', 'fecha_nacimiento', 'nacimiento', 'birth date', 'birthdate'],
            'grado' => ['grado', 'grade', 'nivel'],
            'seccion' => ['seccion', 'sección', 'section'],
            'nivel_educativo' => ['nivel educativo', 'nivel_educativo', 'educational level'],
            'turno' => ['turno', 'shift', 'jornada'],
            'school_period' => ['periodo', 'período', 'periodo escolar', 'school period'],
            'correo_electronico' => ['correo', 'email', 'correo electronico', 'correo_electronico'],
            'representante_nombres' => ['representante nombre', 'representante_nombres', 'padre nombre', 'tutor nombre'],
            'representante_apellidos' => ['representante apellido', 'representante_apellidos', 'padre apellido'],
            'representante_documento_identidad' => ['representante documento', 'representante_documento', 'padre documento'],
            'representante_telefonos' => ['representante telefono', 'representante_telefonos', 'telefono representante', 'padre telefono'],
            'representante_correo' => ['representante correo', 'representante_correo', 'padre correo', 'tutor correo'],
        ];

        foreach ($mapping as $field => $keywords) {
            foreach ($this->headers as $index => $header) {
                $headerLower = strtolower(trim($header));
                foreach ($keywords as $keyword) {
                    if (str_contains($headerLower, $keyword)) {
                        $this->columnMapping[$field] = $index;
                        break 2;
                    }
                }
            }
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedRows = range(0, count($this->previewData) - 1);
        } else {
            $this->selectedRows = [];
        }
    }

    public function nextStep()
    {
        if ($this->step === 2) {
            if (empty($this->selectedRows)) {
                session()->flash('error', 'Debes seleccionar al menos una fila para importar.');
                return;
            }
            $this->step = 3;
        } elseif ($this->step === 3) {
            $this->validateMapping();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    private function validateMapping()
    {
        $required = ['nombres', 'apellidos', 'documento_identidad'];
        $missing = [];

        foreach ($required as $field) {
            if ($this->columnMapping[$field] === '' || $this->columnMapping[$field] === null) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            session()->flash('error', 'Debes mapear los campos obligatorios: ' . implode(', ', $missing));
            return;
        }

        $this->step = 4;
        $this->startImport();
    }

    public function startImport()
    {
        $this->importing = true;
        $this->importedCount = 0;
        $this->updatedCount = 0;
        $this->failedCount = 0;
        $this->errors = [];
        $this->progress = 0;

        try {
            $path = $this->file->getRealPath();
            $extension = $this->file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->importFromExcel($path);
            } else {
                $this->importFromCsv($path);
            }

            $this->progress = 100;
            session()->flash('success', "Importación completada: {$this->importedCount} creados, {$this->updatedCount} actualizados, {$this->failedCount} fallidos.");
        } catch (\Exception $e) {
            Log::error('Error en importación: ' . $e->getMessage());
            session()->flash('error', 'Error durante la importación: ' . $e->getMessage());
        }

        $this->importing = false;
    }

    private function importFromExcel($path)
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $totalToProcess = count($this->selectedRows);
        $processed = 0;

        DB::beginTransaction();
        try {
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowIndex = $row - 2;
                
                if (!in_array($rowIndex, $this->selectedRows)) {
                    continue;
                }

                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $value = $cell->getValue();
                    
                    if ($cell->getDataType() === \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && 
                        \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
                    }
                    
                    $rowData[] = $value;
                }

                $this->processRow($rowData, $row);
                
                $processed++;
                $this->progress = round(($processed / $totalToProcess) * 100);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function importFromCsv($path)
    {
        $file = fopen($path, 'r');
        fgetcsv($file, 0, ','); // Skip headers

        $totalToProcess = count($this->selectedRows);
        $processed = 0;
        $rowIndex = 0;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 0, ',')) !== false) {
                if (!in_array($rowIndex, $this->selectedRows)) {
                    $rowIndex++;
                    continue;
                }

                $this->processRow($data, $rowIndex + 2);
                
                $processed++;
                $this->progress = round(($processed / $totalToProcess) * 100);
                $rowIndex++;
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($file);
    }

    private function processRow($rowData, $rowNumber)
    {
        try {
            $data = $this->mapRowData($rowData);
            
            // Validar datos mínimos
            if (empty($data['nombres']) && empty($data['apellidos']) && empty($data['documento_identidad'])) {
                $this->failedCount++;
                $this->errors[] = "Fila {$rowNumber}: Datos insuficientes";
                return;
            }

            // Preparar datos para crear
            $data['codigo'] = $this->generateUniqueCode();
            $data['empresa_id'] = auth()->user()->empresa_id;
            $data['sucursal_id'] = auth()->user()->sucursal_id;
            $data['status'] = true;
            
            // Asegurar que school_periods_id tenga un valor
            if (empty($data['school_periods_id'])) {
                $defaultPeriod = SchoolPeriod::where('empresa_id', auth()->user()->empresa_id)
                    ->where('is_active', true)
                    ->first();
                
                if (!$defaultPeriod) {
                    $defaultPeriod = SchoolPeriod::where('empresa_id', auth()->user()->empresa_id)
                        ->orderBy('id', 'desc')
                        ->first();
                }
                
                if ($defaultPeriod) {
                    $data['school_periods_id'] = $defaultPeriod->id;
                } else {
                    throw new \Exception('No hay períodos escolares disponibles. Por favor crea uno primero.');
                }
            }
            
            // Buscar estudiante existente solo si updateExisting está activado
            $student = null;
            if ($this->updateExisting && !empty($data['documento_identidad'])) {
                $student = Student::where('documento_identidad', $data['documento_identidad'])
                    ->where('empresa_id', auth()->user()->empresa_id)
                    ->first();
            }

            if ($student) {
                $student->update($data);
                $this->updatedCount++;
            } else {
                Student::create($data);
                $this->importedCount++;
            }
        } catch (\Exception $e) {
            $this->failedCount++;
            $this->errors[] = "Fila {$rowNumber}: " . $e->getMessage();
            Log::error("Error fila {$rowNumber}: " . $e->getMessage());
        }
    }

    private function mapRowData($rowData)
    {
        $data = [];

        foreach ($this->columnMapping as $field => $columnIndex) {
            if ($columnIndex === '' || $columnIndex === null || !isset($rowData[$columnIndex])) {
                $data[$field] = $this->fillMissingWithNA ? 'n/a' : null;
                continue;
            }

            $value = trim($rowData[$columnIndex]);
            
            // Filtrar valores inválidos
            if (in_array(strtolower($value), ['n/a', 'na', 'null', '', 'undefined'])) {
                $value = $this->fillMissingWithNA ? 'n/a' : null;
            }

            // Procesar según el campo
            switch ($field) {
                case 'fecha_nacimiento':
                    $data[$field] = $this->parseDate($value);
                    break;
                    
                case 'representante_telefonos':
                    if ($value && $value !== 'n/a') {
                        // Limpiar y convertir a array
                        $telefonos = array_map('trim', explode(',', $value));
                        $data[$field] = $telefonos;
                    } else {
                        $data[$field] = null;
                    }
                    break;
                    
                case 'nivel_educativo':
                    if ($value && $value !== 'n/a') {
                        $nivel = EducationalLevel::where('nombre', 'like', "%{$value}%")
                            ->where('empresa_id', auth()->user()->empresa_id)
                            ->first();
                        if ($nivel) {
                            $data['nivel_educativo_id'] = $nivel->id;
                        }
                    }
                    break;
                    
                case 'turno':
                    if ($value && $value !== 'n/a') {
                        $turno = Turno::where('nombre', 'like', "%{$value}%")
                            ->where('empresa_id', auth()->user()->empresa_id)
                            ->first();
                        if ($turno) {
                            $data['turno_id'] = $turno->id;
                        }
                    }
                    break;
                    
                case 'school_period':
                    if ($value && $value !== 'n/a') {
                        $period = SchoolPeriod::where('name', 'like', "%{$value}%")
                            ->where('empresa_id', auth()->user()->empresa_id)
                            ->first();
                        if ($period) {
                            $data['school_periods_id'] = $period->id;
                        }
                    }
                    break;
                    
                default:
                    $data[$field] = $value;
                    break;
            }
        }

        return $data;
    }

    private function parseDate($value)
    {
        if (!$value || $value === 'n/a') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'STU' . strtoupper(Str::random(6));
        } while (Student::where('codigo', $code)->exists());

        return $code;
    }

    public function resetImport()
    {
        $this->reset(['file', 'headers', 'previewData', 'totalRows', 'selectedRows', 'step', 'importing', 'importedCount', 'updatedCount', 'failedCount', 'errors', 'progress']);
        $this->initializeColumnMapping();
        $this->selectAll = true;
        $this->updateExisting = true;
        $this->fillMissingWithNA = true;
    }
}
