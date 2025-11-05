<?php

namespace App\Traits;

use App\Models\TemplateCustomization;
use Illuminate\Support\Facades\Log;

trait HasDynamicLayout
{
    /**
     * Obtiene el layout dinámico basado en la configuración de plantilla
     *
     * @return string
     */
    protected function getLayout(): string
    {
        try {
            $settings = TemplateCustomization::getSettings();

            // Validar que el layout_type existe y es válido
            if (!isset($settings->layout_type) || !in_array($settings->layout_type, ['horizontal', 'vertical'])) {
                Log::warning('Layout type inválido o no definido, usando layout admin por defecto');
                return 'components.layouts.admin';
            }

            return $settings->layout_type === 'horizontal'
                ? 'components.layouts.horizontal'
                : 'components.layouts.admin';

        } catch (\Exception $e) {
            // Si hay algún error, usar el layout admin por defecto y registrar el error
            Log::error('Error al obtener layout dinámico: ' . $e->getMessage());
            return 'components.layouts.admin';
        }
    }

    /**
     * Método auxiliar para renderizar con layout dinámico y datos comunes
     *
     * @param string $view
     * @param array $data
     * @param array $layoutData
     * @return \Illuminate\View\View
     */
    protected function renderWithLayout(string $view, array $data = [], array $layoutData = [])
    {
        $defaultLayoutData = [
            'title' => $this->getPageTitle(),
            'breadcrumb' => $this->getBreadcrumb(),
        ];

        $layoutData = array_merge($defaultLayoutData, $layoutData);

        return view($view, $data)->layout($this->getLayout(), $layoutData);
    }

    /**
     * Obtiene el título de la página (puede ser sobrescrito en los componentes)
     *
     * @return string
     */
    protected function getPageTitle(): string
    {
        // Por defecto, usar el nombre de la clase sin namespace
        $className = class_basename(get_class($this));
        return __(strtolower($className));
    }

    /**
     * Obtiene el breadcrumb (puede ser sobrescrito en los componentes)
     *
     * @return array
     */
    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
        ];
    }
}
