<?php

namespace App\View\Components;

use App\Models\TemplateCustomization;
use Illuminate\View\Component;

class DynamicLayout extends Component
{
    public $layoutType;
    public $templateSettings;

    public function __construct()
    {
        $this->templateSettings = TemplateCustomization::getSettings();
        $this->layoutType = $this->templateSettings->layout_type;
    }

    public function render()
    {
        $layout = $this->layoutType === 'horizontal' 
            ? 'components.layouts.horizontal' 
            : 'components.layouts.admin';
            
        return view($layout);
    }
}