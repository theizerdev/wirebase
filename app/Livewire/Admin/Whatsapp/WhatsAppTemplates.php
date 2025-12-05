<?php

namespace App\Livewire\Admin\Whatsapp;

use App\Models\WhatsAppTemplate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class WhatsAppTemplates extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $content = '';
    public $category = 'notification';
    public $is_active = true;
    public $editingTemplate = null;
    public $showModal = false;
    public $deleteModal = false;
    public $templateToDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:500',
        'content' => 'required|string|max:2000',
        'category' => 'required|in:notification,reminder,marketing,alert',
        'is_active' => 'boolean'
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->content = '';
        $this->category = 'notification';
        $this->is_active = true;
        $this->editingTemplate = null;
        $this->showModal = false;
        $this->deleteModal = false;
        $this->templateToDelete = null;
    }

    public function createTemplate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editTemplate($templateId)
    {
        $template = WhatsAppTemplate::findOrFail($templateId);
        
        $this->editingTemplate = $templateId;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->content = $template->content;
        $this->category = $template->category;
        $this->is_active = $template->is_active;
        $this->showModal = true;
    }

    public function saveTemplate()
    {
        $this->validate();

        try {
            if ($this->editingTemplate) {
                $template = WhatsAppTemplate::findOrFail($this->editingTemplate);
                $template->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'content' => $this->content,
                    'category' => $this->category,
                    'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Plantilla actualizada correctamente.');
            } else {
                WhatsAppTemplate::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'content' => $this->content,
                    'category' => $this->category,
                    'is_active' => $this->is_active,
                    'created_by' => auth()->id(),
                    'variables' => $this->extractVariables($this->content)
                ]);
                session()->flash('success', 'Plantilla creada correctamente.');
            }

            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la plantilla: ' . $e->getMessage());
        }
    }

    public function confirmDelete($templateId)
    {
        $this->templateToDelete = $templateId;
        $this->deleteModal = true;
    }

    public function deleteTemplate()
    {
        try {
            $template = WhatsAppTemplate::findOrFail($this->templateToDelete);
            $template->delete();
            session()->flash('success', 'Plantilla eliminada correctamente.');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la plantilla: ' . $e->getMessage());
        }
    }

    public function toggleStatus($templateId)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($templateId);
            $template->update(['is_active' => !$template->is_active]);
            session()->flash('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    private function extractVariables($content)
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
        return $matches[1] ?? [];
    }

    public function getTemplatesProperty()
    {
        return WhatsAppTemplate::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.whatsapp-templates', [
            'templates' => $this->templates
        ])->extends('components.layouts.admin')->section('content');
    }

    public function getPageTitle()
    {
        return 'Plantillas WhatsApp';
    }

    public function getBreadcrumbs()
    {
        return [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'WhatsApp', 'url' => route('admin.whatsapp.dashboard')],
            ['title' => 'Plantillas', 'url' => route('admin.whatsapp.templates.index')]
        ];
    }
}