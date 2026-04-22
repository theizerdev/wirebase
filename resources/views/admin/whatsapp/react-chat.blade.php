@extends('layouts.app')

@section('content')
<div id="whatsapp-root" class="h-screen"></div>

<script>
    window.__USER__ = {
        name: '{{ auth()->user()->name }}',
        email: '{{ auth()->user()->email }}',
        initials: '{{ substr(auth()->user()->name, 0, 1) }}'
    };
</script>

@push('scripts')
    @vite(['resources/js/whatsapp/main.jsx'])
@endpush
@endsection
