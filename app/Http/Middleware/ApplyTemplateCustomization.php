<?php

namespace App\Http\Middleware;

use App\Models\TemplateCustomization;
use Closure;
use Illuminate\Http\Request;

class ApplyTemplateCustomization
{
    public function handle(Request $request, Closure $next)
    {
        $settings = TemplateCustomization::getSettings();
        
        view()->share('templateSettings', $settings);
        
        return $next($request);
    }
}