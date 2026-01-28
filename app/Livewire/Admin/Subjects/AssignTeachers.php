<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignTeachers extends Component
{
    use HasDynamicLayout;

    public Subject $subject;
    public $availableTeachers = [];
    public $selectedTeachers = [];
    public $primaryTeacherId = null;
    public $academicPeriod = '';

    protected $rules = [
        'selectedTeachers' => 'required|array|min:1',
        'selectedTeachers.*' => 'exists:teachers,id',
        'primaryTeacherId' => 'required|exists:teachers,id',
        'academicPeriod' => 'required|string|max:50'
    ];

    protected $messages = [
        'selectedTeachers.required' => 'Debe seleccionar al menos un profesor.',
        'primaryTeacherId.required' => 'Debe seleccionar un profesor principal.',
        'academicPeriod.required' => 'El período académico es obligatorio.'
    ];

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
        $this->academicPeriod = date('Y') . '-' . (date('Y') + 1); // Default academic period
        $this->loadTeachers();
        $this->loadExistingAssignments();
    }

    public function loadTeachers()
    {
        $this->availableTeachers = Teacher::where('is_active', true)
            ->with('user')
            ->get();
    }

    public function loadExistingAssignments()
    {
        $existingAssignments = $this->subject->teachers()
            ->wherePivot('academic_period', $this->academicPeriod)
            ->get();

        $this->selectedTeachers = $existingAssignments->pluck('id')->toArray();
        $primaryTeacher = $existingAssignments->where('pivot.is_primary', true)->first();
        $this->primaryTeacherId = $primaryTeacher ? $primaryTeacher->id : null;
    }

    public function updatedAcademicPeriod()
    {
        $this->loadExistingAssignments();
    }

    public function updatedSelectedTeachers()
    {
        if (!in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->primaryTeacherId = null;
        }
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedTeachers) === count($this->availableTeachers)) {
            $this->selectedTeachers = [];
            $this->primaryTeacherId = null;
        } else {
            $this->selectedTeachers = $this->availableTeachers->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();

        if (!in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->addError('primaryTeacherId', 'El profesor principal debe estar seleccionado.');
            return;
        }

        try {
            DB::transaction(function () {
                // Remove existing assignments for this academic period
                $this->subject->teachers()
                    ->wherePivot('academic_period', $this->academicPeriod)
                    ->detach();

                // Attach new assignments
                $attachData = [];
                foreach ($this->selectedTeachers as $teacherId) {
                    $attachData[$teacherId] = [
                        'assigned_date' => now(),
                        'academic_period' => $this->academicPeriod,
                        'is_primary' => ($teacherId == $this->primaryTeacherId),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $this->subject->teachers()->attach($attachData);
            });

            session()->flash('message', 'Profesores asignados correctamente.');
            return redirect()->route('admin.subjects.show', $this->subject);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al asignar profesores: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.subjects.assign-teachers')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Asignar Profesores: ' . $this->subject->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.subjects.index' => 'Materias',
            'admin.subjects.show' => 'Detalles',
            '#' => 'Asignar Profesores'
        ];
    }
}