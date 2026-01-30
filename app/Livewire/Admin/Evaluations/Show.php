<?php

namespace App\Livewire\Admin\Evaluations;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Evaluation;
use App\Models\Grade;

class Show extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public Evaluation $evaluation;

    public function mount(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation->load([
            'subject', 
            'teacher.user', 
            'evaluationPeriod.schoolPeriod', 
            'evaluationType',
            'grades.student',
            'createdBy',
            'updatedBy'
        ]);
    }

    public function getStatsProperty()
    {
        return [
            'total_students' => $this->evaluation->grades->count(),
            'graded' => $this->evaluation->grades->where('status', 'graded')->count(),
            'pending' => $this->evaluation->grades->where('status', 'pending')->count(),
            'absent' => $this->evaluation->grades->where('status', 'absent')->count(),
            'average' => $this->evaluation->grades->where('status', 'graded')->avg('score'),
            'max' => $this->evaluation->grades->where('status', 'graded')->max('score'),
            'min' => $this->evaluation->grades->where('status', 'graded')->min('score'),
        ];
    }

    public function render()
    {
        return view('livewire.admin.evaluations.show')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Detalle de Evaluación';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluations.index' => 'Evaluaciones',
            'admin.evaluations.show' => 'Detalle'
        ];
    }
}
