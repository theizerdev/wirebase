<?php

namespace App\Http\Middleware;

use App\Models\TemplateCustomization;
use Closure;
use Illuminate\Http\Request;

class ApplyTemplateLayout
{
    public function handle(Request $request, Closure $next)
    {
        $settings = TemplateCustomization::getSettings();
        
        // Determinar el layout basado en la configuración
        if ($settings->layout_type === 'horizontal') {
            view()->share('layoutType', 'horizontal');
        } else {
            view()->share('layoutType', 'vertical');
        }
        
        view()->share('templateSettings', $settings);
        
        return $next($request);
    }
}