<?php

use Illuminate\Support\Facades\Route;

// Chat Interno
Route::get('/chat-interno', \App\Livewire\Admin\Chat\ChatInterno::class)->name('chat-interno.index')->middleware('checkAdminPermission:access chat interno');
