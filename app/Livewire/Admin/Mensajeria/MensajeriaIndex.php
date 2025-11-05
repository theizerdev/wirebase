<?php

namespace App\Livewire\Admin\Mensajeria;

use App\Models\Mensaje;
use App\Models\User;
use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MensajeriaIndex extends Component
{
    use HasDynamicLayout;


    use WithPagination;

    public $selectedMessage = null;
    public $activeTab = 'inbox';
    public $search = '';
    public $selectedDestinatarios = [];
    public $nuevoAsunto = '';
    public $nuevoContenido = '';
    public $nuevoPrioridad = 'media';
    public $adjuntos = [];

    protected $rules = [
        'selectedDestinatarios' => 'required|array|min:1',
        'selectedDestinatarios.*' => 'exists:users,id',
        'nuevoAsunto' => 'required|string|max:255',
        'nuevoContenido' => 'required|string',
        'nuevoPrioridad' => 'required|in:baja,media,alta,urgente',
    ];

    public function mount()
    {
        $this->resetearFormulario();
    }

    public $mostrarModalNuevo = false;

    public function abrirModalNuevo()
    {
        $this->mostrarModalNuevo = true;
        $this->dispatch('abrirModalNuevo');
    }

    public function cerrarModalNuevo()
    {
        $this->mostrarModalNuevo = false;
        $this->resetearFormulario();
        // No dispatch event here - it's already handled by the component
    }

    public function resetearFormulario()
    {
        $this->selectedDestinatarios = [];
        $this->nuevoAsunto = '';
        $this->nuevoContenido = '';
        $this->nuevoPrioridad = 'media';
        $this->adjuntos = [];
    }

    public function cambiarTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedMessage = null;
        $this->resetPage();
    }

    public function seleccionarMensaje($mensajeId)
    {
        $this->selectedMessage = Mensaje::findOrFail($mensajeId);

        // Marcar como leído si es destinatario
        if ($this->selectedMessage->esDestinatario(auth()->id())) {
            $this->selectedMessage->marcarComoLeido(auth()->id());
        }
    }

    public function enviarMensaje()
    {
        $this->validate();

        DB::transaction(function () {
            $mensaje = Mensaje::create([
                'remitente_id' => auth()->id(),
                'asunto' => $this->nuevoAsunto,
                'contenido' => $this->nuevoContenido,
                'prioridad' => $this->nuevoPrioridad,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
            ]);

            // Adjuntar destinatarios
            $mensaje->destinatarios()->attach($this->selectedDestinatarios);

            // Aquí se procesarían los adjuntos si los hay
            // $this->procesarAdjuntos($mensaje);
        });

        $this->resetearFormulario();
        $this->mostrarModalNuevo = false;
        session()->flash('message', 'Mensaje enviado exitosamente.');
    }

    public function marcarComoLeido($mensajeId)
    {
        $mensaje = Mensaje::findOrFail($mensajeId);
        if ($mensaje->esDestinatario(auth()->id())) {
            $mensaje->marcarComoLeido(auth()->id());
        }
    }

    public function archivarMensaje($mensajeId)
    {
        $mensaje = Mensaje::findOrFail($mensajeId);
        if ($mensaje->esDestinatario(auth()->id())) {
            $mensaje->destinatarios()->updateExistingPivot(auth()->id(), [
                'archivado' => true,
                'archivado_en' => now(),
            ]);
        }
    }

    public function getMensajesProperty()
    {
        $query = null;

        switch ($this->activeTab) {
            case 'inbox':
                $query = auth()->user()->mensajesRecibidos()
                    ->where('mensaje_destinatarios.archivado', false);
                break;
            case 'sent':
                $query = auth()->user()->mensajesEnviados();
                break;
            case 'archived':
                $query = auth()->user()->mensajesRecibidos()
                    ->where('mensaje_destinatarios.archivado', true);
                break;
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('asunto', 'like', '%' . $this->search . '%')
                  ->orWhere('contenido', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(10);
    }

    public function getUsuariosProperty()
    {
        return User::where('id', '!=', auth()->id())
            ->forUser()
            ->get();
    }

    public function getNoLeidosCountProperty()
    {
        return auth()->user()->mensajesRecibidos()
            ->where('mensaje_destinatarios.leido', false)
            ->count();
    }

    protected $listeners = [
        'abrirModalNuevo' => 'abrirModalNuevo',
        'cerrarModalNuevo' => 'cerrarModalNuevo',
    ];

    public function render()
    {
        return view('livewire.admin.mensajeria.mensajeria-index')->layout($this->getLayout());
    }
}



