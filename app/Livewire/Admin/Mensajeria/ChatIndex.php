<?php

namespace App\Livewire\Admin\Mensajeria;

use Livewire\Component;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatIndex extends Component
{
    public $conversacionActiva = null;
    public $mensajes = [];
    public $nuevoMensaje = '';
    public $busqueda = '';

    public function mount()
    {
        $primeraConversacion = $this->getConversacionesProperty()->first();
        if ($primeraConversacion) {
            $this->conversacionActiva = $primeraConversacion->id;
            $this->cargarMensajes();
        }
    }

    public function seleccionarChat($userId)
    {
        $this->conversacionActiva = $userId;
        $this->cargarMensajes();
        $this->marcarComoLeido();
    }

    public function cargarMensajes()
    {
        if (!$this->conversacionActiva) return;

        $userId = Auth::id();
        
        $this->mensajes = Mensaje::where('empresa_id', Auth::user()->empresa_id)
            ->where(function($q) use ($userId) {
                $q->where('remitente_id', $userId)
                  ->whereHas('destinatarios', function($sq) {
                      $sq->where('user_id', $this->conversacionActiva);
                  });
            })
            ->orWhere(function($q) use ($userId) {
                $q->where('remitente_id', $this->conversacionActiva)
                  ->whereHas('destinatarios', function($sq) use ($userId) {
                      $sq->where('user_id', $userId);
                  });
            })
            ->with('remitente')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function enviarMensaje()
    {
        if (empty($this->nuevoMensaje) || !$this->conversacionActiva) return;

        $mensaje = Mensaje::create([
            'remitente_id' => Auth::id(),
            'asunto' => 'Chat',
            'contenido' => $this->nuevoMensaje,
            'empresa_id' => Auth::user()->empresa_id,
            'sucursal_id' => Auth::user()->sucursal_id,
        ]);

        $mensaje->destinatarios()->attach($this->conversacionActiva);

        $this->nuevoMensaje = '';
        $this->cargarMensajes();
    }

    public function marcarComoLeido()
    {
        $mensajes = Mensaje::where('remitente_id', $this->conversacionActiva)
            ->whereHas('destinatarios', function($q) {
                $q->where('user_id', Auth::id())
                  ->where('leido', false);
            })
            ->get();

        foreach ($mensajes as $mensaje) {
            $mensaje->marcarComoLeido(Auth::id());
        }
    }

    public function getConversacionesProperty()
    {
        $userId = Auth::id();
        $empresaId = Auth::user()->empresa_id;

        return User::where('empresa_id', $empresaId)
            ->where('id', '!=', $userId)
            ->whereHas('mensajesRecibidos', function($q) use ($userId) {
                $q->where('remitente_id', $userId);
            })
            ->orWhereHas('mensajesEnviados', function($q) use ($userId, $empresaId) {
                $q->whereHas('destinatarios', function($sq) use ($userId) {
                    $sq->where('user_id', $userId);
                })->where('empresa_id', $empresaId);
            })
            ->withCount(['mensajesRecibidos as no_leidos' => function($q) use ($userId) {
                $q->whereHas('destinatarios', function($sq) use ($userId) {
                    $sq->where('user_id', $userId)
                       ->where('leido', false);
                });
            }])
            ->get();
    }

    public function getUsuarioActivoProperty()
    {
        return $this->conversacionActiva ? User::find($this->conversacionActiva) : null;
    }

    public function render()
    {
        return view('livewire.admin.mensajeria.chat-index', [
            'conversaciones' => $this->getConversacionesProperty(),
            'usuarioActivo' => $this->getUsuarioActivoProperty(),
        ])->layout('components.layouts.admin', [
            'title' => 'Mensajería',
            'description' => 'Chat en tiempo real'
        ]);
    }
}
