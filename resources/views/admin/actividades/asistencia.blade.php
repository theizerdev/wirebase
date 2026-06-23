@extends('components.layouts.admin')
@section('title', 'Asistencia')
@section('content')

    <div id="react-asistencia-root"></div>

@endsection

@push('styles')
    <!-- RemixIcon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
@endpush

@push('scripts')
    @viteReactRefresh
    @vite('resources/js/react-asistencia/index.jsx')
@endpush
