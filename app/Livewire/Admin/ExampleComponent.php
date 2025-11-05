<?php

namespace App\Livewire\Admin;

use App\Traits\HasDynamicLayout;
use Livewire\Component;

/**
 * EJEMPLO: Componente actualizado con HasDynamicLayout
 *
 * Este archivo muestra cómo debería verse un componente
 * que utiliza el trait HasDynamicLayout para layouts dinámicos
 */

class ExampleComponent extends Component
{
    use HasDynamicLayout;

    public $title = 'Ejemplo de Componente';

    public function mount()
    {
        // Verificar permisos
        if (!auth()->user()->can('view example')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        // Opción 1: Usar el método renderWithLayout (recomendado)
        return $this->renderWithLayout('livewire.admin.example', [
            'data' => 'Datos del componente'
        ], [
            'title' => $this->title,
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.example' => 'Ejemplo'
            ]
        ]);

        /*
        // Opción 2: Usar layout dinámico directamente
        return $this->renderWithLayout('livewire.admin.example', [
            'data' => 'Datos del componente'
        ], [
            'description' => 'Gestión de ',
        ]);
        */
    }

    /**
     * Personalizar el título de la página (opcional)
     */
    protected function getPageTitle(): string
    {
        return 'Título Personalizado';
    }

    /**
     * Personalizar el breadcrumb (opcional)
     */
    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.example' => 'Ejemplo Personalizado'
        ];
    }
}
