<?php

namespace App\Livewire\Admin\TemplateCustomization;

use App\Models\TemplateCustomization;
use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Index extends Component
{
    use HasDynamicLayout;

    public $settings;
    public $primary_color;
    public $skin;
    public $theme;
    public $semi_dark;
    public $content_layout;
    public $header_type;
    public $menu_collapsed;
    public $navbar_type;
    public $text_direction;
    public $footer_fixed;
    public $dropdown_on_hover;
    public $layout_type;

    public function mount()
    {
        $this->settings = TemplateCustomization::getSettings();
        $this->fill($this->settings->toArray());
    }

    public function save()
    {
        $this->settings->update([
            'primary_color' => $this->primary_color,
            'skin' => $this->skin,
            'theme' => $this->theme,
            'semi_dark' => $this->semi_dark,
            'content_layout' => $this->content_layout,
            'header_type' => $this->header_type,
            'menu_collapsed' => $this->menu_collapsed,
            'navbar_type' => $this->navbar_type,
            'text_direction' => $this->text_direction,
            'footer_fixed' => $this->footer_fixed,
            'dropdown_on_hover' => $this->dropdown_on_hover,
            'layout_type' => $this->layout_type,
        ]);

        $this->dispatch('template-updated', [
            'settings' => $this->settings->toJsConfig()
        ]);
        session()->flash('message', 'Configuración guardada exitosamente');
    }

    public function resetToDefaults()
    {
        $this->primary_color = '#7367F0';
        $this->skin = 0;
        $this->theme = 'light';
        $this->semi_dark = false;
        $this->content_layout = 'compact';
        $this->header_type = 'static';
        $this->menu_collapsed = false;
        $this->navbar_type = 'sticky';
        $this->text_direction = 'ltr';
        $this->footer_fixed = false;
        $this->dropdown_on_hover = false;
        $this->layout_type = 'vertical';
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.template-customization.index', [], [
            'title' => 'Personalización de Plantilla',
            'description' => 'Configurar apariencia del sistema',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.template-customization.index' => 'Personalización'
            ]
        ]);
    }
}
