<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DynamicDatabaseExport;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Log;

class DatabaseExport extends Component
{
    use HasDynamicLayout;

    public $selectedTable = '';
    public $selectedColumns = [];
    public $availableColumns = [];
    public $tableColumns = [];
    public $conditions = [];
    public $exportFormat = 'xlsx';
    public $exportProgress = 0;
    public $isExporting = false;
    public $exportFileName = '';
    public $includeHeaders = true;

    // Opciones de formato
    public $formats = [
        'xlsx' => 'Excel (.xlsx)',
        'csv' => 'CSV (.csv)',
        'pdf' => 'PDF (.pdf)',
        'html' => 'HTML (.html)',
        'sql' => 'SQL (.sql)'
    ];

    // Tablas disponibles (excluir tablas del sistema)
    public $availableTables = [];

    // Relaciones comunes
    public $availableRelations = [];
    public $selectedRelations = [];

    protected $rules = [
        'selectedTable' => 'required|string',
        'exportFormat' => 'required|in:xlsx,csv,pdf,html,sql',
        'exportFileName' => 'nullable|string|max:255'
    ];

    public function mount()
    {
        $this->loadAvailableTables();
        $this->conditions = [
            ['column' => '', 'operator' => '=', 'value' => '', 'logic' => 'AND']
        ];
    }

    public function render()
    {
        return view('livewire.admin.database-export-materialize')
            ->layout($this->getLayout(), [
                'title' => 'Exportar Base de Datos',
                'breadcrumb' => [
                    ['name' => 'Dashboard', 'route' => 'admin.dashboard'],
                    ['name' => 'Exportar BD', 'active' => true]
                ]
            ]);
    }

    public function loadAvailableTables()
    {
        $this->availableTables = [];
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $key = 'Tables_in_' . $databaseName;

        // Tablas a excluir (del sistema)
        $excludedTables = [
            'migrations', 'password_resets', 'password_reset_tokens',
            'personal_access_tokens', 'cache', 'cache_locks', 'jobs',
            'job_batches', 'failed_jobs', 'sessions', 'activity_log'
        ];

        foreach ($tables as $table) {
            $tableName = $table->$key;
            if (!in_array($tableName, $excludedTables)) {
                $this->availableTables[$tableName] = $this->formatTableName($tableName);
            }
        }

        asort($this->availableTables);
    }

    public function updatedSelectedTable($tableName)
    {
        if (empty($tableName)) {
            $this->reset(['availableColumns', 'tableColumns', 'selectedColumns', 'availableRelations']);
            $this->dispatch('contentChanged');
            return;
        }

        $this->loadTableColumns($tableName);
        $this->loadTableRelations($tableName);
        $this->selectedColumns = array_keys($this->availableColumns);
        $this->generateDefaultFileName();
        $this->dispatch('contentChanged');
    }

    public function updatedExportFormat($format)
    {
        // Si cambia a SQL y no hay tabla seleccionada, resetear columnas
        if ($format === 'sql' && $this->selectedTable !== '*') {
            $this->selectedColumns = [];
        }

        // Regenerar el nombre del archivo con el nuevo formato
        $this->generateDefaultFileName();
        $this->dispatch('contentChanged');
    }

    public function loadTableColumns($tableName)
    {
        $this->availableColumns = [];
        $this->tableColumns = [];

        try {
            $columns = Schema::getColumnListing($tableName);

            foreach ($columns as $column) {
                $columnType = $this->getColumnType($tableName, $column);
                $this->availableColumns[$column] = [
                    'name' => $this->formatColumnName($column),
                    'type' => $columnType,
                    'original' => $column
                ];
                $this->tableColumns[$column] = $this->formatColumnName($column);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar las columnas: ' . $e->getMessage());
        }
    }

    public function loadTableRelations($tableName)
    {
        $this->availableRelations = [];
        $modelName = $this->getModelName($tableName);

        if (class_exists($modelName)) {
            try {
                $model = new $modelName();
                $relations = $this->getModelRelations($model);

                foreach ($relations as $relation => $info) {
                    $this->availableRelations[$relation] = [
                        'name' => $this->formatColumnName($relation),
                        'type' => $info['type'],
                        'related_model' => $info['model']
                    ];
                }
            } catch (\Exception $e) {
                // Silenciar errores de relaciones
            }
        }
    }

    public function addCondition()
    {
        $this->conditions[] = ['column' => '', 'operator' => '=', 'value' => '', 'logic' => 'AND'];
        $this->dispatch('contentChanged');
    }

    public function removeCondition($index)
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);

        // Asegurar que el primer elemento no tenga operador lógico
        if (!empty($this->conditions)) {
            $this->conditions[0]['logic'] = 'AND';
        }
        
        $this->dispatch('contentChanged');
    }

    public function updatedConditions($value, $key)
    {
        list($index, $field) = explode('.', $key);

        // Si es el primer elemento y cambia a OR, forzar AND
        if ($index == 0 && $field == 'logic' && $value == 'OR') {
            $this->conditions[$index]['logic'] = 'AND';
        }
        
        $this->dispatch('contentChanged');
    }

    public function selectAllColumns()
    {
        $this->selectedColumns = array_keys($this->availableColumns);
        $this->dispatch('contentChanged');
    }

    public function deselectAllColumns()
    {
        $this->selectedColumns = [];
        $this->dispatch('contentChanged');
    }

    public function startExport()
    {
        $this->validate();

        // Validación especial para exportación SQL
        if ($this->exportFormat === 'sql') {
            return $this->exportSQL();
        }

        if (empty($this->selectedColumns)) {
            session()->flash('message', 'Por favor selecciona al menos una columna para exportar.');
            session()->flash('message_type', 'error');
            return;
        }

        $this->isExporting = true;
        $this->exportProgress = 0;

        try {
            $exportData = [
                'table' => $this->selectedTable,
                'columns' => $this->selectedColumns,
                'conditions' => array_filter($this->conditions, function($condition) {
                    return !empty($condition['column']) && !empty($condition['value']);
                }),
                'relations' => $this->selectedRelations,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id
            ];

            $fileName = $this->exportFileName ?: $this->generateDefaultFileName();

            // Simular progreso
            $this->exportProgress = 25;

            $export = new DynamicDatabaseExport($exportData);

            $this->exportProgress = 75;

            return Excel::download($export, $fileName . '.' . $this->exportFormat);

        } catch (\Exception $e) {
            session()->flash('message', 'Error al exportar: ' . $e->getMessage());
            session()->flash('message_type', 'error');
            Log::error('Error en exportación de BD: ' . $e->getMessage());
        } finally {
            $this->isExporting = false;
            $this->exportProgress = 100;
        }
    }

    public function generateDefaultFileName()
    {
        if (empty($this->selectedTable)) {
            $this->exportFileName = '';
            return;
        }

        if ($this->selectedTable === '*') {
            $databaseName = DB::getDatabaseName();
            $date = now()->format('Y-m-d-His');
            $this->exportFileName = 'database-' . str_replace('_', '-', $databaseName) . '-' . $date;
        } else {
            $tableName = str_replace('_', '-', $this->selectedTable);
            $date = now()->format('Y-m-d-His');
            $this->exportFileName = 'export-' . $tableName . '-' . $date;
        }
        
        return $this->exportFileName;
    }

    private function getColumnType($table, $column)
    {
        try {
            $columnType = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column])[0]->Type;
            return $this->formatColumnType($columnType);
        } catch (\Exception $e) {
            return 'string';
        }
    }

    private function formatColumnType($type)
    {
        if (strpos($type, 'int') !== false) return 'integer';
        if (strpos($type, 'decimal') !== false) return 'decimal';
        if (strpos($type, 'float') !== false) return 'float';
        if (strpos($type, 'double') !== false) return 'double';
        if (strpos($type, 'date') !== false) return 'date';
        if (strpos($type, 'datetime') !== false) return 'datetime';
        if (strpos($type, 'timestamp') !== false) return 'timestamp';
        if (strpos($type, 'varchar') !== false) return 'string';
        if (strpos($type, 'text') !== false) return 'text';
        if (strpos($type, 'boolean') !== false) return 'boolean';
        return 'string';
    }

    private function formatTableName($tableName)
    {
        return ucwords(str_replace('_', ' ', $tableName));
    }

    private function formatColumnName($columnName)
    {
        return ucwords(str_replace('_', ' ', $columnName));
    }

    private function getModelName($tableName)
    {
        $modelName = 'App\\Models\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));
        return rtrim($modelName, 's'); // Singular
    }

    private function getModelRelations($model)
    {
        $relations = [];
        $reflection = new \ReflectionClass($model);

        foreach ($reflection->getMethods() as $method) {
            if ($method->class !== get_class($model) || $method->getNumberOfParameters() > 0) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relatedClass = get_class($return->getRelated());
                    $relationType = class_basename(get_class($return));

                    $relations[$method->getName()] = [
                        'type' => $relationType,
                        'model' => $relatedClass
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $relations;
    }

    public function getAvailableOperators()
    {
        return [
            '=' => 'Igual',
            '!=' => 'Diferente',
            '>' => 'Mayor que',
            '<' => 'Menor que',
            '>=' => 'Mayor o igual',
            '<=' => 'Menor o igual',
            'LIKE' => 'Contiene',
            'NOT LIKE' => 'No contiene',
            'IN' => 'En lista',
            'NOT IN' => 'No en lista',
            'IS NULL' => 'Es nulo',
            'IS NOT NULL' => 'No es nulo'
        ];
    }

    /**
     * Exporta la base de datos completa o una tabla específica como archivo SQL
     */
    public function exportSQL()
    {
        $this->isExporting = true;
        $this->exportProgress = 0;

        try {
            $fileName = $this->exportFileName ?: 'database_backup_' . now()->format('Y-m-d_His');
            $fileName .= '.sql';

            // Simular progreso
            $this->exportProgress = 25;

            // Obtener el contenido SQL
            $sqlContent = $this->generateSQLDump();

            $this->exportProgress = 75;

            // Crear la respuesta para descargar el archivo SQL
            return response()->streamDownload(function() use ($sqlContent) {
                echo $sqlContent;
            }, $fileName, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (\Exception $e) {
            session()->flash('message', 'Error al generar el archivo SQL: ' . $e->getMessage());
            session()->flash('message_type', 'error');
            Log::error('Error en exportación SQL: ' . $e->getMessage());
        } finally {
            $this->isExporting = false;
            $this->exportProgress = 100;
        }
    }

    /**
     * Genera el contenido SQL del dump de la base de datos
     */
    private function generateSQLDump()
    {
        $output = [];

        // Encabezado del archivo SQL
        $output[] = "-- Exportación de Base de Datos";
        $output[] = "-- Fecha: " . now()->format('Y-m-d H:i:s');
        $output[] = "-- Sistema: Sistema de Gestión Académica";
        $output[] = "-- Usuario: " . (auth()->check() ? auth()->user()->name : 'Sistema');
        $output[] = "-- Empresa: " . (auth()->check() && auth()->user()->empresa ? auth()->user()->empresa->nombre : 'N/A');
        $output[] = "";
        $output[] = "SET FOREIGN_KEY_CHECKS = 0;";
        $output[] = "";

        // Si se seleccionó una tabla específica
        if (!empty($this->selectedTable)) {
            $output = array_merge($output, $this->getTableSQL($this->selectedTable));
        } else {
            // Exportar todas las tablas disponibles
            foreach ($this->availableTables as $table => $tableLabel) {
                $output = array_merge($output, $this->getTableSQL($table));
            }
        }

        $output[] = "";
        $output[] = "SET FOREIGN_KEY_CHECKS = 1;";

        return implode("\n", $output);
    }

    /**
     * Genera el SQL para una tabla específica
     */
    private function getTableSQL($tableName)
    {
        $output = [];

        try {
            // Estructura de la tabla
            $output[] = "--";
            $output[] = "-- Estructura de tabla para `{$tableName}`";
            $output[] = "--";
            $output[] = "DROP TABLE IF EXISTS `{$tableName}`;";

            // Obtener la estructura CREATE TABLE
            $createTable = DB::selectOne("SHOW CREATE TABLE `{$tableName}`");
            $createStatement = $createTable->{'Create Table'};
            $output[] = $createStatement . ";";
            $output[] = "";

            // Datos de la tabla
            $output[] = "--";
            $output[] = "-- Volcado de datos para la tabla `{$tableName}`";
            $output[] = "--";

            // Aplicar filtros si existen
            $query = DB::table($tableName);

            // Aplicar condiciones de filtro
            $validConditions = array_filter($this->conditions, function($condition) {
                return !empty($condition['column']) && !empty($condition['value']);
            });

            foreach ($validConditions as $condition) {
                if ($condition['operator'] === 'LIKE') {
                    $query->where($condition['column'], $condition['operator'], '%' . $condition['value'] . '%');
                } elseif (in_array($condition['operator'], ['IS NULL', 'IS NOT NULL'])) {
                    if ($condition['operator'] === 'IS NULL') {
                        $query->whereNull($condition['column']);
                    } else {
                        $query->whereNotNull($condition['column']);
                    }
                } else {
                    $query->where($condition['column'], $condition['operator'], $condition['value']);
                }
            }

            // Filtrar por empresa y sucursal si aplica
            if (Schema::hasColumn($tableName, 'empresa_id')) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }

            if (Schema::hasColumn($tableName, 'sucursal_id')) {
                $query->where('sucursal_id', auth()->user()->sucursal_id);
            }

            $records = $query->get();

            if ($records->isNotEmpty()) {
                foreach ($records as $record) {
                    $data = (array) $record;
                    $columns = array_keys($data);
                    $values = array_map(function($value) {
                        if ($value === null) {
                            return 'NULL';
                        } elseif (is_numeric($value)) {
                            return $value;
                        } else {
                            return "'" . addslashes($value) . "'";
                        }
                    }, array_values($data));

                    $sql = "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");";
                    $output[] = $sql;
                }
            }

            $output[] = "";

        } catch (\Exception $e) {
            $output[] = "-- Error al procesar la tabla {$tableName}: " . $e->getMessage();
            $output[] = "";
        }

        return $output;
    }
}
