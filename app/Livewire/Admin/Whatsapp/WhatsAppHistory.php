<?php

namespace App\Livewire\Admin\Whatsapp;

use App\Models\WhatsAppMessage;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];

    protected $rules = [
        'search' => 'nullable|string|max:255',
        'status' => 'nullable|in:pending,sent,delivered,failed,read',
        'dateFrom' => 'nullable|date',
        'dateTo' => 'nullable|date|after_or_equal:dateFrom',
        'perPage' => 'nullable|integer|in:10,25,50,100'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function getMessagesProperty()
    {
        $query = WhatsAppMessage::with(['template', 'user'])
            ->where('empresa_id', auth()->user()->empresa_id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('recipient', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function getStatisticsProperty()
    {
        $baseQuery = WhatsAppMessage::where('empresa_id', auth()->user()->empresa_id);

        return [
            'total' => $baseQuery->count(),
            'sent' => (clone $baseQuery)->where('status', 'sent')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'failed' => (clone $baseQuery)->where('status', 'failed')->count(),
            'read' => (clone $baseQuery)->where('status', 'read')->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week' => (clone $baseQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)->count()
        ];
    }

    public function retryMessage($messageId)
    {
        try {
            $message = WhatsAppMessage::findOrFail($messageId);
            
            // Verificar si el mensaje está en estado failed o pending
            if (in_array($message->status, ['failed', 'pending'])) {
                // Incrementar el contador de reintentos
                $message->increment('retry_count');
                
                // Aquí iría la lógica para reenviar el mensaje
                // Por ejemplo, podrías usar un job o llamar al servicio de WhatsApp
                
                session()->flash('success', 'Mensaje programado para reenvío.');
            } else {
                session()->flash('error', 'Este mensaje no puede ser reenviado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al intentar reenviar: ' . $e->getMessage());
        }
    }

    public function deleteMessage($messageId)
    {
        try {
            $message = WhatsAppMessage::findOrFail($messageId);
            $message->delete();
            session()->flash('success', 'Mensaje eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el mensaje: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.whatsapp-history', [
            'messages' => $this->messages,
            'statistics' => $this->statistics
        ])->extends('components.layouts.admin')->section('content');
    }

    public function getPageTitle()
    {
        return 'Historial WhatsApp';
    }

    public function getBreadcrumbs()
    {
        return [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'WhatsApp', 'url' => route('admin.whatsapp.dashboard')],
            ['title' => 'Historial', 'url' => '']
        ];
    }
}