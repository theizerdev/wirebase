<?php

namespace App\Livewire\Admin\Evaluations;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationType;

class Edit extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public Evaluation $evaluation;

    public $subject_id = '';
    public $teacher_id = '';
    public $evaluation_period_id = '';
    public $evaluation_type_id = '';
    public $name = '';
    public $description = '';
    public $evaluation_date = '';
    public $max_score = 20;
    public $weight = 100;
    public $is_active = true;
    public $is_published = false;

    public function mount(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
        $this->subject_id = $evaluation->subject_id;
        $this->teacher_id = $evaluation->teacher_id;
        $this->evaluation_period_id = $evaluation->evaluation_period_id;
        $this->evaluation_type_id = $evaluation->evaluation_type_id;
        $this->name = $evaluation->name;
        $this->description = $evaluation->description;
        $this->evaluation_date = $evaluation->evaluation_date->format('Y-m-d');
        $this->max_score = $evaluation->max_score;
        $this->weight = $evaluation->weight;
        $this->is_active = $evaluation->is_active;
        $this->is_published = $evaluation->is_published;
    }

    protected function rules()
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'evaluation_period_id' => 'required|exists:evaluation_periods,id',
            'evaluation_type_id' => 'required|exists:evaluation_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'evaluation_date' => 'required|date',
            'max_score' => 'required|numeric|min:1|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function save()
    {
        if (!auth()->user()->can('edit evaluations')) {
            session()->flash('error', 'No tienes permiso para editar evaluaciones.');
            return;
        }

        $this->validate();

        try {
            $this->evaluation->update([
                'subject_id' => $this->subject_id,
                'teacher_id' => $this->teacher_id,
                'evaluation_period_id' => $this->evaluation_period_id,
                'evaluation_type_id' => $this->evaluation_type_id,
                'name' => $this->name,
                'description' => $this->description,
                'evaluation_date' => $this->evaluation_date,
                'max_score' => $this->max_score,
                'weight' => $this->weight,
                'is_active' => $this->is_active,
                'is_published' => $this->is_published,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('message', 'Evaluación actualizada correctamente.');
            return redirect()->route('admin.evaluations.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $teachers = Teacher::active()->with('user')->get();
        $evaluationPeriods = EvaluationPeriod::orderBy('number')->get();
        $evaluationTypes = EvaluationType::active()->orderBy('name')->get();

        return view('livewire.admin.evaluations.edit', compact(
            'subjects', 'teachers', 'evaluationPeriods', 'evaluationTypes'
        ))->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Editar Evaluación';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluations.index' => 'Evaluaciones',
            'admin.evaluations.edit' => 'Editar'
        ];
    }
}
