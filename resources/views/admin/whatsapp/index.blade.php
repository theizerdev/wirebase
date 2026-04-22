@extends('layouts.admin')

@section('title', 'WhatsApp - Panel de Control')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fab fa-whatsapp text-success"></i>
                    WhatsApp - Panel de Control
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item active">WhatsApp</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="whatsappTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="conexion-tab" data-bs-toggle="tab" data-bs-target="#conexion" type="button" role="tab">
                <i class="fas fa-plug"></i> Conexión
            </button>
        </li>
       
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="whatsappTabContent">
        <!-- Dashboard Tab -->
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
            <livewire:admin.whatsapp.dashboard />
        </div>

        <!-- Conexión Tab -->
        <div class="tab-pane fade" id="conexion" role="tabpanel">
            <livewire:admin.whatsapp.conexion />
        </div>

        <!-- Mensajes Tab -->
        <div class="tab-pane fade" id="mensajes" role="tabpanel">
            <livewire:admin.whatsapp.envio-mensajes />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-switch to connection tab if disconnected
    document.addEventListener('livewire:init', () => {
        Livewire.on('dashboard-refreshed', (event) => {
            // Puedes agregar lógica adicional aquí si es necesario
        });
    });
</script>
@endpush