@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Casas de Alimentación</h1>
        <p><a href="{{ route('admin.casas_alimentacion.create') }}" class="btn btn-primary">Crear Casa de Alimentación</a></p>
        <p>Listado y edición se implementarán con Livewire próximamente.</p>
    </div>
@endsection
