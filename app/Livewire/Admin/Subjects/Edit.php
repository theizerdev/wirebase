<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Subject;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Edit extends Component
{
    use HasDynamicLayout;

    public Subject $subject;
    public $name = '';
    public $code = '';
    public $description = '';
    public $credits = 0;
    public $hours_per_week = 0;
    public $program_id = '';
    public $educational_level_id = '';
    public $is_active = true;

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
        $this->name = $subject->name;
        $this->code = $subject->code;
        $this->description = $subject->description;
        $this->credits = $subject->credits;
        $this->hours_per_week = $subject->hours_per_week;
        $this->program_id = $subject->program_id;
        $this->educational_level_id = $subject->educational_level_id;
        $this->is_active = $subject->is_active;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,' . $this->subject->id,
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
        if (!auth()->user()->can('edit subjects')) {
            session()->flash('error', 'No tienes permiso para editar materias.');
            return redirect()->route('admin.subjects.index');
        }

        $this->validate();

        try {
            $this->subject->update([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'credits' => $this->credits,
                'hours_per_week' => $this->hours_per_week,
                'program_id' => $this->program_id,
                'educational_level_id' => $this->educational_level_id,
                'is_active' => $this->is_active,
                'updated_by' => auth()->id()
            ]);

            session()->flash('message', 'Materia actualizada correctamente.');
            return redirect()->route('admin.subjects.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la materia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $programs = Programa::orderBy('nombre')->get();
        $educationalLevels = NivelEducativo::orderBy('nombre')->get();

        return view('livewire.admin.subjects.edit', compact('programs', 'educationalLevels'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Editar Materia';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.subjects.index' => 'Materias',
            '#' => 'Editar'
        ];
    }
}