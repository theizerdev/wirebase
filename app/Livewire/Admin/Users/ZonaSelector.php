<?php

namespace App\Livewire\Admin\Users;

use App\Models\Zona;
use App\Models\User;
use Livewire\Component;

class ZonaSelector extends Component
{
    public User $user;
    public array $selectedZonas = [];
    public array $zonasDisponibles = [];
    
    /**
     * Montar el componente con el usuario y sus zonas actuales.
     */
    public function mount(User $user)
    {
        $this->user = $user;
        $this->loadZonas();
    }
    
    /**
     * Cargar las zonas disponibles según la empresa/sucursal del usuario.
     */
    public function loadZonas()
    {
        // Obtener zonas de la empresa del usuario
        $query = Zona::query()
            ->where('empresa_id', $this->user->empresa_id)
            ->activas()
            ->orderBy('nombre');
        
        // Si el usuario tiene sucursal, filtrar también por sucursal
        if ($this->user->sucursal_id) {
            $query->where(function($q) {
                $q->where('sucursal_id', $this->user->sucursal_id)
                  ->orWhereNull('sucursal_id'); // Incluir zonas globales de la empresa
            });
        }
        
        $this->zonasDisponibles = $query->get()->toArray();
        
        // Cargar zonas actualmente asignadas al usuario
        $this->selectedZonas = $this->user->zonas()->pluck('zonas.id')->toArray();
    }
    
    /**
     * Guardar las zonas seleccionadas para el usuario.
     */
    public function saveZonas()
    {
        // Sincronizar las zonas seleccionadas
        $this->user->zonas()->sync($this->selectedZonas);
        
        session()->flash('message', 'Zonas actualizadas correctamente.');
        
        // Recargar las zonas
        $this->loadZonas();
        
        // Disparar evento para notificar el cambio
        $this->dispatch('zonas-updated', userId: $this->user->id);
    }
    
    /**
     * Renderizar el componente.
     */
    public function render()
    {
        return view('livewire.admin.users.zona-selector')
            ->layout('layouts.app');
    }
}
