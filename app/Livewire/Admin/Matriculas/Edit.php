<?php

namespace App\Livewire\Admin\Matriculas;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;
use App\Models\PaymentSchedule;

class Edit extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;


    public $matricula;
    public $student_id;
    public $programa_id;
    public $periodo_id;
    public $fecha_matricula;
    public $estado;

    // Campos de costos
    public $costo;
    public $cuota_inicial;
    public $numero_cuotas;

    public $students = [];
    public $programas = [];
    public $periodos = [];

    // Búsqueda de estudiantes
    public $searchStudent = '';
    public $showStudentDropdown = false;
    public $selectedStudent = null;

    // Tabla de amortización
    public $paymentSchedule = [];
    public $showSchedule = false;

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'programa_id' => 'required|exists:programas,id',
        'periodo_id' => 'required|exists:school_periods,id',
        'fecha_matricula' => 'required|date',
        'estado' => 'required|in:activo,inactivo,graduado',
        'costo' => 'required|numeric|min:0',
        'cuota_inicial' => 'required|numeric|min:0',
        'numero_cuotas' => 'required|integer|min:0'
    ];

    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula->load(['student', 'programa', 'periodo']);
        $this->student_id = $matricula->student_id;
        $this->programa_id = $matricula->programa_id;
        $this->periodo_id = $matricula->periodo_id;
        $this->fecha_matricula = $matricula->fecha_matricula ? $matricula->fecha_matricula->format('Y-m-d') : now()->format('Y-m-d');
        $this->estado = $matricula->estado ?? 'activo';
        $this->costo = $matricula->costo ?? 0;
        $this->cuota_inicial = $matricula->cuota_inicial ?? 0;
        $this->numero_cuotas = $matricula->numero_cuotas ?? 0;

        // Configurar estudiante seleccionado
        $this->selectedStudent = $matricula->student;
        $this->searchStudent = $matricula->student ? $matricula->student->nombres . ' ' . $matricula->student->apellidos : '';

        // Asegurar que el student_id esté correctamente establecido
        if (!$this->student_id && $matricula->student) {
            $this->student_id = $matricula->student->id;
        }

        $this->loadData();
        $this->loadPaymentSchedule();
        
        // Verificar si el período existe
        if ($this->periodo_id && !SchoolPeriod::find($this->periodo_id)) {
            session()->flash('warning', 'El período escolar asociado a esta matrícula no está disponible. Por favor, seleccione un período válido.');
        }
    }

    public function loadData()
    {
        $query = Student::query();

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        // Cargar estudiantes disponibles más el estudiante actual
        $this->students = $query->where(function($q) {
                $q->whereDoesntHave('matriculas')
                  ->orWhere('id', $this->student_id);
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        $programaQuery = Programa::where('activo', true);
        if (!auth()->user()->hasRole('Super Administrador')) {
            $programaQuery->where('empresa_id', auth()->user()->empresa_id)
                         ->where('sucursal_id', auth()->user()->sucursal_id);
        }
        $this->programas = $programaQuery->orderBy('nombre')->get();

        // Cargar períodos, asegurando que el período actual esté incluido
        $periodoQuery = SchoolPeriod::query();
        
        // Si hay un período seleccionado, asegurarnos de que esté en la lista
        if ($this->periodo_id) {
            $periodoActual = SchoolPeriod::find($this->periodo_id);
            if ($periodoActual) {
                // Obtener todos los períodos ordenados, incluyendo el actual
                $this->periodos = $periodoQuery->orderBy('name')->get();
            } else {
                $this->periodos = collect(); // Lista vacía si el período no existe
            }
        } else {
            $this->periodos = $periodoQuery->orderBy('name')->get();
        }
    }

    public function updatingStudentId($value)
    {
        // En modo edición, proteger el student_id original
        if ($this->matricula && $this->matricula->exists) {
            $this->student_id = $this->matricula->student_id;
            return;
        }
    }

    public function updatedSearchStudent()
    {
        // En modo edición, no permitir búsqueda de estudiantes
        if ($this->matricula && $this->matricula->exists) {
            return;
        }
        
        if (strlen($this->searchStudent) >= 2) {
            $query = Student::query();

            if (!auth()->user()->hasRole('Super Administrador')) {
                $query->where('empresa_id', auth()->user()->empresa_id)
                      ->where('sucursal_id', auth()->user()->sucursal_id);
            }

            $this->students = $query->where(function($q) {
                    $q->where('nombres', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('documento_identidad', 'like', '%' . $this->searchStudent . '%');
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->limit(10)
                ->get();
            $this->showStudentDropdown = true;
        } else {
            $this->students = [];
            $this->showStudentDropdown = false;
        }
    }

    public function selectStudent($studentId)
    {
        // En modo edición, no permitir cambiar el estudiante
        if ($this->matricula && $this->matricula->exists) {
            return;
        }
        
        $student = Student::with('nivelEducativo')->find($studentId);
        if ($student) {
            $this->selectedStudent = $student;
            $this->student_id = $student->id;
            $this->searchStudent = $student->nombres . ' ' . $student->apellidos;
            $this->showStudentDropdown = false;
            
            // Auto-completar costos del nivel educativo
            if ($student->nivelEducativo) {
                $this->costo = $student->nivelEducativo->costo_matricula ?? $this->costo;
                $this->cuota_inicial = $student->nivelEducativo->cuota_inicial ?? $this->cuota_inicial;
                $this->numero_cuotas = $student->nivelEducativo->numero_cuotas ?? $this->numero_cuotas;
            }
            
            $this->generatePaymentSchedule();
        }
    }

    public function clearStudentSelection()
    {
        // En modo edición, no permitir limpiar la selección de estudiante
        if ($this->matricula && $this->matricula->exists) {
            return;
        }
        
        $this->selectedStudent = null;
        $this->student_id = null;
        $this->searchStudent = '';
        $this->showStudentDropdown = false;
        $this->students = [];
        $this->generatePaymentSchedule();
    }

    public function updateCostos()
    {
        if ($this->student_id && $this->programa_id) {
            $student = Student::find($this->student_id);
            $programa = Programa::find($this->programa_id);
            
            if ($student && $student->nivelEducativo && $programa) {
                $this->costo = $student->nivelEducativo->costo ?? 0;
                $this->cuota_inicial = $student->nivelEducativo->cuota_inicial ?? 0;
                $this->numero_cuotas = $student->nivelEducativo->numero_cuotas ?? 1;
                $this->generatePaymentSchedule();
            }
        }
    }

    public function loadPaymentSchedule()
    {
        if (class_exists('\App\Models\PaymentSchedule')) {
            $this->paymentSchedule = $this->matricula->paymentSchedules()
                ->orderBy('numero_cuota')
                ->get()
                ->map(function ($schedule) {
                    return [
                        'numero_cuota' => $schedule->numero_cuota,
                        'descripcion' => $schedule->numero_cuota == 0 ? 'Cuota inicial' : 'Cuota ' . $schedule->numero_cuota,
                        'monto' => $schedule->monto,
                        'fecha_vencimiento' => $schedule->fecha_vencimiento,
                        'estado' => $schedule->estado ?? 'pendiente'
                    ];
                })
                ->toArray();
        } else {
            $this->paymentSchedule = [];
        }

        $this->showSchedule = count($this->paymentSchedule) > 0;

        // Si no hay cronograma existente, generar uno nuevo
        if (count($this->paymentSchedule) == 0) {
            $this->generatePaymentSchedule();
        }
    }

    public function updatedCosto()
    {
        $this->generatePaymentSchedule();
    }

    public function updatedCuotaInicial()
    {
        $this->generatePaymentSchedule();
    }

    public function updatedNumeroCuotas()
    {
        $this->generatePaymentSchedule();
    }

    public function updatedPeriodoId()
    {
        $this->generatePaymentSchedule();
    }

    public function generatePaymentSchedule()
    {
        // Solo generar si tenemos todos los datos necesarios
        if (!$this->costo || !$this->periodo_id) {
            $this->paymentSchedule = [];
            $this->showSchedule = false;
            return;
        }

        $periodo = SchoolPeriod::find($this->periodo_id);
        if (!$periodo) {
            $this->paymentSchedule = [];
            $this->showSchedule = false;
            return;
        }

        // Calcular monto restante después de la cuota inicial
        $montoRestante = $this->costo - $this->cuota_inicial;

        // Si no hay cuotas, todo se cobra en la cuota inicial
        if ($this->numero_cuotas <= 0) {
            $this->paymentSchedule = [
                [
                    'numero_cuota' => 1,
                    'descripcion' => 'Pago único',
                    'monto' => $this->costo,
                    'fecha_vencimiento' => $periodo->start_date
                ]
            ];
            $this->showSchedule = true;
            return;
        }

        // Calcular monto por cuota
        $montoCuota = $montoRestante / $this->numero_cuotas;

        // Generar cuotas mensuales
        $this->paymentSchedule = [];

        // Agregar cuota inicial
        if ($this->cuota_inicial > 0) {
            $this->paymentSchedule[] = [
                'numero_cuota' => 0,
                'descripcion' => 'Cuota inicial',
                'monto' => $this->cuota_inicial,
                'fecha_vencimiento' => $periodo->start_date
            ];
        }

        // Agregar cuotas distribuidas uniformemente a lo largo del período escolar
        $startDate = new \DateTime($periodo->start_date);
        $endDate = new \DateTime($periodo->end_date);

        // Calcular intervalo total en días
        $totalDays = $startDate->diff($endDate)->days;

        // Para cada cuota, calcular la fecha de vencimiento distribuida uniformemente
        for ($i = 1; $i <= $this->numero_cuotas; $i++) {
            $dueDate = clone $startDate;

            // Calcular días entre cuotas (distribución uniforme)
            if ($this->numero_cuotas > 1) {
                $daysBetweenPayments = floor($totalDays / ($this->numero_cuotas - 1));
                $dueDate->modify('+' . ($daysBetweenPayments * ($i - 1)) . ' days');
            } else {
                // Si solo hay una cuota, colocarla a la mitad del período
                $daysBetweenPayments = floor($totalDays / 2);
                $dueDate->modify('+' . $daysBetweenPayments . ' days');
            }

            // Asegurarse de que la fecha no exceda la fecha final
            if ($dueDate > $endDate) {
                $dueDate = clone $endDate;
            }

            $this->paymentSchedule[] = [
                'numero_cuota' => $i,
                'descripcion' => 'Cuota ' . $i,
                'monto' => round($montoCuota, 2),
                'fecha_vencimiento' => $dueDate->format('Y-m-d')
            ];
        }

        $this->showSchedule = true;
    }

    public function update()
    {
        // Verificar permiso para editar matrículas
        if (!auth()->user()->can('edit matriculas')) {
            session()->flash('error', 'No tienes permiso para editar matrículas.');
            return;
        }

        // Asegurar que el student_id se mantiene en modo edición
        if ($this->matricula && $this->matricula->exists && !$this->student_id) {
            $this->student_id = $this->matricula->student_id;
        }

        $this->validate();

        try {
            $this->matricula->update([
                'student_id' => $this->student_id,
                'programa_id' => $this->programa_id,
                'periodo_id' => $this->periodo_id,
                'fecha_matricula' => $this->fecha_matricula,
                'estado' => $this->estado,
                'costo' => $this->costo,
                'cuota_inicial' => $this->cuota_inicial,
                'numero_cuotas' => $this->numero_cuotas
            ]);

            // Actualizar cronograma de pagos
            $this->updatePaymentSchedule();

            session()->flash('message', 'Matrícula actualizada correctamente.');
            return redirect()->route('admin.matriculas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la matrícula: ' . $e->getMessage());
        }
    }

    private function updatePaymentSchedule()
    {
        if (!class_exists('\App\Models\PaymentSchedule')) {
            return;
        }

        // Solo eliminar cronograma si no hay pagos registrados
        $existingSchedules = $this->matricula->paymentSchedules();
        $hasPayments = $existingSchedules->where('monto_pagado', '>', 0)->exists();

        if (!$hasPayments) {
            // Eliminar cronograma existente solo si no hay pagos
            $existingSchedules->delete();

            // Crear nuevo cronograma
            foreach ($this->paymentSchedule as $schedule) {
                PaymentSchedule::create([
                    'matricula_id' => $this->matricula->id,
                    'numero_cuota' => $schedule['numero_cuota'],
                    'monto' => $schedule['monto'],
                    'fecha_vencimiento' => $schedule['fecha_vencimiento'],
                    'estado' => 'pendiente',
                    'empresa_id' => auth()->user()->empresa_id ?? 1,
                    'sucursal_id' => auth()->user()->sucursal_id ?? 1,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.matriculas.edit')->layout($this->getLayout());
    }
}