<?php

use Illuminate\Support\Facades\Route;

// Personalización de Plantillas
Route::get('/template-customization', \App\Livewire\Admin\TemplateCustomization\Index::class)
    ->middleware(['auth'])
    ->name('template-customization');
