<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Subject;
use App\Models\Programa;
use App\Models\NivelEducativo;
use Illuminate\Validation\Rule;

class Create extends Component
{
    use HasDynamicLayout;

    public $name = '';
    public $code = '';
    public $description = '';
    public $credits = 0;
    public $hours_per_week = 0;
    public $program_id = '';
    public $educational_level_id = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:0',
            'hours_per_week' => 'required|integer|min:0',
            'program_id' => 'required|exists:programas,id',
            'educational_level_id' => 'required|exists:niveles_educativos,id',
            'is_active' => 'boolean'
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre es requerido.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'code.required' => 'El código es requerido.',
            'code.max' => 'El código no puede exceder 20 caracteres.',
            'code.unique' => 'Este código ya está en uso.',
            'credits.required' => 'Los créditos son requeridos.',
            'credits.integer' => 'Los créditos deben ser un número entero.',
            'credits.min' => 'Los créditos no pueden ser negativos.',
            'hours_per_week.required' => 'Las horas por semana son requeridas.',
            'hours_per_week.integer' => 'Las horas por semana deben ser un número entero.',
            'hours_per_week.min' => 'Las horas por semana no pueden ser negativas.',
            'program_id.required' => 'El programa es requerido.',
            'program_id.exists' => 'El programa seleccionado no existe.',
            'educational_level_id.required' => 'El nivel educativo es requerido.',
            'educational_level_id.exists' => 'El nivel educativo seleccionado no existe.'
        ];
    }

    public function save()
    {
        if (!auth()->user()->can('create subjects')) {
            session()->flash('error', 'No tienes permiso para crear materias.');
            return redirect()->route('admin.subjects.index');
        }

        $this->validate();

        try {
            $subject = Subject::create([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'credits' => $this->credits,
                'hours_per_week' => $this->hours_per_week,
                'program_id' => $this->program_id,
                'educational_level_id' => $this->educational_level_id,
                'is_active' => $this->is_active,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            session()->flash('message', 'Materia creada correctamente.');
            return redirect()->route('admin.subjects.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la materia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $programs = Programa::orderBy('nombre')->get();
        $educationalLevels = NivelEducativo::orderBy('nombre')->get();

        return view('livewire.admin.subjects.create', compact('programs', 'educationalLevels'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Crear Materia';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.subjects.index' => 'Materias',
            '#' => 'Crear'
        ];
    }
}