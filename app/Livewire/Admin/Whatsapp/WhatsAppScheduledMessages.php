<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WhatsAppScheduledMessage;
use App\Models\WhatsAppTemplate;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppScheduledMessages extends Component
{
    use WithPagination;

    // Properties
    public $recipient = '';
    public $message = '';
    public $selectedTemplate = '';
    public $templateVariables = [];
    public $scheduledDate = '';
    public $scheduledTime = '';
    public $timezone = 'America/Caracas';
    
    // UI State
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingMessage = null;
    public $activeTab = 'manual';
    public $loading = false;
    public $error = '';
    public $success = '';
    
    // Data
    public $templates = [];
    public $recentContacts = [];
    public $search = '';
    public $status = '';
    public $perPage = 10;

    protected $rules = [
        'recipient' => 'required|string|min:7|max:20',
        'message' => 'required|string|max:1000',
        'scheduledDate' => 'required|date|after_or_equal:today',
        'scheduledTime' => 'required|date_format:H:i',
        'templateVariables.*' => 'nullable|string|max:255'
    ];

    protected $messages = [
        'recipient.required' => 'El número de teléfono es requerido',
        'recipient.string' => 'El número debe ser texto',
        'message.required' => 'El mensaje es requerido',
        'message.max' => 'El mensaje no puede exceder 1000 caracteres',
        'scheduledDate.required' => 'La fecha es requerida',
        'scheduledDate.after_or_equal' => 'La fecha debe ser hoy o en el futuro',
        'scheduledTime.required' => 'La hora es requerida',
        'scheduledTime.date_format' => 'Formato de hora inválido'
    ];

    public function mount()
    {
        $this->loadTemplates();
        $this->loadRecentContacts();
        $this->scheduledDate = now()->format('Y-m-d');
        $this->scheduledTime = now()->addHour()->format('H:i');
    }

    public function loadTemplates()
    {
        try {
            $this->templates = WhatsAppTemplate::where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading templates: ' . $e->getMessage());
            $this->templates = collect();
        }
    }

    public function loadRecentContacts()
    {
        try {
            $this->recentContacts = Student::select('id', 'nombre', 'apellido', 'telefono')
                ->whereNotNull('telefono')
                ->where('telefono', '!=', '')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading recent contacts: ' . $e->getMessage());
            $this->recentContacts = collect();
        }
    }

    public function updatedSelectedTemplate($templateId)
    {
        if ($templateId) {
            try {
                $template = WhatsAppTemplate::find($templateId);
                if ($template && $template->variables) {
                    $this->templateVariables = array_fill_keys($template->variables, '');
                }
            } catch (\Exception $e) {
                Log::error('Error updating template: ' . $e->getMessage());
            }
        } else {
            $this->templateVariables = [];
        }
    }

    public function selectRecentContact($studentId)
    {
        try {
            $student = Student::find($studentId);
            if ($student && $student->telefono) {
                $this->recipient = preg_replace('/[^0-9]/', '', $student->telefono);
            }
        } catch (\Exception $e) {
            Log::error('Error selecting recent contact: ' . $e->getMessage());
        }
    }

    public function processTemplateMessage()
    {
        if (!$this->selectedTemplate) {
            return $this->message;
        }

        try {
            $template = WhatsAppTemplate::find($this->selectedTemplate);
            if (!$template) {
                return '';
            }

            $content = $template->content;
            foreach ($this->templateVariables as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }

            return $content;
        } catch (\Exception $e) {
            Log::error('Error processing template: ' . $e->getMessage());
            return '';
        }
    }

    public function formatPhoneNumber($number)
    {
        try {
            $service = \App\Services\WhatsAppService::forCompany(auth()->user()->empresa_id);
            $formatted = $service->formatPhone($number);
            if (!$formatted) {
                throw new \Exception('Número inválido');
            }
            return $formatted;
        } catch (\Throwable $e) {
            $digits = preg_replace('/\D+/', '', $number);
            if (!$digits) {
                throw new \Exception('Número inválido');
            }
            return ltrim($digits, '+');
        }
    }

    public function createScheduledMessage()
    {
        $this->validate();

        try {
            $this->loading = true;
            
            // Format phone number
            $formattedNumber = $this->formatPhoneNumber($this->recipient);
            
            // Combine date and time
            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $this->scheduledDate . ' ' . $this->scheduledTime, $this->timezone);
            
            // Process message based on active tab
            if ($this->activeTab === 'template' && $this->selectedTemplate) {
                $message = $this->processTemplateMessage();
                $templateId = $this->selectedTemplate;
            } else {
                $message = $this->message;
                $templateId = null;
            }

            // Create scheduled message
            $scheduledMessage = WhatsAppScheduledMessage::create([
                'recipient' => $formattedNumber,
                'message' => $message,
                'template_id' => $templateId,
                'template_variables' => $this->activeTab === 'template' ? $this->templateVariables : null,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'retry_count' => 0
            ]);

            $this->loading = false;
            $this->success = 'Mensaje programado exitosamente';
            $this->resetForm();
            $this->showCreateModal = false;
            
        } catch (\Exception $e) {
            $this->loading = false;
            $this->error = 'Error al programar mensaje: ' . $e->getMessage();
            Log::error('Error creating scheduled message: ' . $e->getMessage());
        }
    }

    public function editMessage($messageId)
    {
        try {
            $message = WhatsAppScheduledMessage::findOrFail($messageId);
            
            if ($message->status !== 'pending') {
                $this->error = 'Solo se pueden editar mensajes pendientes';
                return;
            }

            $this->editingMessage = $message;
            $this->recipient = $message->recipient;
            $this->message = $message->message;
            $this->selectedTemplate = $message->template_id;
            $this->templateVariables = $message->template_variables ?? [];
            $this->scheduledDate = $message->scheduled_at->format('Y-m-d');
            $this->scheduledTime = $message->scheduled_at->format('H:i');
            $this->activeTab = $message->template_id ? 'template' : 'manual';
            $this->showEditModal = true;
            
        } catch (\Exception $e) {
            $this->error = 'Error al cargar mensaje: ' . $e->getMessage();
            Log::error('Error editing message: ' . $e->getMessage());
        }
    }

    public function updateScheduledMessage()
    {
        $this->validate();

        try {
            $this->loading = true;
            
            if (!$this->editingMessage || $this->editingMessage->status !== 'pending') {
                throw new \Exception('No se puede actualizar este mensaje');
            }
            
            // Format phone number
            $formattedNumber = $this->formatPhoneNumber($this->recipient);
            
            // Combine date and time
            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $this->scheduledDate . ' ' . $this->scheduledTime, $this->timezone);
            
            // Process message based on active tab
            if ($this->activeTab === 'template' && $this->selectedTemplate) {
                $message = $this->processTemplateMessage();
                $templateId = $this->selectedTemplate;
            } else {
                $message = $this->message;
                $templateId = null;
            }

            // Update scheduled message
            $this->editingMessage->update([
                'recipient' => $formattedNumber,
                'message' => $message,
                'template_id' => $templateId,
                'template_variables' => $this->activeTab === 'template' ? $this->templateVariables : null,
                'scheduled_at' => $scheduledAt
            ]);

            $this->loading = false;
            $this->success = 'Mensaje actualizado exitosamente';
            $this->resetForm();
            $this->showEditModal = false;
            $this->editingMessage = null;
            
        } catch (\Exception $e) {
            $this->loading = false;
            $this->error = 'Error al actualizar mensaje: ' . $e->getMessage();
            Log::error('Error updating scheduled message: ' . $e->getMessage());
        }
    }

    public function cancelMessage($messageId)
    {
        try {
            $message = WhatsAppScheduledMessage::findOrFail($messageId);
            
            if ($message->status === 'pending') {
                $message->update(['status' => 'cancelled']);
                $this->success = 'Mensaje cancelado exitosamente';
            } else {
                $this->error = 'Solo se pueden cancelar mensajes pendientes';
            }
            
        } catch (\Exception $e) {
            $this->error = 'Error al cancelar mensaje: ' . $e->getMessage();
            Log::error('Error canceling message: ' . $e->getMessage());
        }
    }

    public function deleteMessage($messageId)
    {
        try {
            $message = WhatsAppScheduledMessage::findOrFail($messageId);
            
            if (in_array($message->status, ['pending', 'cancelled', 'failed'])) {
                $message->delete();
                $this->success = 'Mensaje eliminado exitosamente';
            } else {
                $this->error = 'No se puede eliminar un mensaje en estado ' . $message->status;
            }
            
        } catch (\Exception $e) {
            $this->error = 'Error al eliminar mensaje: ' . $e->getMessage();
            Log::error('Error deleting message: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->recipient = '';
        $this->message = '';
        $this->selectedTemplate = '';
        $this->templateVariables = [];
        $this->scheduledDate = now()->format('Y-m-d');
        $this->scheduledTime = now()->addHour()->format('H:i');
        $this->activeTab = 'manual';
        $this->error = '';
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WhatsAppScheduledMessage::with(['template', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('recipient', 'like', '%' . $this->search . '%')
                          ->orWhere('message', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->orderBy('scheduled_at', 'desc');

        $messages = $query->paginate($this->perPage);

        $statistics = [
            'total' => WhatsAppScheduledMessage::count(),
            'pending' => WhatsAppScheduledMessage::where('status', 'pending')->count(),
            'sent' => WhatsAppScheduledMessage::where('status', 'sent')->count(),
            'failed' => WhatsAppScheduledMessage::where('status', 'failed')->count(),
            'cancelled' => WhatsAppScheduledMessage::where('status', 'cancelled')->count(),
            'today' => WhatsAppScheduledMessage::whereDate('scheduled_at', today())->count()
        ];

        return view('livewire.admin.whatsapp.whatsapp-scheduled-messages', [
            'messages' => $messages,
            'statistics' => $statistics
        ]);
    }
}
