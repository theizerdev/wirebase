<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\EducationalLevel;
use App\Models\Turno;
use App\Models\SchoolPeriod;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Import extends Component
{
    use WithFileUploads;

    public $file;
    public $preview = [];
    public $importing = false;
    public $imported = false;
    public $totalRows = 0;
    public $importedRows = 0;
    public $failedRows = 0;
    public $errorsList = [];
    public $importProgress = 0;
    
    // Nuevas propiedades para mejorar la importación
    public $selectedRows = []; // Filas seleccionadas para importar
    public $selectAll = false; // Seleccionar todas las filas
    public $updateExisting = true; // Actualizar si existe
    public $fillMissingWithNA = true; // Llenar datos faltantes con 'n/a'
    public $importMode = 'preview'; // preview, mapping, importing
    public $processedData = []; // Datos procesados para importar
    public $validationErrors = []; // Errores de validación por fila
    
    // Campos para mapeo de columnas
    public $columnMapping = [
        'nombres' => '',
        'apellidos' => '',
        'fecha_nacimiento' => '',
        'documento_identidad' => '',
        'grado' => '',
        'seccion' => '',
        'nivel_educativo' => '',
        'turno' => '',
        'school_period' => '',
        'correo_electronico' => '',
        'representante_nombres' => '',
        'representante_apellidos' => '',
        'representante_documento_identidad' => '',
        'representante_telefonos' => '',
        'representante_correo' => '',
    ];
    
    protected $rules = [
        'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
    ];

    public function render()
    {
        return view('livewire.admin.students.import')
            ->layout('components.layouts.admin', [
                'title' => 'Importar Estudiantes',
                'description' => 'Importar estudiantes masivamente desde un archivo CSV o Excel'
            ]);
    }

    public function updatedFile()
    {
        $this->validate();
        
        // Limpiar datos previos
        $this->preview = [];
        $this->errorsList = [];
        
        try {
            // Procesar archivo para previsualización
            $this->processFileForPreview();
        } catch (\Exception $e) {
            Log::error('Error al procesar archivo para previsualización: ' . $e->getMessage());
            session()->flash('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function processFileForPreview()
    {
        $path = $this->file->getRealPath();
        
        // Detectar tipo de archivo
        $extension = $this->file->getClientOriginalExtension();
        
        if (in_array($extension, ['xlsx', 'xls'])) {
            // Procesar archivo Excel
            $this->processExcelFile($path);
        } else {
            // Procesar archivo CSV
            $this->processCsvFile($path);
        }
    }

    private function processExcelFile($path)
    {
        $reader = IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Obtener encabezados
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headers[] = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
        }
        
        // Obtener todas las filas para procesamiento
        $allRows = [];
        $highestRow = $worksheet->getHighestRow();
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            $allRows[] = $rowData;
        }
        
        // Procesar y validar datos
        $this->processAndValidateData($headers, $allRows);
    }

    private function processAndValidateData($headers, $allRows)
    {
        $this->totalRows = count($allRows);
        $this->processedData = [];
        $this->validationErrors = [];
        $this->selectedRows = [];
        
        foreach ($allRows as $index => $row) {
            $rowData = [];
            $errors = [];
            
            // Procesar cada columna mapeada
            foreach ($this->columnMapping as $field => $columnIndex) {
                if ($columnIndex !== null && isset($row[$columnIndex])) {
                    $value = trim($row[$columnIndex]);
                    
                    // Validar campos requeridos
                    if ($this->isRequiredField($field) && empty($value)) {
                        if ($this->fillMissingWithNA) {
                            $value = 'n/a';
                        } else {
                            $errors[] = "Campo requerido: {$field}";
                        }
                    }
                    
                    $rowData[$field] = $value;
                } else {
                    // Campo no mapeado o vacío
                    if ($this->fillMissingWithNA) {
                        $rowData[$field] = 'n/a';
                    } elseif ($this->isRequiredField($field)) {
                        $errors[] = "Campo requerido no mapeado: {$field}";
                    }
                }
            }
            
            // Validar formato de fecha
            if (isset($rowData['fecha_nacimiento']) && !empty($rowData['fecha_nacimiento']) && $rowData['fecha_nacimiento'] !== 'n/a') {
                try {
                    Carbon::parse($rowData['fecha_nacimiento']);
                } catch (\Exception $e) {
                    $errors[] = "Formato de fecha inválido";
                }
            }
            
            // Verificar si el estudiante ya existe
            if (isset($rowData['documento_identidad'])) {
                $existingStudent = Student::where('documento_identidad', $rowData['documento_identidad'])->first();
                if ($existingStudent) {
                    $rowData['existing_student'] = true;
                    $rowData['existing_student_id'] = $existingStudent->id;
                } else {
                    $rowData['existing_student'] = false;
                }
            }
            
            $this->processedData[$index] = $rowData;
            $this->validationErrors[$index] = $errors;
            $this->selectedRows[] = $index; // Seleccionar por defecto
        }
        
        // Preparar vista previa (primeras 5 filas para mostrar en la interfaz)
        $previewRows = array_slice($allRows, 0, 300);
        $this->preview = [
            'headers' => $headers,
            'rows' => $previewRows
        ];
    }

    private function isRequiredField($field)
    {
        $requiredFields = ['nombres', 'apellidos', 'documento_identidad', 'nivel_educativo', 'grado', 'seccion'];
        return in_array($field, $requiredFields);
    }

    private function processCsvFile($path)
    {
        $file = fopen($path, 'r');
        
        // Obtener encabezados
        $headers = fgetcsv($file, 1000, ',');
        
        // Obtener todas las filas para procesamiento
        $allRows = [];
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            $allRows[] = $data;
        }
        
        fclose($file);
        
        // Procesar y validar todos los datos
        $this->processAndValidateData($headers, $allRows);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedRows = array_keys($this->processedData);
        } else {
            $this->selectedRows = [];
        }
    }

    public function toggleRowSelection($rowIndex)
    {
        if (in_array($rowIndex, $this->selectedRows)) {
            $this->selectedRows = array_diff($this->selectedRows, [$rowIndex]);
        } else {
            $this->selectedRows[] = $rowIndex;
        }
    }

    public function proceedToMapping()
    {
        $this->importMode = 'mapping';
    }

    public function proceedToImport()
    {
        $this->importMode = 'importing';
        $this->import();
    }

    public function saveMapping()
    {
        // Guardar la configuración de mapeo actual
        session(['columnMapping' => $this->columnMapping]);
        
        // Continuar con el siguiente paso
        $this->proceedToImport();
    }

    public function setImportMode($mode)
    {
        $this->importMode = $mode;
    }

    public function import()
    {
        $this->validate([
            'columnMapping.nombres' => 'required|integer|min:0',
            'columnMapping.apellidos' => 'required|integer|min:0',
            'columnMapping.documento_identidad' => 'required|integer|min:0',
        ]);

        $this->importing = true;
        $this->importedRows = 0;
        $this->failedRows = 0;
        $this->errorsList = [];
        $this->importProgress = 0;

        $totalRows = count($this->selectedRows);
        $processedRows = 0;

        DB::transaction(function () use (&$processedRows, $totalRows) {
            foreach ($this->selectedRows as $rowIndex) {
                if (isset($this->processedData[$rowIndex])) {
                    $this->processRow($this->processedData[$rowIndex], $rowIndex);
                    
                    // Actualizar progreso
                    $processedRows++;
                    $this->importProgress = $totalRows > 0 ? round(($processedRows / $totalRows) * 100) : 0;
                }
            }
        });

        $this->importing = false;
        $this->imported = true;
        $this->importProgress = 100;
    }

    private function importFromExcel($path)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        
        // Procesar cada fila
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            
            $this->processRow($rowData, $row);
        }
    }

    private function importFromCsv($path)
    {
        $file = fopen($path, 'r');
        fgetcsv($file, 1000, ','); // Saltar encabezados
        
        $rowNumber = 2;
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            $this->processRow($data, $rowNumber);
            $rowNumber++;
        }
        
        fclose($file);
    }

    private function processRow($rowData, $rowIndex)
    {
        try {
            // Si el estudiante existe y no queremos actualizar, saltar
            if (isset($rowData['existing_student']) && $rowData['existing_student'] && !$this->updateExisting) {
                return;
            }
            
            // Preparar datos del estudiante
            $studentData = $this->prepareStudentData($rowData);
            
            // Validar que al menos haya datos mínimos
            if (!$this->hasMinimumRequiredData($studentData)) {
                Log::warning('Fila sin datos mínimos requeridos', [
                    'fila' => $rowIndex + 1,
                    'datos' => $studentData
                ]);
                $this->failedRows++;
                $this->errorsList[] = [
                    'row' => $rowIndex + 1,
                    'error' => 'Fila sin datos mínimos requeridos (nombres, apellidos o documento)',
                    'data' => $rowData
                ];
                return;
            }
            
            if (isset($rowData['existing_student']) && $rowData['existing_student']) {
                // Actualizar estudiante existente
                $student = Student::find($rowData['existing_student_id']);
                $student->update($studentData);
            } else {
                // Crear nuevo estudiante
                $studentData['codigo'] = $this->generateUniqueCode();
                $studentData['empresa_id'] = Auth::user()->empresa_id;
                $studentData['sucursal_id'] = Auth::user()->sucursal_id;
                
                Log::info('Intentando crear estudiante', ['student_data' => $studentData]);
                Student::create($studentData);
            }
            
            $this->importedRows++;
            
        } catch (\Exception $e) {
            $this->failedRows++;
            $this->errorsList[] = [
                'row' => $rowIndex + 1,
                'error' => $e->getMessage(),
                'data' => $rowData
            ];
            Log::error('Error al procesar fila ' . ($rowIndex + 1) . ': ' . $e->getMessage(), [
                'row_data' => $rowData,
                'student_data' => $studentData ?? null,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function prepareStudentData($rowData)
    {
        $studentData = [];
        
        // Mapear campos básicos
        $fieldMapping = [
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'documento_identidad' => 'documento_identidad',
            'fecha_nacimiento' => 'fecha_nacimiento',
            'grado' => 'grado',
            'seccion' => 'seccion',
            'correo_electronico' => 'correo_electronico',
            'representante_nombres' => 'representante_nombres',
            'representante_apellidos' => 'representante_apellidos',
            'representante_documento_identidad' => 'representante_documento_identidad',
            'representante_telefonos' => 'representante_telefonos',
            'representante_correo' => 'representante_correo'
        ];
        
        foreach ($fieldMapping as $importField => $dbField) {
            if (isset($rowData[$importField])) {
                // Filtrar valor inválido primero
                $filteredValue = $this->filterInvalidValue($rowData[$importField]);
                
                // Manejo especial para campos de fecha
                if ($dbField === 'fecha_nacimiento') {
                    $studentData[$dbField] = $this->parseDateSafely($filteredValue);
                } else {
                    $studentData[$dbField] = $filteredValue;
                }
            }
        }
        
        // Asegurar que fecha_nacimiento sea null o una fecha válida antes de guardar
        if (isset($studentData['fecha_nacimiento']) && $studentData['fecha_nacimiento'] === null) {
            Log::info('Fecha de nacimiento establecida como null, será guardada como null en BD');
        }
        
        // Procesar nivel_educativo
        if (isset($rowData['nivel_educativo']) && !empty($rowData['nivel_educativo']) && $rowData['nivel_educativo'] !== 'n/a') {
            $nivel = EducationalLevel::where('nombre', 'like', '%' . $rowData['nivel_educativo'] . '%')
                ->where('empresa_id', Auth::user()->empresa_id)
                ->first();
            if ($nivel) {
                $studentData['nivel_educativo_id'] = $nivel->id;
                Log::info('Nivel educativo encontrado', ['nivel_id' => $nivel->id, 'nombre' => $nivel->nombre]);
            }
        }
        
        // Si no se encontró nivel educativo, asignar el primero disponible
        if (!isset($studentData['nivel_educativo_id'])) {
            $defaultNivel = EducationalLevel::where('empresa_id', Auth::user()->empresa_id)->first();
            if ($defaultNivel) {
                $studentData['nivel_educativo_id'] = $defaultNivel->id;
                Log::info('Asignado nivel educativo por defecto', ['nivel_id' => $defaultNivel->id, 'nombre' => $defaultNivel->nombre]);
            } else {
                throw new \Exception('No hay niveles educativos disponibles en el sistema');
            }
        }
        
        // Procesar turno
        if (isset($rowData['turno']) && !empty($rowData['turno']) && $rowData['turno'] !== 'n/a') {
            $turno = Turno::where('nombre', 'like', '%' . $rowData['turno'] . '%')
                ->where('empresa_id', Auth::user()->empresa_id)
                ->first();
            if ($turno) {
                $studentData['turno_id'] = $turno->id;
            }
        }
        
        // Si no se encontró turno, asignar el primero disponible
        if (!isset($studentData['turno_id'])) {
            $defaultTurno = Turno::where('empresa_id', Auth::user()->empresa_id)->first();
            if ($defaultTurno) {
                $studentData['turno_id'] = $defaultTurno->id;
            }
        }
        
        // Procesar school_period
        if (isset($rowData['school_period']) && !empty($rowData['school_period']) && $rowData['school_period'] !== 'n/a') {
            $periodo = SchoolPeriod::where('name', 'like', '%' . $rowData['school_period'] . '%')
                ->where('empresa_id', Auth::user()->empresa_id)
                ->first();
            if ($periodo) {
                $studentData['school_periods_id'] = $periodo->id;
            }
        }
        
        // Si no se encontró período escolar, asignar el activo o el último
        if (!isset($studentData['school_periods_id'])) {
            $defaultPeriod = SchoolPeriod::where('empresa_id', Auth::user()->empresa_id)
                ->where('is_active', true)
                ->first();
            
            if (!$defaultPeriod) {
                $defaultPeriod = SchoolPeriod::where('empresa_id', Auth::user()->empresa_id)
                    ->orderBy('id', 'desc')
                    ->first();
            }
            
            if ($defaultPeriod) {
                $studentData['school_periods_id'] = $defaultPeriod->id;
            } else {
                throw new \Exception('No hay períodos escolares disponibles en el sistema');
            }
        }
        
        Log::info('Datos del estudiante preparados', [
            'nivel_educativo_id' => $studentData['nivel_educativo_id'] ?? 'NO ASIGNADO',
            'turno_id' => $studentData['turno_id'] ?? 'NO ASIGNADO',
            'school_periods_id' => $studentData['school_periods_id'] ?? 'NO ASIGNADO'
        ]);
        
        return $studentData;
    }

    private function mapRowData($data)
    {
        $mappedData = [];
        
        foreach ($this->columnMapping as $field => $columnIndex) {
            if ($columnIndex !== '' && isset($data[$columnIndex])) {
                $value = $data[$columnIndex];
                
                // Filtrar valores "n/a" y similares
                $filteredValue = $this->filterInvalidValue($value);
                
                // Procesamiento especial para ciertos campos
                switch ($field) {
                    case 'fecha_nacimiento':
                        $mappedData[$field] = $this->parseDateSafely($filteredValue);
                        break;
                        
                    case 'representante_telefonos':
                        if ($filteredValue) {
                            // Convertir a array si es una lista separada por comas
                            $mappedData[$field] = explode(',', $filteredValue);
                        }
                        break;
                        
                    case 'nivel_educativo':
                        if ($filteredValue) {
                            // Buscar nivel educativo por nombre
                            $nivel = EducationalLevel::where('nombre', 'like', "%{$filteredValue}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($nivel) {
                                $mappedData['nivel_educativo_id'] = $nivel->id;
                            }
                            else
                            {
                                 $mappedData['nivel_educativo_id'] = 1;
                            }
                        }
                        break;
                        
                    case 'turno':
                        if ($filteredValue) {
                            // Buscar turno por nombre
                            $turno = Turno::where('nombre', 'like', "%{$filteredValue}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($turno) {
                                $mappedData['turno_id'] = $turno->id;
                            }
                        }
                        break;
                        
                    case 'school_period':
                        if ($filteredValue) {
                            // Buscar período escolar por nombre
                            $period = SchoolPeriod::where('name', 'like', "%{$filteredValue}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($period) {
                                $mappedData['school_periods_id'] = $period->id;
                            }
                        }
                        break;
                        
                    default:
                        $mappedData[$field] = $filteredValue;
                        break;
                }
            }
        }
        
        Log::info('Mapeo de datos completado', [
            'fila_original' => $data,
            'datos_mapeados' => $mappedData
        ]);
        
        return $mappedData;
    }

    private function hasMinimumRequiredData($studentData)
    {
        // Verificar que al menos haya uno de estos campos básicos
        $hasBasicData = !empty($studentData['nombres']) || 
                       !empty($studentData['apellidos']) || 
                       !empty($studentData['documento_identidad']);
        
        Log::info('Validando datos mínimos', [
            'nombres' => !empty($studentData['nombres']),
            'apellidos' => !empty($studentData['apellidos']),
            'documento' => !empty($studentData['documento_identidad']),
            'has_basic_data' => $hasBasicData
        ]);
        
        return $hasBasicData;
    }

    private function filterInvalidValue($value)
    {
        if (!$value) {
            return null;
        }
        
        $invalidValues = ['n/a', 'na', 'null', '', ' ', '0', 'undefined', 'N/A', 'NA', 'NULL'];
        $trimmedValue = trim($value);
        
        if (in_array(strtolower($trimmedValue), $invalidValues)) {
            Log::info('Valor inválido filtrado', ['original' => $value, 'filtrado' => null]);
            return null;
        }
        
        return $trimmedValue;
    }

    private function parseDateSafely($dateValue)
    {
        if (!$dateValue) {
            return null;
        }
        
        $invalidValues = ['n/a', 'na', 'null', '', ' ', '0', 'undefined'];
        if (in_array(strtolower(trim($dateValue)), $invalidValues)) {
            Log::info('Valor de fecha inválido detectado', ['valor' => $dateValue]);
            return null;
        }
        
        // Detectar si es un número serial de Excel (número entero o decimal)
        if (is_numeric($dateValue)) {
            Log::info('Detectado número serial de Excel', ['valor' => $dateValue]);
            try {
                // La fecha base de Excel es 1900-01-01
                $baseDate = Carbon::create(1900, 1, 1);
                
                // Convertir el número a días y fracción de día (para horas/minutos)
                $days = (float)$dateValue;
                
                // Restar 2 días por el bug de Excel con el año 1900
                $days = $days - 2;
                
                // Crear la fecha sumando los días
                $excelDate = $baseDate->copy()->addDays($days);
                
                Log::info('Fecha Excel convertida', [
                    'original' => $dateValue, 
                    'dias' => $days,
                    'convertida' => $excelDate->format('Y-m-d')
                ]);
                return $excelDate->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning('Error convirtiendo número serial de Excel', ['valor' => $dateValue, 'error' => $e->getMessage()]);
                return null;
            }
        }
        
        // Si no es numérico, intentar parsear como fecha normal
        try {
            return Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Error parseando fecha', ['valor' => $dateValue, 'error' => $e->getMessage()]);
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
        $this->reset(['file', 'preview', 'importing', 'imported', 'totalRows', 'importedRows', 'failedRows', 'errorsList', 'importProgress']);
        $this->columnMapping = [
            'nombres' => '',
            'apellidos' => '',
            'fecha_nacimiento' => '',
            'documento_identidad' => '',
            'grado' => '',
            'seccion' => '',
            'nivel_educativo' => '',
            'turno' => '',
            'school_period' => '',
            'correo_electronico' => '',
            'representante_nombres' => '',
            'representante_apellidos' => '',
            'representante_documento_identidad' => '',
            'representante_telefonos' => '',
            'representante_correo' => '',
        ];
    }
}